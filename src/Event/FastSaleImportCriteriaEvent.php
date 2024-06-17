<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Event;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Event\ShopwareSalesChannelEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

class FastSaleImportCriteriaEvent extends Event implements ShopwareSalesChannelEvent
{
    public function __construct(
        protected Criteria $criteria,
        protected SalesChannelContext $salesChannelContext,
    ) {
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }

    public function getContext(): Context
    {
        return $this->getSalesChannelContext()->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }
}
