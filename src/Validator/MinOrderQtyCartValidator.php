<?php declare(strict_types=1);

namespace ByteWolfHQ\ProductMinOrderQty\Validator;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class MinOrderQtyCartValidator implements CartValidatorInterface
{
    public function validate(
        Cart $cart,
        ErrorCollection $errors,
        SalesChannelContext $context
    ): void {
        // TODO: Mindestbestellmenge prüfen, Fehler in $errors pushen
    }
}