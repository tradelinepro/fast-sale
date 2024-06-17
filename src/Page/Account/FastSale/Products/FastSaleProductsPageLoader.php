<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Page\Account\FastSale\Products;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\GenericPageLoader;
use Shopware\Storefront\Page\Page;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class FastSaleProductsPageLoader
{
    public function __construct(
        private readonly GenericPageLoader $genericLoader,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function load(string $type, Request $request, SalesChannelContext $context): Page
    {
        $page = FastSaleProductsPage::createFrom($this->genericLoader->load($request, $context));

        $page->setType($type);

        $this->eventDispatcher->dispatch(new FastSaleProductsPageLoadedEvent($page, $context, $request));

        return $page;
    }
}
