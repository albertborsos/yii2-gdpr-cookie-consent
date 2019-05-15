<?php

namespace albertborsos\cookieconsent\actions;

use albertborsos\cookieconsent\Component;
use albertborsos\cookieconsent\domains\CookieSettingsDomain;
use albertborsos\cookieconsent\domains\forms\CookieSettingsForm;
use albertborsos\cookieconsent\helpers\CookieHelper;
use albertborsos\cookieconsent\interfaces\CookieComponentInterface;
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
                Yii::$app->session->addFlash('success', Yii::t('cookieconsent/form', 'flash.message.success'));
                return $this->controller->refresh();
            }
        }

        /** @var Component $component */
        $component = CookieHelper::getComponent();

        return $this->controller->render($this->viewFilePath, [
            'model' => $form,
            'categories' => $component->getCategories(),
            'resetLink' => $component->urlSettings,
        ]);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    private function registerAssets()
    {
        /** @var Component $component */
        $component = CookieHelper::getComponent();

        Yii::$app->view->registerJs("
            $(document).on('click', '.cc-revoke-custom', function (e) {
                e.preventDefault();
                var cookieNames = " . \yii\helpers\Json::encode($component->getCategories()) . ';
                $.each(cookieNames, function(){
                    document.cookie = ' . $component->removeCookieConfig("cookieconsent_option_' + this + '") . ';
                });
                document.cookie = ' . $component->removeCookieConfig('cookieconsent_status') . ';
                // update cookie settings page if cookieconsent status changed on this page
                window.location.reload();
            });
        ');
    }
}
