<?php

namespace Pim\Bundle\CatalogBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Pim\Bundle\CatalogBundle\Manager\ProductManager;
use Pim\Bundle\CatalogBundle\Model\ProductInterface;
use Pim\Bundle\CatalogBundle\Entity\Group;

/**
 * Validator for unique variant group axis values constraint
 *
 * @author    Filips Alpe <filips@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UniqueVariantAxisValidator extends ConstraintValidator
{
    /**
     * @var ProductManager $manager
     */
    protected $manager;

    /**
     * Constructor
     * @param ProductManager $manager
     */
    public function __construct(ProductManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Don't allow having multiple products with same combination of values for axis of the variant group
     *
     * @param object     $entity
     * @param Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        if ($entity instanceof Group and $entity->getType()->isVariant()) {
            $this->validateVariantGroup($entity, $constraint);
        } elseif ($entity instanceof ProductInterface) {
            $this->validateProduct($entity, $constraint);
        }
    }

    /**
     * Validate variant group
     *
     * @param Group      $variantGroup
     * @param Constraint $constraint
     */
    protected function validateVariantGroup(Group $variantGroup, Constraint $constraint)
    {
        $existingCombinations = array();

        foreach ($variantGroup->getProducts() as $product) {
            $values = array();
            foreach ($variantGroup->getAttributes() as $attribute) {
                $code = $attribute->getCode();
                $option = $product->getValue($code) ? (string) $product->getValue($code)->getOption() : '';
                $values[] = sprintf('%s: %s', $code, $option);
            }
            $combination = implode(', ', $values);

            if (in_array($combination, $existingCombinations)) {
                $this->addViolation($constraint, $product->getLabel(), $variantGroup->getLabel(), $combination);
            } else {
                $existingCombinations[] = $combination;
            }
        }
    }

    /**
     * Validate product
     *
     * @param ProductInterface $entity
     * @param Constraint       $constraint
     *
     * @return null
     */
    protected function validateProduct(ProductInterface $entity, Constraint $constraint)
    {
        foreach ($entity->getGroups() as $variantGroup) {
            if ($variantGroup->getType()->isVariant()) {
                $this->validateVariantGroup($entity, $variantGroup, $constraint);
            }
        }
    }

    /**
     * Validate a variant group
     *
     * @param Group $variantGroup
     *
     * @return null
     */
    protected function validateVariantGroup(ProductInterface $entity, Group $variantGroup, Constraint $constraint)
    {
        $criteria = $this->prepareVariantGroupCriterias();
        $repository = $this->manager->getFlexibleRepository();

        $matchingProducts = $repository->findAllForVariantGroup($variantGroup, $criteria);

        $matchingProducts = array_filter(
            $matchingProducts,
            function ($product) use ($entity) {
                return $product->getId() !== $entity->getId();
            }
        );

        if (count($matchingProducts) !== 0) {
            $values = array();
            foreach ($criteria as $item) {
                $values[] = sprintf('%s: %s', $item['attribute']->getCode(), (string) $item['option']);
            }
            $this->addViolation(
                $constraint,
                $entity->getLabel(),
                $variantGroup->getLabel(),
                implode(', ', $values)
            );
        }
    }

    /**
     * Prepare variant group criterias
     *
     * @param Group $variantGroup
     *
     * @return array
     */
    protected function prepareVariantGroupCriterias(Group $variantGroup)
    {
        $criteria = array();
        foreach ($variantGroup->getAttributes() as $attribute) {
            $value = $entity->getValue($attribute->getCode());
            $criteria[] = array(
                'attribute' => $attribute,
                'option'    => $value ? $value->getOption() : null,
            );
        }

        return $criteria;
    }

    /**
     * Add violation to the executioncontext
     *
     * @param Constraint $constraint
     * @param string     $productLabel
     * @param string     $variantLabel
     * @param string     $values
     */
    protected function addViolation(Constraint $constraint, $productLabel, $variantLabel, $values)
    {
        $this->context->addViolation(
            $constraint->message,
            array(
                '%product%'       => $productLabel,
                '%variant group%' => $variantLabel,
                '%values%'        => $values
            )
        );
    }
}
