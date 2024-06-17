<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Subscriber;

use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Storefront\Page\Checkout\Cart\CheckoutCartPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tradelinepro\FastSale\Checkout\Cart\FastSaleExportInterface;
use Tradelinepro\FastSale\Extension\FastSaleExtension;

class CheckoutCartPageLoadedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly FastSaleExportInterface $export,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutCartPageLoadedEvent::class => 'execute',
        ];
    }

    public function execute(CheckoutCartPageLoadedEvent $event): void
    {
        $context = $event->getSalesChannelContext();
        if (!$context->getCustomer()) {
            return;
        }

        $lineItems = $this->export->export($context);
        if (!$lineItems) {
            return;
        }

        $page = $event->getPage();
        $extensionName = FastSaleExtension::EXTENSION_NAME;
        $page->addExtension($extensionName, new ArrayStruct(['showExportButton' => true]));
    }
}
