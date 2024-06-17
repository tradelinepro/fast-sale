<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Checkout\Cart;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class FastSaleExport implements FastSaleExportInterface
{
    private array $cache = [];

    public function __construct(
        private readonly CartService $cartService,
    ) {
    }

    public function export(SalesChannelContext $context): ?LineItemCollection
    {
        $token = $context->getToken();
        if (isset($this->cache[$token]) || \array_key_exists($token, $this->cache)) {
            return $this->cache[$token];
        }

        $cart = $this->cartService->getCart($token, $context);

        $lineItems = $cart->getLineItems();
        if ($lineItems->count() <= 0) {
            return $this->cache[$token] = null;
        }

        $lineItems = $lineItems->filter(function (LineItem $lineItem) {
            if ($lineItem->getType() === $lineItem::PRODUCT_LINE_ITEM_TYPE) {
                return true;
            }

            if ($lineItem->hasPayloadValue('productNumber')) {
                return true;
            }

            return false;
        });

        if ($lineItems->count() <= 0) {
            return $this->cache[$token] = null;
        }

        return $lineItems;
    }
}
