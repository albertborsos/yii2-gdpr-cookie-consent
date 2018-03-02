<?php
/**
 * Application configuration for unit tests
 */
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/config.php'),
    [
        'basePath' => __DIR__ . '/../unit/runtime/web/',
    ]
);
