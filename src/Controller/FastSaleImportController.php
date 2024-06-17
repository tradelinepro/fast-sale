<?php declare(strict_types=1);
/*
 * @author digital.manufaktur GmbH
 * @link   https://www.digitalmanufaktur.com/
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tradelinepro\FastSale\Controller;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Tradelinepro\FastSale\Event\FastSaleImportCriteriaEvent;
use Tradelinepro\FastSale\Exception\FastSaleImportException;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class FastSaleImportController extends StorefrontController
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CartService $cartService,
        private readonly SalesChannelRepository $productRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route(path: '/fast-sale/import', name: 'frontend.fast-sale.import', options: ['seo' => false], defaults: ['_loginRequired' => true], methods: ['POST'])]
    public function fastSaleImport(Cart $cart, Request $request, SalesChannelContext $context): Response
    {
        $file = $request->files->get('file');

        try {
            if (!$file || $file->getSize() <= 0) {
                throw new FastSaleImportException($this->trans('fast-sale.error.fileEmptyOrInvalid'));
            }

            $file = $file->move("{$this->kernel->getProjectDir()}/var/tmp", $file->getClientOriginalName());

            $lines = IOFactory::createReader(IOFactory::identify($file->getRealPath()))
                ->load($file->getRealPath())
                ->getActiveSheet()
                ->toArray(null, false, false, false);

            $lines = \array_values($lines);

            unset($lines[0]);

            if (!$lines) {
                throw new FastSaleImportException($this->trans('fast-sale.error.fileEmptyOrInvalid'));
            }

            $lines = \array_values($lines);

            if (\count($lines[0]) !== 2) {
                throw new FastSaleImportException($this->trans('fast-sale.error.exactColumnsCount'));
            }

            $values = [];
            foreach ($lines as $key => $line) {
                if (!isset($line[0])) {
                    $values[] = ++$key;
                }
            }

            if ($values) {
                throw new FastSaleImportException($this->trans(
                    'fast-sale.error.emptyProductNumbers',
                    ['%values%' => \implode(', ', $values)],
                ));
            }

            $values = [];
            foreach ($lines as $key => $line) {
                if ($line[1] === null) {
                    $values[] = ++$key;
                }
            }

            if ($values) {
                throw new FastSaleImportException($this->trans(
                    'fast-sale.error.emptyQtys',
                    ['%values%' => \implode(', ', $values)],
                ));
            }

            $values = [];
            foreach ($lines as $key => $line) {
                if (!\is_int($line[1])) {
                    $values[] = ++$key;
                }
            }

            if ($values) {
                throw new FastSaleImportException($this->trans(
                    'fast-sale.error.notIntegerQtys',
                    ['%values%' => \implode(', ', $values)],
                ));
            }
            $lines = \array_filter($lines, fn ($v) => $v[1] > 0);
            if (!$lines) {
                throw new FastSaleImportException($this->trans('fast-sale.error.allProductsNotFound'));
            }

            $productNumbers = \array_column($lines, 0);

            $criteria = new Criteria();
            $criteria->addFilter(new OrFilter([
                new EqualsAnyFilter('productNumber', $productNumbers),
                new EqualsAnyFilter('ean', $productNumbers),
            ]))
                ->setLimit(\count($productNumbers) * 2)
                ->setTitle('fast-sale::import');

            $this->eventDispatcher->dispatch(new FastSaleImportCriteriaEvent($criteria, $context));

            $searchResults = $this->productRepository->search($criteria, $context);
            if ($searchResults->count() <= 0) {
                throw new FastSaleImportException($this->trans('fast-sale.error.allProductsNotFound'));
            }

            /** @var SalesChannelProductCollection $productsCollection */
            $productsCollection = $searchResults->getEntities()
                ->filter(fn ($product) => !$product->hasExtension('zeroPrice'));

            if ($productsCollection->count() <= 0) {
                throw new FastSaleImportException($this->trans('fast-sale.error.allProductsNotFound'));
            }

            $products = [];
            $productsWithEan = [];
            foreach ($productsCollection as $product) {
                $products["_{$product->getProductNumber()}"] = $product;
                $productsWithEan["_{$product->getEan()}"] = $product;
            }

            $lines = \array_combine($productNumbers, $lines);
            $lineItems = [];
            $lineItemType = LineItem::PRODUCT_LINE_ITEM_TYPE;
            $count = 0;

            $notFound = [];
            foreach ($lines as $productNumber => $line) {
                if (isset($products["_$productNumber"])) {
                    $product = $products["_$productNumber"];
                } elseif (isset($productsWithEan["_$productNumber"])) {
                    $product = $productsWithEan["_$productNumber"];
                } else {
                    $notFound[] = $productNumber;

                    continue;
                }

                $values = $product->getId();

                $lineItem = new LineItem($values, $lineItemType, $values, (int) $line[1]);

                $lineItem->setStackable(true);
                $lineItem->setRemovable(true);

                $count += $lineItem->getQuantity();

                $lineItems[] = $lineItem;
            }

            if ($lineItems) {
                $cart = $this->cartService->add($cart, $lineItems, $context);

                if (!$this->traceErrors($cart)) {
                    $this->addFlash('success', $this->trans('checkout.addToCartSuccess', ['%count%' => $count]));
                }
            }

            if (\count($notFound) > 0) {
                $this->addFlash('warning', $this->trans(
                    'fast-sale.error.productsNotFound',
                    ['%productNumbers%' => \implode(', ', $notFound)],
                ));
            }
        } catch (\Throwable $e) {
            $this->addFlash('danger', $e->getMessage());

            $this->logger->error($e);
        } finally {
            if ($file) {
                $file = $file->getRealPath();

                @\unlink($file);
            }
        }

        return $this->createActionResponse($request);
    }

    private function traceErrors(Cart $cart): bool
    {
        if ($cart->getErrors()->count() <= 0) {
            return false;
        }

        $this->addCartErrors($cart);

        $cart->getErrors()->clear();

        return true;
    }
}
