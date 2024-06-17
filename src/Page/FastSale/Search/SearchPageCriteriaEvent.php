<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Page\FastSale\Search;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\PageLoadedEvent;
use Symfony\Component\HttpFoundation\Request;

class SearchPageCriteriaEvent extends PageLoadedEvent
{
    public function __construct(
        protected Criteria $criteria,
        protected SearchPage $page,
        SalesChannelContext $salesChannelContext,
        Request $request,
    ) {
        parent::__construct($salesChannelContext, $request);
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }

    public function getPage(): SearchPage
    {
        return $this->page;
    }
}
