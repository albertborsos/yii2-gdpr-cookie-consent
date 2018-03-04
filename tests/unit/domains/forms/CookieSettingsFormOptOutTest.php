<?php
/**
 * Created by PhpStorm.
 * User: aborsos
 * Date: 2018. 03. 04.
 * Time: 11:31
 */

class CookieSettingsFormOptOutTest extends \albertborsos\ddd\tests\support\base\AbstractFormTest
{
    protected $formClass = \albertborsos\cookieconsent\domains\forms\CookieSettingsForm::class;

    public function invalidSettingsProvider()
    {
        return [
            'session is always required - disabled' => ['options.session', 0, 0, 1, 0, 0],
            'session is always required - enabled' => ['options.session', 0, 1, 1, 1, 1],
            'usagehelper is always required - disabled' => ['options.usagehelper', 1, 0, 0, 0, 0],
            'usagehelper is always required - enabled' => ['options.usagehelper', 1, 1, 0, 1, 1],
        ];
    }

    public function validSettingsProvider()
    {
        return [
            'everything is enabled' => [1, 1, 1, 1, 1],
            'everything is disabled but session and usagehelper' => [1, 0, 1, 0, 0],
        ];
    }

    /**
     * @dataProvider invalidSettingsProvider
     *
     * @param $expectedErrorField
     * @param $session
     * @param $ads
     * @param $usageHelper
     * @param $performance
     * @param $behavior
     */
    public function testNotAnsweredInvalid($expectedErrorField, $session, $ads, $usageHelper, $performance, $behavior)
    {
        $this->mockComponent(null);

        $form = $this->mockForm([
            'options' => [
                \albertborsos\cookieconsent\Component::CATEGORY_SESSION => $session,
                \albertborsos\cookieconsent\Component::CATEGORY_ADS => $ads,
                \albertborsos\cookieconsent\Component::CATEGORY_USAGE_HELPER => $usageHelper,
                \albertborsos\cookieconsent\Component::CATEGORY_PERFORMANCE => $performance,
                \albertborsos\cookieconsent\Component::CATEGORY_BEHAVIOR => $behavior,
            ],
        ]);

        $this->assertFalse($form->validate(), \yii\helpers\Json::encode($form->getErrors()));
        $this->assertEquals(1, count($form->getErrors()));
        $this->assertArrayHasKey($expectedErrorField, $form->getErrors());
    }
    /**
     * @dataProvider invalidSettingsProvider
     *
     * @param $expectedErrorField
     * @param $session
     * @param $ads
     * @param $usageHelper
     * @param $performance
     * @param $behavior
     */
    public function testAllowedInvalid($expectedErrorField, $session, $ads, $usageHelper, $performance, $behavior)
    {
        $this->mockComponent(\albertborsos\cookieconsent\Component::STATUS_DISMISSED);
        $form = $this->mockForm([
            'options' => [
                \albertborsos\cookieconsent\Component::CATEGORY_SESSION => $session,
                \albertborsos\cookieconsent\Component::CATEGORY_ADS => $ads,
                \albertborsos\cookieconsent\Component::CATEGORY_USAGE_HELPER => $usageHelper,
                \albertborsos\cookieconsent\Component::CATEGORY_PERFORMANCE => $performance,
                \albertborsos\cookieconsent\Component::CATEGORY_BEHAVIOR => $behavior,
            ],
        ]);

        $this->assertFalse($form->validate(), \yii\helpers\Json::encode($form->getErrors()));
        $this->assertEquals(1, count($form->getErrors()));
        $this->assertArrayHasKey($expectedErrorField, $form->getErrors());
    }

