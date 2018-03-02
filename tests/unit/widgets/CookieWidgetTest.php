<?php

use albertborsos\cookieconsent\widgets\CookieWidget;
use yii\helpers\ArrayHelper;

class CookieWidgetTest extends \Codeception\Test\Unit
{
    public function configDataProvider()
    {
        return [
            'default empty config' => [[], []],
            'default empty config hu' => [[], [], 'hu'],
            'position top-pushdown' => [['position' => 'top-pushdown'], ['position' => 'top', 'static' => true]],
            'modified paletteConfig' => [['paletteConfig' => ['popup' => ['background' => '#fff']]], ['palette' => ['popup' => ['background' => '#fff']]]],
            'paletteConfig is false' => [['paletteConfig' => false], ['palette' => []]],
            'paletteConfig is empty array' => [['paletteConfig' => []], ['palette' => []]],
            'modified layout' => [['layout' => 'edgeless'], ['layout' => 'edgeless']],
        ];
    }

    /**
     * @dataProvider configDataProvider
     *
     * @param $widgetConfig
     * @param $expectedConfig
     */
    public function testConfig($widgetConfig, $expectedConfig, $appLanguage = 'en')
    {
        $expectedPluginOptions = array_merge([
            'position' => 'bottom',
            'static' => false,
            'domain' => '/',
            'palette' => [
                'popup' => [
                    'background' => '#000',
                ],
                'button' => [
                    'background' => '#f1d600',
                ],
            ],
            'layout' => 'block',
            'type' => 'opt-out',
            'content' => [
                'message' => '',
                'allow' => '',
                'deny' => '',
                'dismiss' => '',
                'link' => '',
                'href' => '',
            ],
        ], $expectedConfig);

        $widget = $this->mockWidget($widgetConfig);
        $this->assertEquals($expectedPluginOptions['domain'], $widget->pluginOptions['domain']);
        $this->assertEquals($expectedPluginOptions['position'], $widget->pluginOptions['position']);
        $this->assertEquals($expectedPluginOptions['static'], $widget->pluginOptions['static']);
        if (empty($expectedPluginOptions['palette'])) {
            $this->assertEmpty(ArrayHelper::getValue($widget->pluginOptions, 'palette'));
        } else {
            $this->assertEquals($expectedPluginOptions['palette'], $widget->pluginOptions['palette']);
        }
        $this->assertEquals($expectedPluginOptions['layout'], $widget->pluginOptions['layout']);
        $this->assertEquals($expectedPluginOptions['type'], $widget->pluginOptions['type']);
        if (!preg_match(CookieWidget::REGEX_IS_ENGLISH_LANGUAGE, $appLanguage)) {
            $this->assertEquals($expectedPluginOptions['content']['message'], ArrayHelper::getValue($widget->pluginOptions, 'content.message'));
            $this->assertEquals($expectedPluginOptions['content']['dismiss'], ArrayHelper::getValue($widget->pluginOptions, 'content.dismiss'));
            $this->assertEquals($expectedPluginOptions['content']['deny'], ArrayHelper::getValue($widget->pluginOptions, 'content.deny'));
            $this->assertEquals($expectedPluginOptions['content']['allow'], ArrayHelper::getValue($widget->pluginOptions, 'content.allow'));
            $this->assertEquals($expectedPluginOptions['content']['link'], ArrayHelper::getValue($widget->pluginOptions, 'content.link'));
            $this->assertEquals($expectedPluginOptions['content']['href'], ArrayHelper::getValue($widget->pluginOptions, 'content.href'));
        } else { // if language is english
            $this->assertEmpty(ArrayHelper::getValue($widget->pluginOptions, 'content.message'));
            $this->assertEmpty(ArrayHelper::getValue($widget->pluginOptions, 'content.dismiss'));
            $this->assertEmpty(ArrayHelper::getValue($widget->pluginOptions, 'content.deny'));
            $this->assertEmpty(ArrayHelper::getValue($widget->pluginOptions, 'content.allow'));
            $this->assertEmpty(ArrayHelper::getValue($widget->pluginOptions, 'content.link'));
            $this->assertEmpty(ArrayHelper::getValue($widget->pluginOptions, 'content.href'));
        }
    }

    private function mockWidget(array $config = [])
    {
        return Yii::createObject(CookieWidget::class, [$config]);
    }
}
