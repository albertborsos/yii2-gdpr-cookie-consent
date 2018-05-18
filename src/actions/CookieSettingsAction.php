<?php

namespace albertborsos\cookieconsent\actions;

use albertborsos\cookieconsent\Component;
use albertborsos\cookieconsent\domains\CookieSettingsDomain;
use albertborsos\cookieconsent\domains\forms\CookieSettingsForm;
use Yii;
use yii\base\Action;
use yii\di\Instance;
use yii\web\Response;
use yii\widgets\ActiveForm;

class CookieSettingsAction extends Action
{
    public $viewFilePath = '@vendor/albertborsos/yii2-gdpr-cookie-consent/src/actions/views/cookie-settings.php';

    /**
     * @return array|string|Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $this->registerAssets();

        $form = new CookieSettingsForm();

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $domain = new CookieSettingsDomain($form);
            if ($domain->process()) {
                Yii::$app->session->setFlash('success', Yii::t('cookieconsent/form', 'flash.message.success'));
                return $this->controller->refresh();
            }
        }

        /** @var Component $component */
        $component = Instance::ensure('cookieConsent', Component::class);

        return $this->controller->render($this->viewFilePath, [
            'model' => $form,
            'categories' => $component->getCategories(),
        ]);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    private function registerAssets()
    {
        /** @var Component $component */
        $component = Instance::ensure('cookieConsent', Component::class);

        Yii::$app->view->registerJs("
            $(document).on('click', '.cc-revoke-custom', function () {
                document.cookie = 'cookieconsent_status=; Path=" . $component->cookiePath . "; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            });
        ");
    }
}
