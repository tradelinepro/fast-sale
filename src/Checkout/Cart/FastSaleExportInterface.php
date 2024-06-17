<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Checkout\Cart;

use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

interface FastSaleExportInterface
{
    public function export(SalesChannelContext $context): ?LineItemCollection;
}
