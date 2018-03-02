<?php
/**
 * Application configuration shared by all test types
 */

$dbDsn = getenv('CI_DB_DSN');
$dbUsername = getenv('CI_DB_USERNAME');
$dbPassword = getenv('CI_DB_PASSWORD');

$dbConfig = [
    'dsn' => 'mysql:host=localhost;dbname=yii2_basic_tests',
];
if ($dbDsn !== false) {
    $dbConfig['dsn'] = $dbDsn;
}
if ($dbUsername !== false) {
    $dbConfig['username'] = $dbUsername;
}
if ($dbPassword !== false) {
    $dbConfig['password'] = $dbPassword;
}

return [
    'id' => 'gdpr-test',
    'vendorPath' => __DIR__ . '/../../vendor',
    'components' => [
        'assetManager' => [
            'basePath' => '@gdprcookieconsent/tests/unit/runtime/web/assets',
            'bundles' => [
                'albertborsos\cookieconsent\assets\CookieConsentAsset' => [
                    'sourcePath' => '@bower/cookieconsent/build/',
                ],
            ],
        ],
        //'db' => $dbConfig,
        'i18n' => [
            /** Message tokeneket/fordításokat tartalmazó fájlok */
            'translations' => [
                'cookieconsent/*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@vendor/albertborsos/yii2-gdpr-cookie-consent/src/messages',
                ],
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                ],
            ],
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'cookieConsent' => [
            'class' => '\albertborsos\cookieconsent\Component',
            'complianceType' => \albertborsos\cookieconsent\Component::COMPLIANCE_TYPE_OPT_OUT,
        ],
    ],
];
