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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tradelinepro\FastSale\Checkout\Cart\FastSaleExportInterface;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class FastSaleExportController extends StorefrontController
{
    public function __construct(
        private readonly FastSaleExportInterface $export,
    ) {
    }

    #[Route(path: '/fast-sale/export', name: 'frontend.fast-sale.export', options: ['seo' => false], defaults: ['_loginRequired' => true], methods: ['GET'])]
    public function fastSaleExport(SalesChannelContext $context): Response
    {
        $lineItems = $this->export->export($context);
        if (!$lineItems) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        $data = "{$this->trans('fast-sale.csv.column1')};{$this->trans('fast-sale.csv.column2')}";

        foreach ($lineItems as $lineItem) {
            $data .= "\n{$lineItem->getPayloadValue('productNumber')};{$lineItem->getQuantity()}";
        }

        $dateTime = (new \DateTime())->format('dmy-H-i-s');

        return new Response($data, Response::HTTP_OK, [
            'Content-Type' => 'text/csv',
            'Content-Length' => \mb_strlen($data),
            'Content-Disposition' => "attachment; filename=fastsale-shopping-cart-$dateTime.csv",
        ]);
    }
}
