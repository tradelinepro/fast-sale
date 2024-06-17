<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

class FastSaleImportException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'DMF__FAST_SALE_IMPORT';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
