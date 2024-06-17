<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Page\FastSale\Search;

use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Storefront\Page\Page;

class SearchPage extends Page
{
    protected EntitySearchResult $searchResult;

    protected string $searchTerm;

    public function getSearchResult(): EntitySearchResult
    {
        return $this->searchResult;
    }

    public function setSearchResult(EntitySearchResult $searchResult): void
    {
        $this->searchResult = $searchResult;
    }

    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    public function setSearchTerm(string $searchTerm): void
    {
        $this->searchTerm = $searchTerm;
    }
}
