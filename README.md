GDPR compatible Cookie Consent widget for Yii 2.0 Framework
===========================================================

GDPR compatible Cookie Consent widget allows the user to choose which kind of cookies they want to accept.

[![Build Status](https://travis-ci.org/albertborsos/yii2-gdpr-cookie-consent.svg?branch=master)](https://travis-ci.org/albertborsos/yii2-gdpr-cookie-consent)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Run

```
composer require --prefer-dist albertborsos/yii2-gdpr-cookie-consent
```


Usage
-----

add the component to your config file:

```php
<?php
return [
    // ...
    'components' => [
        // ...
        'cookieConsent' => [
            'class' => \albertborsos\cookieconsent\Component::class,
            'urlSettings' => ['/site/cookie-settings'],
            'urlPrivacyPolicy' => ['/site/privacy-policy'],
            'documents' => [
                ['name' => 'Privacy Policy', 'url' => ['/docs/privacy-policy.pdf']],
            ],
            'disabledCategories' => [
                \albertborsos\cookieconsent\Component::CATEGORY_BEHAVIOR,
            ],
        ],
        // ...
        'i18n' => [
            // ...
            'translations' => [
                'cookieconsent/*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@vendor/albertborsos/yii2-gdpr-cookie-consent/src/messages',
                ],
            ],
            // ...
        ],
    ],
    // ...
];
```

Register the widget in your layout. For example in a `_cookieconsent.php` partial view.

```php
<?php
/** @var \albertborsos\cookieconsent\Component $component */
$component = Yii::$app->cookieConsent;
$component->registerWidget([
    'policyLink' => ['/default/cookie-settings'],
    'policyLinkText' => \yii\helpers\Html::tag('i', null, ['class' => 'fa fa-cog']) . ' Beállítások',
    'pluginOptions' => [
        'expiryDays' => 365,
        'hasTransition' => false,
        'revokeBtn' => '<div class="cc-revoke {{classes}}">Cookie Policy</div>',
    ],
]);

```

Add the cookie settings form to any of your controller:

```php
<?php

namespace app\controllers;

class SiteController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'cookie-settings' => \albertborsos\cookieconsent\actions\CookieSettingsAction::class,
            'privacy-policy' => \albertborsos\cookieconsent\actions\PrivacyPolicyAction::class,
        ];
    }
}

```

Check your relevant widget is allowed by the user or not with the CookieConsent helper class in the following way:

```php
<?php

use \albertborsos\cookieconsent\helpers\CookieConsent;
use \albertborsos\cookieconsent\Component;

if(CookieConsent::isAllowedType(CookieConsent::TYPE_GOOGLE_ANALYTICS)){
    // register GA script
}

if(CookieConsent::isAllowedCategory(Component::CATEGORY_BEHAVIOR)){
    // register hotjar script
}

```
