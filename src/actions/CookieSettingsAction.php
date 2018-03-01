<?php

namespace albertborsos\cookieconsent\actions;

use albertborsos\cookieconsent\domains\CookieSettingsDomain;
use albertborsos\cookieconsent\domains\forms\CookieSettingsForm;
use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\widgets\ActiveForm;

class CookieSettingsAction extends Action
{
    public $viewFilePath = '@vendor/albertborsos/yii2-gdpr-cookie-consent/src/actions/views/cookie-settings.php';

    public function run()
    {
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

        return $this->controller->render($this->viewFilePath, [
            'model' => $form,
        ]);
    }
}
