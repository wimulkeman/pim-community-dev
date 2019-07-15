<?php
declare(strict_types=1);

namespace Pim\Bundle\CatalogBundle\EventSubscriber\AttributeOption;

use Akeneo\Component\StorageUtils\StorageEvents;
use Pim\Bundle\CatalogBundle\Elasticsearch\ProductAndProductModelQueryBuilderFactory;
use Pim\Component\Catalog\FamilyVariant\Query\FamilyVariantsByAttributeAxesInterface;
use Pim\Component\Catalog\Model\AttributeOptionInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Subscribe to remove event on attribute option
 *
 * @author    Willy Mesnage <willy.mesnage@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeOptionRemovalSubscriber implements EventSubscriberInterface
{
    /** @var FamilyVariantsByAttributeAxesInterface */
    protected $familyVariantsByAttributeAxes;

    /** @var ProductAndProductModelQueryBuilderFactory */
    protected $pqbFactory;

    /**
     * @param FamilyVariantsByAttributeAxesInterface    $familyVariantsByAttributeAxes
     * @param ProductAndProductModelQueryBuilderFactory $pqbFactory
     */
    public function __construct(
        FamilyVariantsByAttributeAxesInterface $familyVariantsByAttributeAxes,
        ProductAndProductModelQueryBuilderFactory $pqbFactory
    ) {
        $this->familyVariantsByAttributeAxes = $familyVariantsByAttributeAxes;
        $this->pqbFactory = $pqbFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            StorageEvents::PRE_REMOVE => 'disallowRemovalIfOptionIsUsedAsAttributeAxes'
        ];
    }

    /**
     * @param GenericEvent $event
     */
    public function disallowRemovalIfOptionIsUsedAsAttributeAxes(GenericEvent $event): void
    {
        $attributeOption = $event->getSubject();
        if (!$attributeOption instanceof AttributeOptionInterface) {
            return;
        }

        $attributeCode = $attributeOption->getAttribute()->getCode();
        $familyVariantsIdentifiers = $this->familyVariantsByAttributeAxes->findIdentifiers([$attributeCode]);

        if (empty($familyVariantsIdentifiers)) {
            return;
        }

        if ($this->thereAreEntitiesCurrentlyUsingThisOptionAsAxes($attributeOption, $familyVariantsIdentifiers)) {
            throw new \LogicException(sprintf(
                'Attribute option "%s" could not be removed as it is used as variant axis value.',
                $attributeOption->getCode()
            ));
        }
    }

    /**
     * @param AttributeOptionInterface $attributeOption
     * @param array $familyVariantsIdentifier
     *
     * @return bool
     */
    protected function thereAreEntitiesCurrentlyUsingThisOptionAsAxes(
        AttributeOptionInterface $attributeOption,
        array $familyVariantsIdentifier
    ): bool {
        $pqb = $this->pqbFactory->create([
            'filters' => [
                [
                    'field' => 'family_variant',
                    'operator' => Operators::IN_LIST,
                    'value' => $familyVariantsIdentifier,
                ],
                [
                    'field' => $attributeOption->getAttribute()->getCode(),
                    'operator' => Operators::IN_LIST,
                    'value' => [$attributeOption->getCode()],
                ]
            ]
        ]);

        return 0 !== $pqb->execute()->count();
    }
}