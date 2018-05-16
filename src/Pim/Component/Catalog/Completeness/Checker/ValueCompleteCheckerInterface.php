<?php

namespace Pim\Component\Catalog\Completeness\Checker;

use Akeneo\Channel\Component\Model\LocaleInterface;
use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\ValueInterface;

/**
 * Check if a product value is complete or not for a given couple channel/locale.
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @internal for internal use only, please use the \Pim\Component\Catalog\Completeness\CompletenessGeneratorInterface
 *           to calculate the completeness on a product
 */
interface ValueCompleteCheckerInterface
{
    /**
     * Is the given product value complete on the given couple channel/locale?
     *
     * @param ValueInterface        $value
     * @param ChannelInterface|null $channel
     * @param LocaleInterface|null  $locale
     *
     * @return bool
     */
    public function isComplete(
        ValueInterface $value,
        ChannelInterface $channel,
        LocaleInterface $locale
    );

    /**
     * Is the checker able to determine if the given value is complete on the given couple channel/locale?
     *
     * The checker supports the value if:
     *      - the checker supports the attribute type of the value
     *      - the locale of the value is compatible (localisable + locale specific) with the given locale
     *      - the channel of the value is compatible with the given channel
     *
     * @param ValueInterface   $value
     * @param ChannelInterface $channel
     * @param LocaleInterface  $locale
     *
     * @return bool
     */
    public function supportsValue(
        ValueInterface $value,
        ChannelInterface $channel,
        LocaleInterface $locale
    );
}
