<?php

namespace albertborsos\cookieconsent\assets;

use yii\web\AssetBundle;

class CookieConsentAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@bower/cookieconsent/build/';

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
