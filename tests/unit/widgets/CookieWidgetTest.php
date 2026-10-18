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
            'modified layout' => [['layout' => 'edgeless'], ['layout' => 'edgeless']],
            'custom message' => [['message' => 'custom'], ['content' => ['message' => 'custom']]],
            'custom dismissButtonText' => [['dismissButtonText' => 'custom'], ['content' => ['dismiss' => 'custom']]],
            'custom denyButtonText' => [['denyButtonText' => 'custom'], ['content' => ['deny' => 'custom']]],
            'custom allowButtonText' => [['allowButtonText' => 'custom'], ['content' => ['allow' => 'custom']]],
            'custom policyLinkText' => [['policyLinkText' => 'custom'], ['content' => ['link' => 'custom']]],
            'custom policyLink' => [['policyLink' => 'custom'], ['content' => ['href' => 'http://localhost/custom']]],
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
            'domain' => '',
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
        if (ArrayHelper::getValue($widgetConfig, 'paletteConfig') === false) {
            $this->assertEmpty(ArrayHelper::getValue($widget->pluginOptions, 'palette'));
        } else {
            $this->assertEquals($expectedPluginOptions['palette'], $widget->pluginOptions['palette']);
        }
        $this->assertEquals($expectedPluginOptions['layout'], $widget->pluginOptions['layout']);
        $this->assertEquals($expectedPluginOptions['type'], $widget->pluginOptions['type']);

        $contentFields = [
            'message' => 'message',
            'dismissButtonText' => 'dismiss',
            'denyButtonText' => 'deny',
            'allowButtonText' => 'allow',
            'policyLinkText' => 'link',
            'policyLink' => 'href',
        ];

        foreach ($contentFields as $widgetField => $pluginOptionKey) {
            if (!preg_match(CookieWidget::REGEX_IS_ENGLISH_LANGUAGE, $appLanguage) || isset($widgetConfig[$widgetField])) {
                $this->assertEquals($expectedPluginOptions['content'][$pluginOptionKey], ArrayHelper::getValue($widget->pluginOptions, 'content.' . $pluginOptionKey));
            } else { // if language is english
                $this->assertEmpty(ArrayHelper::getValue($widget->pluginOptions, 'content.' . $pluginOptionKey));
            }
        }
    }

    public function testConsentChangesReloadEveryPage()
    {
        $widget = $this->mockWidget();

        foreach (['onStatusChange', 'onRevokeChoice'] as $callback) {
            $script = (string) $widget->pluginOptions[$callback];
            $this->assertContains('cookieconsent_option_', $script);
            $this->assertContains('window.location.reload();', $script);
            $this->assertNotContains('currentUrl', $script);
            $this->assertNotContains('policyUrl', $script);
            $this->assertLessThan(strpos($script, 'window.location.reload();'), strpos($script, 'document.cookie'));
        }
    }

    public function testCustomConsentCallbacksArePreserved()
    {
        $callback = new \yii\web\JsExpression('function(){window.customConsentHandler();}');
        $widget = $this->mockWidget([
            'pluginOptions' => [
                'onStatusChange' => $callback,
                'onRevokeChoice' => $callback,
            ],
        ]);

        $this->assertSame($callback, $widget->pluginOptions['onStatusChange']);
        $this->assertSame($callback, $widget->pluginOptions['onRevokeChoice']);
    }

    private function mockWidget(array $config = [])
    {
        return Yii::createObject(CookieWidget::class, [$config]);
    }
}
