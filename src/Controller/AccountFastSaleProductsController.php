<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Controller;

use Shopware\Core\Framework\Routing\RoutingException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tradelinepro\FastSale\Page\Account\FastSale\Products\FastSaleProductsPageLoader;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class AccountFastSaleProductsController extends StorefrontController
{
    public function __construct(
        private readonly FastSaleProductsPageLoader $pageLoader,
    ) {
    }

    #[Route(path: '/account/fast-sale/products/{type}', name: 'frontend.account.fast-sale.products', options: ['seo' => false], methods: ['GET'], defaults: ['_loginRequired' => true])]
    public function fastSaleProducts(string $type, Request $request, SalesChannelContext $context): Response
    {
        if ($type !== 'search' && $type !== 'import') {
            throw RoutingException::invalidRequestParameter('type');
        }

        return $this->renderStorefront('@Storefront/storefront/page/account/fast-sale/products/index.html.twig', [
            'page' => $this->pageLoader->load($type, $request, $context),
        ]);
    }

    #[Route(path: '/account/fast-sale/products/search', name: 'frontend.account.fast-sale.products.search', options: ['seo' => false], methods: ['GET'], defaults: ['_loginRequired' => true])]
    public function fastSaleProductsSearch(Request $request, SalesChannelContext $context): Response
    {
        return $this->fastSaleProducts('search', $request, $context);
    }

    #[Route(path: '/account/fast-sale/products/import', name: 'frontend.account.fast-sale.products.import', options: ['seo' => false], methods: ['GET'], defaults: ['_loginRequired' => true])]
    public function fastSaleProductsImport(Request $request, SalesChannelContext $context): Response
    {
        return $this->fastSaleProducts('import', $request, $context);
    }
}
