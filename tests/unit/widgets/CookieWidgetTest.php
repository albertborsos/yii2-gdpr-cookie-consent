<?php

class CookieWidgetTest extends \Codeception\Test\Unit
{
    public function configDataProvider()
    {
        return [
            [],
        ];
    }

    public function testConfig()
    {
        $widget = $this->mockWidget();
        $this->assertEquals('/', $widget->pluginOptions['domain']);
    }

    private function mockWidget(array $config = [])
    {
        return Yii::createObject(\albertborsos\cookieconsent\widgets\CookieWidget::class, [$config]);
    }
}
