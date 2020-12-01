<?php

namespace Akeneo\Pim\Enrichment\Bundle\Product;

use Akeneo\Pim\Enrichment\Component\Product\Query\CountProductModelsWithRemovedAttributeInterface;
use Akeneo\Pim\Enrichment\Component\Product\Query\CountProductsAndProductModelsWithInheritedRemovedAttributeInterface;
use Akeneo\Pim\Enrichment\Component\Product\Query\CountProductsWithRemovedAttributeInterface;
use Akeneo\Pim\Enrichment\Component\Product\Query\GetProductIdentifiersWithRemovedAttributeInterface;
use Akeneo\Pim\Enrichment\Component\Product\Query\GetProductModelIdentifiersWithRemovedAttributeInterface;
use Akeneo\Pim\Enrichment\Component\Product\Repository\ProductModelRepositoryInterface;
use Akeneo\Pim\Enrichment\Component\Product\Repository\ProductRepositoryInterface;
use Akeneo\Pim\Enrichment\Component\Product\ValuesRemover\CleanValuesOfRemovedAttributesInterface;
use Akeneo\Pim\Structure\Component\Query\PublicApi\AttributeType\GetAttributes;
use Akeneo\Tool\Bundle\ConnectorBundle\Doctrine\UnitOfWorkAndRepositoriesClearer;
use Akeneo\Tool\Component\StorageUtils\Saver\BulkSaverInterface;
use Doctrine\DBAL\Connection;
use Akeneo\Tool\Bundle\ElasticsearchBundle\Client;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FastCleanValuesOfRemovedAttributes implements CleanValuesOfRemovedAttributesInterface
{
    private const BATCH_SIZE = 100;

    /** @var CountProductsWithRemovedAttributeInterface */
    private $countProductsWithRemovedAttribute;

    /** @var CountProductModelsWithRemovedAttributeInterface */
    private $countProductModelsWithRemovedAttribute;

    /** @var CountProductsAndProductModelsWithInheritedRemovedAttributeInterface */
    private $countProductsAndProductModelsWithInheritedRemovedAttribute;

    /** @var GetProductIdentifiersWithRemovedAttributeInterface */
    private $getProductIdentifiersWithRemovedAttribute;

    /** @var GetProductModelIdentifiersWithRemovedAttributeInterface */
    private $getProductModelIdentifiersWithRemovedAttribute;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var BulkSaverInterface */
    private $productSaver;

    /** @var ProductModelRepositoryInterface */
    private $productModelRepository;

    /** @var BulkSaverInterface */
    private $productModelSaver;

    /** @var ValidatorInterface */
    private $validator;

    /** @var GetAttributes */
    private $getAttributes;

    /** @var UnitOfWorkAndRepositoriesClearer */
    private $clearer;

    private Connection $connection;

    private Client $productAndProductModelClient;

    public function __construct(
        CountProductsWithRemovedAttributeInterface $countProductsWithRemovedAttribute,
        CountProductModelsWithRemovedAttributeInterface $countProductModelsWithRemovedAttribute,
        CountProductsAndProductModelsWithInheritedRemovedAttributeInterface $countProductsAndProductModelsWithInheritedRemovedAttribute,
        GetProductIdentifiersWithRemovedAttributeInterface $getProductIdentifiersWithRemovedAttribute,
        GetProductModelIdentifiersWithRemovedAttributeInterface $getProductModelIdentifiersWithRemovedAttribute,
        ProductRepositoryInterface $productRepository,
        BulkSaverInterface $productSaver,
        ProductModelRepositoryInterface $productModelRepository,
        BulkSaverInterface $productModelSaver,
        ValidatorInterface $validator,
        GetAttributes $getAttributes,
        UnitOfWorkAndRepositoriesClearer $clearer,
        Connection $connection,
        Client $productAndProductModelClient
    ) {
        $this->countProductsWithRemovedAttribute = $countProductsWithRemovedAttribute;
        $this->countProductModelsWithRemovedAttribute = $countProductModelsWithRemovedAttribute;
        $this->countProductsAndProductModelsWithInheritedRemovedAttribute = $countProductsAndProductModelsWithInheritedRemovedAttribute;
        $this->getProductIdentifiersWithRemovedAttribute = $getProductIdentifiersWithRemovedAttribute;
        $this->getProductModelIdentifiersWithRemovedAttribute = $getProductModelIdentifiersWithRemovedAttribute;
        $this->productRepository = $productRepository;
        $this->productSaver = $productSaver;
        $this->productModelRepository = $productModelRepository;
        $this->productModelSaver = $productModelSaver;
        $this->validator = $validator;
        $this->getAttributes = $getAttributes;
        $this->clearer = $clearer;
        $this->connection = $connection;
        $this->productAndProductModelClient = $productAndProductModelClient;
    }

    public function countProductsWithRemovedAttribute(array $attributesCodes): int
    {
        return $this->countProductsWithRemovedAttribute->count($attributesCodes);
    }

    public function cleanProductsWithRemovedAttribute(array $attributesCodes, ?callable $progress = null): void
    {
        foreach ($attributesCodes as $attributeCode) {
            $removeValues = <<<SQL
            UPDATE `pim_catalog_product`
            SET `pim_catalog_product`.raw_values = JSON_REMOVE(`pim_catalog_product`.raw_values, '$.:attribute_code');
    SQL;

            $this->connection->executeUpdate($removeValues, [':attribute_code' => $attributeCode]);

            // 'query' => [
            //     'terms' => ['categories' => $this->categoryCodesToRemove],
            // ],
            // 'script' => [
            //     // WARNING: "inline" will need to be changed to "source" when we'll switch to Elasticsearch 5.6
            //     'inline' => 'ctx._source.categories.removeAll(params.categories); if (0 == ctx._source.categories.size()) { ctx._source.remove("categories"); }',
            //     'lang'   => 'painless',
            //     'params' => ['categories' => $this->categoryCodesToRemove],
            // ],

            $this->productAndProductModelClient->updateByQuery([
                'query' => [
                    'exists' => ['field' => sprintf('_source.values.%s', $attributeCode)],
                ],
                'script' => [
                    'inline' => 'ctx._source.values.remove(params.attributeCode)',
                    'lang'   => 'painless',
                    'params' => ['attributeCode' => $attributeCode],
                ]
            ]);
        }
        // foreach ($this->getProductIdentifiersWithRemovedAttribute->nextBatch($attributesCodes, self::BATCH_SIZE) as $identifiers) {
        //     $products = $this->productRepository->findBy(['identifier' => $identifiers]);
        //     $this->productSaver->saveAll($products, ['force_save' => true]);

        //     if (null !== $progress) {
        //         $progress(count($products));
        //     }

        //     $this->clearer->clear();
        // }
    }

    public function countProductModelsWithRemovedAttribute(array $attributesCodes): int
    {
        return $this->countProductModelsWithRemovedAttribute->count($attributesCodes);
    }

    public function cleanProductModelsWithRemovedAttribute(array $attributesCodes, ?callable $progress = null): void
    {
        foreach ($attributesCodes as $attributeCode) {
    //         $removeValues = <<<SQL
    //         UPDATE `pim_catalog_product_model`
    //         SET `pim_catalog_product_model`.raw_values = JSON_REMOVE(`pim_catalog_product_model`.raw_values, '$.:attribute_code');
    // SQL;

    //         $this->connection->executeUpdate($removeValues, [':attribute_code' => $attributeCode]);


        }
    }

    public function countProductsAndProductModelsWithInheritedRemovedAttribute(array $attributesCodes): int
    {
        return $this->countProductsAndProductModelsWithInheritedRemovedAttribute->count($attributesCodes);
    }

    public function validateRemovedAttributesCodes(array $attributesCodes): void
    {
        if (empty($attributesCodes)) {
            throw new \LogicException('The given attributes codes should not be empty.');
        }

        foreach ($attributesCodes as $attributeCode) {
            $this->validateAttributeCode($attributeCode);
        }
    }

    private function validateAttributeCode(string $attributeCode): void
    {
        $violations = $this->validator->validate($attributeCode, [
            new Assert\NotBlank(),
            new Assert\Length([
                'max' => 100,
            ]),
            new Assert\Regex('/^[a-zA-Z0-9_]+$/'),
            new Assert\Regex('/^(?!(id|iD|Id|ID|associationTypes|categories|categoryId|completeness|enabled|(?i)\bfamily\b|groups|associations|products|scope|treeId|values|category|parent|label|(.)*_(products|groups)|entity_type|attributes)$)/'),
            new Assert\Regex('/^[^\n]+$/D'),
        ]);

        if (count($violations) > 0) {
            throw new \InvalidArgumentException(sprintf('The attribute code "%s" is not valid.', $attributeCode));
        }

        $attribute = $this->getAttributes->forCode($attributeCode);

        if (null !== $attribute) {
            throw new \InvalidArgumentException(sprintf(
                'The attribute with the code "%s" still exists.',
                $attributeCode
            ));
        }
    }
}
