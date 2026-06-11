<?php
declare(strict_types=1);

namespace ByteWolfHQ\ProductMinOrderQty;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class ProductMinOrderQty extends Plugin
{
    private const string CUSTOM_FIELD_SET_NAME = 'bytewolfhq_product_min_order_qty';
    private const string CUSTOM_FIELD_NAME = 'bytewolfhq_min_order_qty';

    public function install(InstallContext $installContext): void
    {
        $this->createCustomFields($installContext->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }
        $this->removeCustomFields($uninstallContext->getContext());
    }

    private function createCustomFields(Context $context): void
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', self::CUSTOM_FIELD_SET_NAME));

        $exists = $customFieldSetRepository->search($criteria, $context);

        if ($exists->getTotal() > 0) {
            return;
        }

        $customFieldSetRepository->create([
            [
                'name' => self::CUSTOM_FIELD_SET_NAME,
                'config' => [
                    'label' => [
                        'de-DE' => 'Mindestbestellmenge',
                        'en-GB' => 'Minimum Order Quantity',
                    ],
                ],
                'relations' => [
                    ['entityName' => 'product'],
                ],
                'customFields' => [
                    [
                        'name' => self::CUSTOM_FIELD_NAME,
                        'type' => CustomFieldTypes::INT,
                        'config' => [
                            'label' => [
                                'de-DE' => 'Mindestbestellmenge',
                                'en-GB' => 'Minimum Order Quantity',
                            ],
                            'helpText' => [
                                'de-DE' => 'Mindestanzahl an Einheiten, die pro Bestellung gekauft werden müssen. 0 = deaktiviert.',
                                'en-GB' => 'Minimum number of units that must be purchased per order. 0 = disabled.',
                            ],
                            'min' => 0,
                            'componentName' => 'sw-field',
                            'customFieldType' => 'number',
                            'numberType' => 'int',
                        ],
                    ],
                ],
            ],
        ], $context);
    }

    private function removeCustomFields(Context $context)
    {
        /** @var EntityRepository $customFieldSetRepo */
        $customFieldSetRepo = $this->container->get('custom_field_set.repository');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', self::CUSTOM_FIELD_SET_NAME));

        $result = $customFieldSetRepo->searchIds($criteria, $context);

        if ($result->getTotal() === 0) {
            return;
        }

        $ids = array_map(fn (string $id) => ['id' => $id], $result->getIds());
        $customFieldSetRepo->delete($ids, $context);
    }
}