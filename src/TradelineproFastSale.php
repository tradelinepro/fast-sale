<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale;

use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Plugin;

#[Package('core')]
class TradelineproFastSale extends Plugin
{
    public function executeComposerCommands(): bool
    {
        return true;
    }

    /**
     * Set priority to allow other plugins change templates defined in this plugin
     * Other plugins should have a value greater than declared in the Tradeline core plugin
     */
    public function getTemplatePriority(): int
    {
        return 50;
    }
}
