<?php

namespace albertborsos\cookieconsent\assets;

use yii\web\AssetBundle;

class CookieConsentAsset extends AssetBundle
{
    /**
     * Workaround for this issue: https://github.com/insites/cookieconsent/pull/261
     *
     * @var string
     */
    public $sourcePath = '@vendor/albertborsos/yii2-gdpr-cookie-consent/src/assets/vendor/cookieconsent-fixed/';
    // public $sourcePath = '@bower/cookieconsent/build/';

    /**
     * @var array
     */
    public $js = [
        'cookieconsent.min.js',
    ];

    /**
     * @var array
     */
    public $css = [
        'cookieconsent.min.css',
    ];

    /**
     * @var array
     */
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
