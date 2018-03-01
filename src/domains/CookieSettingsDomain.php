<?php

namespace albertborsos\cookieconsent\domains;

use albertborsos\cookieconsent\Component;
use albertborsos\ddd\models\AbstractDomain;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;

class CookieSettingsDomain extends AbstractDomain
{

    /**
     * Business logic to store data for multiple resources.
     *
     * @return mixed
     */
    public function process()
    {
        $this->storeSettingsInCookies();

        return true;
    }

    private function storeSettingsInCookies()
    {
        foreach ($this->getForm()->options as $category => $newValue) {
            $name = Component::COOKIE_OPTION_PREFIX . $category;
            $currentValue = ArrayHelper::getValue($_COOKIE, $name);
            if ($currentValue !== $newValue) {
                unset($_COOKIE[$name]);
                setcookie($name, $newValue);
            }
        }
    }
}
