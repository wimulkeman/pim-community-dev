<?php


namespace Akeneo\Tool\Component\Api\Exception;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @package   Akeneo\Tool\Component\Api\Exception
 * @author    Thomas Galvaing <thomas.galvaing@akeneo.com>
 * @copyright 2020 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductViolationHttpException extends UnprocessableEntityHttpException
{
    /** @var string */
    protected $violations;

    /** @var ProductInterface */
    protected $product;

    /**
     * @param ConstraintViolationListInterface $violations
     * @param ProductInterface                 $product
     * @param string                           $message
     * @param \Exception|null                  $previous
     * @param int                              $code
     */
    public function __construct(
        ProductInterface $product,
        ConstraintViolationListInterface $violations,
        $message = 'Validation failed.',
        \Exception $previous = null,
        $code = 0
    ) {
        parent::__construct($message, $previous, $code);

        $this->violations = $violations;
        $this->product = $product;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * @return ProductInterface
     */
    public function getProduct() : ProductInterface {
        return $this->product;
    }
}