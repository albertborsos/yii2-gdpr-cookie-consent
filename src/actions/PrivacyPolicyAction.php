<?php

namespace albertborsos\cookieconsent\actions;

use albertborsos\cookieconsent\Component;
use yii\base\Action;
use yii\di\Instance;
use yii\web\Response;

class PrivacyPolicyAction extends Action
{
    public $viewFilePath = '@vendor/albertborsos/yii2-gdpr-cookie-consent/src/actions/views/privacy-policy.php';

    /**
     * @return array|string|Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        /** @var Component $component */
        $component = Instance::ensure('cookieConsent', Component::class);

        return $this->controller->render($this->viewFilePath, [
            'documents' => $component->documents,
        ]);
    }
}
