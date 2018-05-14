<?php

class CookieConsentTest extends \Codeception\Test\Unit
{
    public function typeProvider()
    {
        return [
            'facebook'                    => ['facebook', 'performance', true],
            'facebook app'                => ['facebook-app', 'usagehelper', true],
            'facebook pixel'              => ['facebook-pixel', 'ads', true],
            'google analytics'            => ['google-analytics', 'performance', true],
            'google tag manager'          => ['google-tag-manager', 'performance', true],
            'facebook disabled'           => ['facebook', 'performance', false],
            'facebook app disabled'       => ['facebook-pixel', 'ads', false],
            'google analytics disabled'   => ['google-analytics', 'performance', false],
            'google tag manager disabled' => ['google-tag-manager', 'performance', false],
        ];
    }

    public function categoryProvider()
    {
        return [
            'CATEGORY_USAGE_HELPER'         => ['usagehelper', true],
            'CATEGORY_ADS'                  => ['ads', true],
            'CATEGORY_PERFORMANCE'          => ['performance', true],
            'CATEGORY_PERFORMANCE disabled' => ['performance', false],
            'CATEGORY_ADS disabled'         => ['ads', false],
        ];
    }

    /**
     * @dataProvider typeProvider
     *
     * @param $type
     * @param $expected
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
