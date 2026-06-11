<?php
declare(strict_types=1);

namespace ByteWolfHQ\ProductMinOrderQty\Validator;

use Shopware\Core\Checkout\Cart\Error\Error;

class MinOrderQtyError extends Error
{
    private const KEY = 'min-order-qty-not-reached';

    public function __construct(
        private readonly string $productName,
        private readonly int $minQty,
        private readonly int $currentQty
    )
    {
        parent::__construct();
    }

    public function getId(): string
    {
        return self::KEY . '-' . $this->productName;
    }

    public function getMessageKey(): string
    {
        return self::KEY;
    }

    public function getLevel(): int
    {
        return self::LEVEL_ERROR;
    }

    public function blockOrder(): bool
    {
        return true;
    }

    public function blockReplace(): bool
    {
        return false;
    }

    public function getParameters(): array
    {
        return [
            'productName' => $this->productName,
            'minQty'      => $this->minQty,
            'currentQty'  => $this->currentQty,
        ];
    }
}