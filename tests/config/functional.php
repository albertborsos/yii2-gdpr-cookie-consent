<?php
$_SERVER['SCRIPT_FILENAME'] = TEST_ENTRY_FILE;
$_SERVER['SCRIPT_NAME'] = TEST_ENTRY_URL;

/**
 * Application configuration for functional tests
 */
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/config.php'),
    [
        'components' => [
            'request' => [
                // Csrf validation does not work in functional tests
                'enableCsrfValidation' => false,
            ],
        ],
    ]
);
