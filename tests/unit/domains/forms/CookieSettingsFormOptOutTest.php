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

    public function invalidSettingsNotAnsweredOrDismissedProvider()
    {
        return [
            'session is always required' => ['options.session', 0, 1, 1, 1, 1],
            'usage helper is required if at least one (not mandatory) category is disabled' => ['options.usagehelper', 1, 0, 0, 1, 1],
        ];
    }

    public function invalidSettingsDeniedProvider()
    {
        return [
            'session is always required' => ['options.session', 0, 0, 0, 0, 0],
            'usage helper is required if at least one (not mandatory) category is enabled' => ['options.usagehelper', 1, 0, 0, 0, 1],
        ];
    }

    public function validSettingsNotAnsweredOrDismissedProvider()
    {
        return [
            'everything is enabled' => [1, 1, 1, 1, 1],
            'session and usagehelper is enabled' => [1, 0, 1, 0, 0],
        ];
    }

    public function validSettingsDeniedProvider()
    {
        return [
            'everything is enabled' => [1, 1, 1, 1, 1],
            'everything is disabled but session' => [1, 0, 0, 0, 0],
            'session and usagehelper is enabled' => [1, 0, 1, 0, 0],
        ];
    }

    /**
     * @dataProvider invalidSettingsNotAnsweredOrDismissedProvider
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
     * @dataProvider invalidSettingsNotAnsweredOrDismissedProvider
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
     * @dataProvider invalidSettingsDeniedProvider
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
     * @dataProvider validSettingsNotAnsweredOrDismissedProvider
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
     * @dataProvider validSettingsNotAnsweredOrDismissedProvider
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
     * @dataProvider validSettingsDeniedProvider
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
