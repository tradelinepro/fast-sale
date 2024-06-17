<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Page\FastSale\Search;

use Shopware\Core\Content\Product\SalesChannel\Suggest\AbstractProductSuggestRoute;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class SearchPageLoader
{
    public function __construct(
        private readonly GenericPageLoaderInterface $genericLoader,
        private readonly AbstractProductSuggestRoute $route,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function load(Request $request, SalesChannelContext $context): SearchPage
    {
        $page = SearchPage::createFrom($this->genericLoader->load($request, $context));

        $criteria = (new Criteria())
            ->addFilter(new NotFilter(NotFilter::CONNECTION_OR, [
                new EqualsFilter('customFields.isGrouped', true),
                new EqualsFilter('customFields.isMachine', true),
            ]));

        $criteria->setTotalCountMode($criteria::TOTAL_COUNT_MODE_EXACT)
            ->setLimit(10)
            ->setTitle('fast-sale::search');

        $request->query->set('limit', 10);

        $criteria->assign([
            'excludeCustomProductTypes' => true,
        ]);

        $this->eventDispatcher->dispatch(new SearchPageCriteriaEvent($criteria, $page, $context, $request));

        $page->setSearchResult(
            $this->route->load($request, $context, $criteria)
                ->getListingResult()
        );
        $page->setSearchTerm((string) $request->query->get('search'));

        $this->eventDispatcher->dispatch(new SearchPageLoadedEvent($page, $context, $request));

        return $page;
    }
}
