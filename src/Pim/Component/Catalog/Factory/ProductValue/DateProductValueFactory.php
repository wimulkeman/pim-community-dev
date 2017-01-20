<?php

namespace Pim\Component\Catalog\Factory\ProductValue;

use Akeneo\Component\StorageUtils\Exception\InvalidPropertyException;
use Pim\Component\Catalog\Exception\InvalidArgumentException;
use Pim\Component\Catalog\Model\AttributeInterface;

/**
 * Factory that creates date product values.
 *
 * @internal  Please, do not use this class directly. You must use \Pim\Component\Catalog\Factory\ProductValueFactory.
 *
 * @author    Damien Carcel (damien.carcel@akeneo.com)
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class DateProductValueFactory implements ProductValueFactoryInterface
{
    /** @var string */
    protected $dateProductValueClass;

    /** @var string */
    protected $supportedAttributeType;

    /**
     * @param string $dateProductValueClass
     * @param string $supportedAttributeType
     */
    public function __construct($dateProductValueClass, $supportedAttributeType)
    {
        if (!class_exists($dateProductValueClass)) {
            throw new \InvalidArgumentException(
                sprintf('The product value class "%s" does not exist.', $dateProductValueClass)
            );
        }

        $this->dateProductValueClass = $dateProductValueClass;
        $this->supportedAttributeType = $supportedAttributeType;
    }

    /**
     * @inheritdoc
     */
    public function create(AttributeInterface $attribute, $channelCode, $localeCode, $data)
    {
        $this->checkData($attribute, $data);

        $value = new $this->dateProductValueClass();
        $value->setAttribute($attribute);
        $value->setScope($channelCode);
        $value->setLocale($localeCode);

        if (null !== $data) {
            $value->setDate(new \DateTime($data));
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function supports($attributeType)
    {
        return $attributeType === $this->supportedAttributeType;
    }

    /**
     * Checks the data.
     *
     * @param AttributeInterface $attribute
     * @param mixed              $data
     *
     * @throws InvalidArgumentException
     */
    protected function checkData(AttributeInterface $attribute, $data)
    {
        if (null === $data) {
            return;
        }

        if (!is_string($data)) {
            throw InvalidArgumentException::expected(
                $attribute->getCode(),
                'datetime or string',
                'date',
                'factory',
                gettype($data)
            );
        }

        try {
            new \DateTime($data);

            if (!preg_match('/^\d{4}-\d{2}-\d{2}/', $data)) {
                $this->throwsInvalidDateException($attribute, $data);
            }
        } catch (\Exception $e) {
            $this->throwsInvalidDateException($attribute, $data);
        }
    }

    /**
     * @param AttributeInterface $attribute
     * @param                    $data
     *
     * @throws InvalidPropertyException
     */
    protected function throwsInvalidDateException(AttributeInterface $attribute, $data)
    {
        throw InvalidPropertyException::dateExpected(
            $attribute->getCode(),
            'yyyy-mm-dd',
            'date',
            'factory',
            $data
        );
    }
}
