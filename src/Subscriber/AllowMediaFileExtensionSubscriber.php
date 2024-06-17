<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Subscriber;

use Shopware\Core\Content\Media\Event\MediaFileExtensionWhitelistEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AllowMediaFileExtensionSubscriber implements EventSubscriberInterface
{
    private array $fileExtensions = [
        'csv', 'odt', 'doc', 'docx', 'xls', 'xlsx', 'txt',
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            MediaFileExtensionWhitelistEvent::class => 'addExtensionToWhitelist',
        ];
    }

    public function addExtensionToWhitelist(MediaFileExtensionWhitelistEvent $event): void
    {
        $whitelist = $event->getWhitelist();

        $whitelist = \array_merge($whitelist, $this->fileExtensions);

        $event->setWhitelist($whitelist);
    }
}
