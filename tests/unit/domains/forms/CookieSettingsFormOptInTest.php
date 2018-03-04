<?php
/**
 * Created by PhpStorm.
 * User: aborsos
 * Date: 2018. 03. 04.
 * Time: 11:31
 */

class CookieSettingsFormOptInTest extends \albertborsos\ddd\tests\support\base\AbstractFormTest
{
    protected $formClass = \albertborsos\cookieconsent\domains\forms\CookieSettingsForm::class;

    protected function setUp()
    {
        Yii::$app->cookieConsent->complianceType = \albertborsos\cookieconsent\Component::COMPLIANCE_TYPE_OPT_IN;
        return parent::setUp();
    }

    public function invalidSettingsDefaultProvider()
    {
        return [
            'session is always required' => ['options.session', 0, 1, 1, 1, 1],
            'usage helper is required if at least one (not mandatory) category is enabled' => ['options.usagehelper', 1, 0, 0, 0, 1],
        ];
    }

    public function validSettingsProvider()
    {
        return [
            'everything is enabled' => [1, 1, 1, 1, 1],
            //'everything is disabled but session' => [1, 0, 0, 0, 0],
        ];
    }

    /**
     * @dataProvider invalidSettingsDefaultProvider
     *
     * @param $expectedErrorField
     * @param $session
     * @param $ads
     * @param $usageHelper
     * @param $performance
     * @param $behavior
     */
    public function testInvalid($expectedErrorField, $session, $ads, $usageHelper, $performance, $behavior)
    {
        $form = $this->mockForm([
            'options' => [
                \albertborsos\cookieconsent\Component::CATEGORY_SESSION => $session,
                \albertborsos\cookieconsent\Component::CATEGORY_ADS => $ads,
                \albertborsos\cookieconsent\Component::CATEGORY_USAGE_HELPER => $usageHelper,
                \albertborsos\cookieconsent\Component::CATEGORY_PERFORMANCE => $performance,
                \albertborsos\cookieconsent\Component::CATEGORY_BEHAVIOR => $behavior,
            ],
        ]);

        $this->assertFalse($form->validate());
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
    public function testValid($session, $ads, $usageHelper, $performance, $behavior)
    {
        $form = $this->mockForm([
            'options' => [
                \albertborsos\cookieconsent\Component::CATEGORY_SESSION => $session,
                \albertborsos\cookieconsent\Component::CATEGORY_ADS => $ads,
                \albertborsos\cookieconsent\Component::CATEGORY_USAGE_HELPER => $usageHelper,
                \albertborsos\cookieconsent\Component::CATEGORY_PERFORMANCE => $performance,
                \albertborsos\cookieconsent\Component::CATEGORY_BEHAVIOR => $behavior,
            ],
        ]);

        $this->assertTrue($form->validate());
        $this->assertEquals(0, count($form->getErrors()));
    }
}
