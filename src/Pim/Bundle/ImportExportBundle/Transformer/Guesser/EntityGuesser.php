<?php

namespace Pim\Bundle\ImportExportBundle\Transformer\Guesser;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Pim\Bundle\ImportExportBundle\Transformer\Property\PropertyTransformerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Guesser for entity transformer
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class EntityGuesser implements GuesserInterface
{
    /**
     * @var PropertyTransformerInterface
     */
    protected $transformer;

    /**
     * @var RegistryInterface
     */
    protected $doctrine;
    
    /**
     * Constructor
     *
     * @param PropertyTransformerInterface $transformer
     * @param RegistryInterface $doctrine
     */
    public function __construct(PropertyTransformerInterface $transformer, RegistryInterface $doctrine)
    {
        $this->transformer = $transformer;
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransformerInfo(array $columnInfo, ClassMetadataInfo $metadata)
    {
        if (!$metadata->hasAssociation($columnInfo['propertyPath'])) {
            return;
        }

        $mapping = $metadata->getAssociationMapping($columnInfo['propertyPath']);
        $relatedMapping = $this->doctrine->getManager()->getClassMetadata($mapping['targetEntity']);
        if (!$relatedMapping->hasField('code')) {
            return;
        }
        return array(
            $this->transformer,
            array(
                'class'    => $mapping['targetEntity'],
                'multiple' => (ClassMetadataInfo::MANY_TO_MANY == $mapping['type'])
            )
        );
    }
}
