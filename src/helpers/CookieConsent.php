<?php

namespace albertborsos\cookieconsent\helpers;

use albertborsos\cookieconsent\Component;
use yii\di\Instance;

class CookieConsent
{
    const TYPE_FACEBOOK = 'facebook';
    const TYPE_FACEBOOK_APP = 'facebook-app';
    const TYPE_GOOGLE_ANALYTICS = 'google-analytics';
    const TYPE_GOOGLE_TAG_MANAGER = 'google-tag-manager';

    const TYPES = [
        self::TYPE_FACEBOOK,
        self::TYPE_GOOGLE_ANALYTICS,
        self::TYPE_GOOGLE_TAG_MANAGER,
    ];

    const MAPPING = [
        Component::CATEGORY_USAGE_HELPER => [
            self::TYPE_FACEBOOK_APP,
        ],
        Component::CATEGORY_PERFORMANCE => [
            self::TYPE_FACEBOOK,
            self::TYPE_GOOGLE_ANALYTICS,
            self::TYPE_GOOGLE_TAG_MANAGER,
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
        return $component->isAllowed(static::getCategoryByType($type));
    }

    public static function isAllowedCategory($category)
    {
        /** @var Component $component */
        $component = Instance::ensure('cookieConsent', Component::class);
        return $component->isAllowed($category);
    }

    /**
     * @param $type
     * @return int|string
     */
    private static function getCategoryByType($type)
    {
        foreach (self::MAPPING as $category => $types) {
            if (in_array($type, $types)) {
                return $category;
            }
        }
    }
}