    /**
     * @dataProvider invalidSettingsProvider
     *
     * @param $expectedErrorField
     * @param $session
     * @param $ads
     * @param $usageHelper
     * @param $performance
     * @param $behavior
     */
    public function testDeniedInvalid($expectedErrorField, $session, $ads, $usageHelper, $performance, $behavior)
    {
        $this->mockComponent(\albertborsos\cookieconsent\Component::STATUS_DENIED);
        $form = $this->mockForm([
            'options' => [
                \albertborsos\cookieconsent\Component::CATEGORY_SESSION => $session,
                \albertborsos\cookieconsent\Component::CATEGORY_ADS => $ads,
                \albertborsos\cookieconsent\Component::CATEGORY_USAGE_HELPER => $usageHelper,
                \albertborsos\cookieconsent\Component::CATEGORY_PERFORMANCE => $performance,
                \albertborsos\cookieconsent\Component::CATEGORY_BEHAVIOR => $behavior,
            ],
        ]);

        $this->assertFalse($form->validate(), \yii\helpers\Json::encode($form->getErrors()));
        $this->assertEquals(1, count($form->getErrors()));
        $this->assertArrayHasKey($expectedErrorField, $form->getErrors());
    }

    /**
     * @dataProvider validSettingsProvider
     *
     * @param $session
     * @param $ads
     * @param $usageHelper
     * @param $performance
     * @param $behavior
     */
    public function testNotAnsweredValid($session, $ads, $usageHelper, $performance, $behavior)
    {
        $this->mockComponent(null);
        $form = $this->mockForm([
            'options' => [
                \albertborsos\cookieconsent\Component::CATEGORY_SESSION => $session,
                \albertborsos\cookieconsent\Component::CATEGORY_ADS => $ads,
                \albertborsos\cookieconsent\Component::CATEGORY_USAGE_HELPER => $usageHelper,
                \albertborsos\cookieconsent\Component::CATEGORY_PERFORMANCE => $performance,
                \albertborsos\cookieconsent\Component::CATEGORY_BEHAVIOR => $behavior,
            ],
        ]);

        $this->assertTrue($form->validate(), \yii\helpers\Json::encode($form->getErrors()));
        $this->assertEquals(0, count($form->getErrors()));
    }

    /**
     * @dataProvider validSettingsProvider
     *
     * @param $session
     * @param $ads
     * @param $usageHelper
     * @param $performance
     * @param $behavior
     */
    public function testAllowedValid($session, $ads, $usageHelper, $performance, $behavior)
    {
        $this->mockComponent(\albertborsos\cookieconsent\Component::STATUS_DISMISSED);
        $form = $this->mockForm([
            'options' => [
                \albertborsos\cookieconsent\Component::CATEGORY_SESSION => $session,
                \albertborsos\cookieconsent\Component::CATEGORY_ADS => $ads,
                \albertborsos\cookieconsent\Component::CATEGORY_USAGE_HELPER => $usageHelper,
                \albertborsos\cookieconsent\Component::CATEGORY_PERFORMANCE => $performance,
                \albertborsos\cookieconsent\Component::CATEGORY_BEHAVIOR => $behavior,
            ],
        ]);

        $this->assertTrue($form->validate(), \yii\helpers\Json::encode($form->getErrors()));
        $this->assertEquals(0, count($form->getErrors()));
    }

    /**
     * @dataProvider validSettingsProvider
     *
     * @param $session
     * @param $ads
     * @param $usageHelper
     * @param $performance
     * @param $behavior
     */
    public function testDeniedValid($session, $ads, $usageHelper, $performance, $behavior)
    {
        $this->mockComponent(\albertborsos\cookieconsent\Component::STATUS_DENIED);
        $form = $this->mockForm([
            'options' => [
                \albertborsos\cookieconsent\Component::CATEGORY_SESSION => $session,
                \albertborsos\cookieconsent\Component::CATEGORY_ADS => $ads,
                \albertborsos\cookieconsent\Component::CATEGORY_USAGE_HELPER => $usageHelper,
                \albertborsos\cookieconsent\Component::CATEGORY_PERFORMANCE => $performance,
                \albertborsos\cookieconsent\Component::CATEGORY_BEHAVIOR => $behavior,
            ],
        ]);

        $this->assertTrue($form->validate(), \yii\helpers\Json::encode($form->getErrors()));
        $this->assertEquals(0, count($form->getErrors()));
    }

    private function mockComponent($status)
    {
        Yii::$app->cookieConsent->complianceType = \albertborsos\cookieconsent\Component::COMPLIANCE_TYPE_OPT_OUT;
        Yii::$app->cookieConsent->setStatus($status);
    }
}
