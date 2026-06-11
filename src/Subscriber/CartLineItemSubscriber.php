<?php declare(strict_types=1);

namespace ByteWolfHQ\ProductMinOrderQty\Subscriber;

use Shopware\Core\Checkout\Cart\Event\CartLineItemAddedEvent;
use Shopware\Core\Content\Product\Cart\ProductCartProcessor;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;

class CartLineItemSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sales_channel.product.loaded' => 'onProductLoaded',
        ];
    }

    public function onProductLoaded(EntityLoadedEvent $event): void
    {
        // This subscriber is the hook if we later need to hook in
    }
}