<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Controller;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tradelinepro\FastSale\Page\FastSale\Search\SearchPageLoader;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class FastSaleSearchController extends StorefrontController
{
    public function __construct(
        private readonly SearchPageLoader $pageLoader,
    ) {
    }

    #[Route(path: '/fast-sale/search', name: 'frontend.fast-sale.search', options: ['seo' => false], defaults: ['XmlHttpRequest' => true, '_loginRequired' => true, '_httpCache' => true], methods: ['GET'])]
    public function fastSaleSearch(Request $request, SalesChannelContext $context): Response
    {
        return $this->renderStorefront('@Storefront/storefront/fast-sale/search.html.twig', [
            'page' => $this->pageLoader->load($request, $context),
        ]);
    }
}
