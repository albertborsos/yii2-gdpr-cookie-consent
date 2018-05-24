<?php

namespace albertborsos\cookieconsent\helpers;

use albertborsos\cookieconsent\Component;
use yii\di\Instance;

class CookieConsent
{
    const TYPE_FACEBOOK       = 'facebook';
    const TYPE_FACEBOOK_APP   = 'facebook-app';
    const TYPE_FACEBOOK_PIXEL = 'facebook-pixel';

    const TYPE_GOOGLE_ANALYTICS        = 'google-analytics';
    const TYPE_GOOGLE_ANALYTICS_ANONYM = 'google-analytics-anonym';
    const TYPE_GOOGLE_TAG_MANAGER      = 'google-tag-manager';
    const TYPE_GOOGLE_MAPS             = 'google-maps';

    const TYPE_HOTJAR = 'hotjar';

    const MAPPING = [
        Component::CATEGORY_USAGE_HELPER => [
            self::TYPE_GOOGLE_ANALYTICS_ANONYM,
            self::TYPE_FACEBOOK_APP, // facebook app cookies are required if it is in use
        ],
        Component::CATEGORY_ADS => [
            self::TYPE_FACEBOOK_PIXEL,
        ],
        Component::CATEGORY_SOCIAL => [
            self::TYPE_FACEBOOK,
            self::TYPE_GOOGLE_MAPS,
        ],
        Component::CATEGORY_STATISTICS => [
            self::TYPE_GOOGLE_ANALYTICS,
            self::TYPE_GOOGLE_TAG_MANAGER,
        ],
        Component::CATEGORY_BEHAVIOR => [
            self::TYPE_HOTJAR,
        ],
    ];

    /**
     * @param $type
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function isAllowedType($type)
    {
        /** @var Component $component */
        $component = Instance::ensure('cookieConsent', Component::class);
        return $component->isAllowed(self::getCategoryByType($type));
    }

    /**
     * @param $category
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function isAllowedCategory($category)
    {
        /** @var Component $component */
        $component = Instance::ensure('cookieConsent', Component::class);
        return $component->isAllowed($category);
    }

    /**
     * @param $type
     * @return int|null|string
     */
    private static function getCategoryByType($type)
    {
        foreach (self::MAPPING as $category => $types) {
            if (in_array($type, $types)) {
                return $category;
            }
        }

        return null;
    }
}
