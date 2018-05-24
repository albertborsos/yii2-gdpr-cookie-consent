<?php

class CookieConsentTest extends \Codeception\Test\Unit
{
    public function typeProvider()
    {
        return [
            'facebook'                    => ['facebook', 'social', true],
            'facebook app'                => ['facebook-app', 'usagehelper', true],
            'facebook pixel'              => ['facebook-pixel', 'ads', true],
            'google analytics'            => ['google-analytics', 'statistics', true],
            'google analytics anonym'     => ['google-analytics-anonym', 'usage-helper', true],
            'google tag manager'          => ['google-tag-manager', 'statistics', true],
            'hotjar'                      => ['hotjar', 'behavior', true],
            'google maps'                 => ['google-maps', 'social', true],

            'facebook disabled'           => ['facebook', 'social', false],
            'facebook pixel disabled'     => ['facebook-pixel', 'ads', false],
            'google analytics disabled'   => ['google-analytics', 'statistics', false],
            'google tag manager disabled' => ['google-tag-manager', 'statistics', false],
            'hotjar disabled'             => ['hotjar', 'behavior', false],
            'google maps disabled'        => ['google-maps', 'social', false],
        ];
    }

    public function categoryProvider()
    {
        return [
            'CATEGORY_USAGE_HELPER'        => ['usagehelper', true],
            'CATEGORY_SOCIAL'              => ['social', true],
            'CATEGORY_STATISTICS'          => ['statistics', true],
            'CATEGORY_ADS'                 => ['ads', true],
            'CATEGORY_BEHAVIOR'            => ['behavior', true],

            'CATEGORY_SOCIAL disabled'     => ['social', false],
            'CATEGORY_STATISTICS disabled' => ['statistics', false],
            'CATEGORY_ADS disabled'        => ['ads', false],
            'CATEGORY_BEHAVIOR disabled'   => ['behavior', false],
        ];
    }

    /**
     * @dataProvider typeProvider
     *
     * @param $type
     * @param $expected
     * @throws \yii\base\InvalidConfigException
     */
    public function testIsAllowedType($type, $category, $expected)
    {
        $this->mockCookie($category, $expected);
        $isAllowed = \albertborsos\cookieconsent\helpers\CookieConsent::isAllowedType($type);
        $this->assertEquals($expected, $isAllowed);
    }

    /**
     * @dataProvider categoryProvider
     *
     * @param $category
     * @param $expected
     * @throws \yii\base\InvalidConfigException
     */
    public function testIsAllowedCategory($category, $expected)
    {
        $this->mockCookie($category, $expected);
        $isAllowed = \albertborsos\cookieconsent\helpers\CookieConsent::isAllowedCategory($category);
        $this->assertEquals($expected, $isAllowed);
    }

    /**
     * @param $category
     * @param $value
     */
    private function mockCookie($category, $value)
    {
        $name = \albertborsos\cookieconsent\Component::COOKIE_OPTION_PREFIX . $category;
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
        }

        $_COOKIE[$name] = strval($value);
    }
}
