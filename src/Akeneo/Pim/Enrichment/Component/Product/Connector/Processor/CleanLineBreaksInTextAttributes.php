<?php
declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Component\Product\Connector\Processor;

use Akeneo\Pim\Structure\Component\AttributeTypes;
use Akeneo\Pim\Structure\Component\Query\PublicApi\AttributeType\Attribute;
use Akeneo\Pim\Structure\Component\Query\PublicApi\AttributeType\GetAttributes;
use Akeneo\Tool\Component\Batch\Model\Warning;

/**
 * @author    Nicolas Marniesse <nicolas.marniesse@akeneo.com>
 * @copyright 2021 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class CleanLineBreaksInTextAttributes
{
    private const LINE_BREAK_REGULAR_EXPRESSION = '/[\r\n|\r|\n]+/';

    private GetAttributes $getAttributes;

    public function __construct(GetAttributes $getAttributes)
    {
        $this->getAttributes = $getAttributes;
    }

    public function cleanStandardFormat(array $item): array
    {
        if (!is_array($item['values'] ?? null)) {
            return $item;
        }

        $fieldsWithLineBreak = $this->getFieldsWithLineBreak($item);
        if (0 === count($fieldsWithLineBreak)) {
            return $item;
        }

        $textAttributeFieldsToClean = $this->getTextAttributeFieldsToClean($fieldsWithLineBreak);

        foreach ($textAttributeFieldsToClean as $field) {
            $valuesForField = $item['values'][$field] ?? null;
            foreach ($valuesForField as $key => $value) {
                if (is_string($value['data'])) {
                    $cleanedData = preg_replace(
                        static::LINE_BREAK_REGULAR_EXPRESSION,
                        ' ',
                        $value['data']
                    );
                    if ($cleanedData !== $value['data']) {
                        $item['values'][$field][$key]['data'] = $cleanedData;
                    }
                }
            }
        }

        return $item;
    }

    /**
     * @return string[]
     */
    private function getFieldsWithLineBreak(array $item): array
    {
        $fieldsWithLineBreak = [];
        foreach ($item['values'] as $field => $values) {
            foreach ($values as $value) {
                if (is_string($value['data']) && preg_match(static::LINE_BREAK_REGULAR_EXPRESSION, $value['data'])) {
                    $fieldsWithLineBreak[] = $field;

                    break;
                }
            }
        }

        return $fieldsWithLineBreak;
    }

    /**
     * @return string[]
     */
    private function getTextAttributeFieldsToClean(array $fieldsWithLineBreak): array
    {
        $attributes = $this->getAttributes->forCodes($fieldsWithLineBreak);

        return array_keys(array_filter(
            $attributes,
            fn (Attribute $attribute): bool => AttributeTypes::TEXT === $attribute->type()
        ));
    }
}
