<?php declare(strict_types=1);

namespace ByteWolfHQ\ProductMinOrderQty\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductPageSubscriber implements EventSubscriberInterface {
    private const CUSTOM_FIELD_NAME = 'bytewolfhq_min_order_qty';

    public function __construct(
        private readonly EntityRepository $productRepository
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
        ];
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $product = $event->getPage()->getProduct();

        $customFields = $product->getCustomFields() ?? [];
        $minQty = (int) ($customFields[self::CUSTOM_FIELD_NAME] ?? 0);

        if ($minQty < 0) {
            return;
        }

        // Overwrite current minPurchase if minQty is greater
        $currentMin = $product->getMinPurchase() ?? 1;

        if ($minQty > $currentMin) {
            $product->setMinPurchase($minQty);
        }
    }
}