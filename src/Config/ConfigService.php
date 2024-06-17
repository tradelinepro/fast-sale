<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Config;

use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\RequestStack;

class ConfigService implements ConfigServiceInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly SystemConfigService $config,
        private readonly string $configPrefix,
    ) {
    }

    public function __call(string $method, array $args = []): mixed
    {
        if (!$args) {
            $this->processArgs($args);
        }

        return $this->config->get("{$this->configPrefix}.config.$method", ...$args);
    }

    public function __get(string $property): mixed
    {
        $args = [];

        $this->processArgs($args);

        return $this->config->get("{$this->configPrefix}.config.$property", ...$args);
    }

    private function processArgs(array &$args): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return;
        }

        $salesChannelId = $request->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_ID);
        if (!$salesChannelId) {
            return;
        }

        $args = [$salesChannelId];
    }
}
