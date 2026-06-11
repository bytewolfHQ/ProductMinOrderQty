<?php declare(strict_types=1);

namespace ByteWolfHQ\ProductMinOrderQty\Validator;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class MinOrderQtyCartValidator implements CartValidatorInterface
{
    private const CUSTOM_FIELD_NAME = 'bytewolfhq_min_order_qty';

    public function validate(
        Cart $cart,
        ErrorCollection $errors,
        SalesChannelContext $context
    ): void {
        foreach ($cart->getLineItems() as $lineItem) {
            if ($lineItem->getType() !== LineItem::PRODUCT_LINE_ITEM_TYPE) {
                continue;
            }

            $payload = $lineItem->getPayload();
            $minQty = (int) ($payload['customFields'][self::CUSTOM_FIELD_NAME] ?? 0);

            if ($minQty <= 0) {
                continue;
            }

            $currentQty = (int) $lineItem->getQuantity();

            if ($currentQty >= $minQty) {
                $errors->add(new MinOrderQtyError(
                    $lineItem->getLabel() ?? $lineItem->getId(),
                    $minQty,
                    $currentQty
                ));
            }
        }
    }
}