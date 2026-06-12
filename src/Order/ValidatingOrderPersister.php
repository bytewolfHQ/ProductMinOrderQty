<?php declare(strict_types=1);

namespace ByteWolfHQ\ProductMinOrderQty\Order;

use ByteWolfHQ\ProductMinOrderQty\Validator\MinOrderQtyError;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\Order\OrderPersisterInterface;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ValidatingOrderPersister implements OrderPersisterInterface
{
    private const CUSTOM_FIELD_NAME = 'bytewolfhq_min_order_qty';

    public function __construct(
        private readonly OrderPersisterInterface $decorated,
        private readonly EntityRepository $productRepository
    ) {
    }

    public function persist(Cart $cart, SalesChannelContext $context): string
    {
        // Validate minimum order quantities before persisting
        $this->validateMinOrderQuantities($cart, $context);

        // If validation passes, delegate to the original persister
        return $this->decorated->persist($cart, $context);
    }

    private function validateMinOrderQuantities(Cart $cart, SalesChannelContext $context): void
    {
        $productIds = [];
        $lineItemsByProductId = [];

        // Collect all product IDs from cart
        foreach ($cart->getLineItems() as $lineItem) {
            if ($lineItem->getType() !== LineItem::PRODUCT_LINE_ITEM_TYPE) {
                continue;
            }

            $productId = $lineItem->getReferencedId();
            if ($productId) {
                $productIds[] = $productId;
                $lineItemsByProductId[$productId] = $lineItem;
            }
        }

        if (empty($productIds)) {
            return;
        }

        // Load products with custom fields
        $criteria = new Criteria($productIds);
        $products = $this->productRepository->search($criteria, $context->getContext());

        /** @var ProductEntity $product */
        foreach ($products as $product) {
            $lineItem = $lineItemsByProductId[$product->getId()];
            $customFields = $product->getTranslation('customFields') ?? [];
            $minQty = (int) ($customFields[self::CUSTOM_FIELD_NAME] ?? 0);

            if ($minQty <= 0) {
                continue;
            }

            $currentQty = (int) $lineItem->getQuantity();

            if ($currentQty < $minQty) {
                $cart->getErrors()->add(new MinOrderQtyError(
                    $lineItem->getLabel() ?? $lineItem->getId(),
                    $minQty,
                    $currentQty
                ));
            }
        }
    }
}
