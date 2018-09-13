<?php

namespace albertborsos\cookieconsent\domains;

use albertborsos\cookieconsent\Component;
use albertborsos\cookieconsent\helpers\CookieHelper;
use albertborsos\cookieconsent\interfaces\CookieComponentInterface;
use albertborsos\ddd\models\AbstractDomain;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

class CookieSettingsDomain extends AbstractDomain
{
    /**
     * Business logic to store data for multiple resources.
     *
     * @return bool|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function process()
    {
        $this->storeSettingsInCookies();

        return true;
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    private function storeSettingsInCookies()
    {
        /** @var Component $component */
        $component = CookieHelper::getComponent();

        $expireAt = time() + $component->cookieExpire;
        foreach ($this->getForm()->options as $category => $newValue) {
            $name = Component::COOKIE_OPTION_PREFIX . $category;
            $currentValue = ArrayHelper::getValue($_COOKIE, $name);
            if ($currentValue !== $newValue) {
                unset($_COOKIE[$name]);
                setcookie($name, $newValue, $expireAt, $component->cookiePath, $component->cookieDomain, $component->cookieSecure, $component->cookieHttpOnly);
            }
        }
    }
}
