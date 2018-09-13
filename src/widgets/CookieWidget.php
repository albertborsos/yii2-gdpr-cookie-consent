<?php

namespace albertborsos\cookieconsent\widgets;

use albertborsos\cookieconsent\assets\CookieConsentAsset;
use albertborsos\cookieconsent\Component;
use albertborsos\cookieconsent\interfaces\CookieComponentInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

class CookieWidget extends Widget
{
    const POSITION_BOTTOM = 'bottom';
    const POSITION_TOP = 'top';
    const POSITION_TOP_PUSHDOWN = 'top-pushdown';
    const POSITION_EDGELESS = 'edgeless';
    const POSITION_BOTTOM_RIGHT = 'bottom-right';
    const POSITION_BOTTOM_LEFT = 'bottom-left';

    const LAYOUT_BLOCK = 'block';
    const LAYOUT_CLASSIC = 'classic';
    const LAYOUT_EDGELESS = 'edgeless';

    const COMPLIANCE_TYPE_INFO = 'info';
    const COMPLIANCE_TYPE_OPT_IN = 'opt-in';
    const COMPLIANCE_TYPE_OPT_OUT = 'opt-out';

    const REGEX_IS_ENGLISH_LANGUAGE = '/^en(-[A-Z]{2})?$/';

    const DEFAULT_DOMAIN = '/';

    const DEFAULT_PALETTE = [
        'popup' => [
            'background' => '#000',
        ],
        'button' => [
            'background' => '#f1d600',
        ],
    ];

    /**
     * @var string
     */
    public $position = self::POSITION_BOTTOM;

    /**
     * By default the DEFAULT_PALETTE config will be loaded, but you can override it.
     * To totally skip palette configurations, set this property to `false`.
     *
     * @var array|false
     */
    public $paletteConfig = [];

    /**
     * @var string
     */
    public $layout = self::LAYOUT_BLOCK;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $dismissButtonText;

    /**
     * @var string for opt-out type compliance
     */
    public $denyButtonText;

    /**
     * @var string for opt-in type compliance
     */
    public $allowButtonText;

    /**
     * @var string
     */
    public $policyLinkText;

    /**
     * @var bool|string|array Link to your own policy
     */
    public $policyLink;

    /**
     * Custom plugin options. Will be merged with the default options.
     *
     * @var array
     */
    public $pluginOptions = [];

    /**
     * This option must be set, otherwise your cookies may not work.
     *
     * @deprecated since 1.0.4 uses value from component
     * @var string
     */
    public $domain = self::DEFAULT_DOMAIN;

    /**
     * @var Component
     */
    private $_component;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->loadComponent();
        $this->initializeTranslations();
        $this->preparePluginOptions();
        $this->fixOptInDismissButtonText();
        $this->registerAssets();
    }

    public function getComponent()
    {
        return $this->_component;
    }

    private function initializeTranslations()
    {
        if (preg_match(self::REGEX_IS_ENGLISH_LANGUAGE, Yii::$app->language)) {
            // use the default cookieconsent.js messages for english languages
            return;
        }

        $languageMessages['content'] = [
            'message' => Yii::t('cookieconsent/widget', 'message'),
            'allow'   => Yii::t('cookieconsent/widget', 'allow'),
            'deny'    => Yii::t('cookieconsent/widget', 'deny'),
            'dismiss' => Yii::t('cookieconsent/widget', 'dismiss'),
            'link'    => Yii::t('cookieconsent/widget', 'link'),
        ];

        $this->pluginOptions = ArrayHelper::merge($languageMessages, $this->pluginOptions);
    }

    private function preparePluginOptions()
    {
        if (!isset($this->pluginOptions['domain'])) {
            $this->pluginOptions['domain'] = $this->getComponent()->cookieDomain;
        }

        if (!isset($this->pluginOptions['cookie']['secure'])) {
            $this->pluginOptions['cookie']['secure'] = $this->getComponent()->cookieSecure;
        }

        if (!empty($this->position)) {
            $this->pluginOptions['position'] = $this->position;

            $this->pluginOptions['static'] = false;
            if ($this->position === self::POSITION_TOP_PUSHDOWN) {
                $this->pluginOptions['position'] = self::POSITION_TOP;
                $this->pluginOptions['static'] = true;
            }
        }

        if ($this->paletteConfig !== false) {
            if (empty(ArrayHelper::getValue($this->pluginOptions, 'palette'))) {
                $this->pluginOptions['palette'] = self::DEFAULT_PALETTE;
            }

            if (!empty($this->paletteConfig)) {
                $this->pluginOptions['palette'] = $this->paletteConfig;
            }
        }

        if (!empty($this->layout)) {
            $this->pluginOptions['layout'] = $this->layout;
        }

        /** compliance type must be configured in the config of \albertborsos\cookieconsent\Component */
        $this->pluginOptions['type'] = $this->getComponent()->complianceType;

        if (!empty($this->message)) {
            $this->pluginOptions['content']['message'] = $this->message;
        }

        if (!empty($this->dismissButtonText)) {
            $this->pluginOptions['content']['dismiss'] = $this->dismissButtonText?: Yii::t('cookieconsent/widget', 'dismiss');
        }

        if (!empty($this->denyButtonText)) {
            $this->pluginOptions['content']['deny'] = $this->denyButtonText ?: Yii::t('cookieconsent/widget', 'deny');
        }

        if (!empty($this->allowButtonText)) {
            $this->pluginOptions['content']['allow'] = $this->allowButtonText ?: Yii::t('cookieconsent/widget', 'allow');
        }

        if (!empty($this->policyLinkText)) {
            $this->pluginOptions['content']['link'] = $this->policyLinkText;
        }

        if (!empty($this->policyLink)) {
            $this->pluginOptions['content']['href'] = Url::to($this->policyLink, true);
        }

        if (!isset($this->pluginOptions['onStatusChange'])) {
            $this->pluginOptions['onStatusChange'] = $this->getRemoveCookieJsExpression();
        }
        if (!isset($this->pluginOptions['onRevokeChoice'])) {
            $this->pluginOptions['onRevokeChoice'] = $this->getRemoveCookieJsExpression();
        }
    }

    /**
     * @throws InvalidConfigException
     */
    private function registerAssets()
    {
        if (!Yii::$app instanceof \yii\web\Application) {
            return;
        }

        $view = Yii::$app->getView();
        CookieConsentAsset::register($view);
        $view->registerJs('window.cookieconsent.initialise(' . Json::encode($this->pluginOptions) . ');', View::POS_READY);
    }

    private function loadComponent()
    {
        /** @var CookieComponentInterface $component */
        $component = Instance::ensure('cookieConsent', CookieComponentInterface::class);
        $this->_component = $component->getComponent();
    }

    private function getRemoveCookieJsExpression()
    {
        return new \yii\web\JsExpression('function(){
            var cookieNames = ' . \yii\helpers\Json::encode($this->getComponent()->getCategories()) . ";
            $.each(cookieNames, function(){
                document.cookie = 'cookieconsent_option_' + this + '=; Domain=" . $this->getComponent()->cookieDomain . '; Path=' . $this->getComponent()->cookiePath . '; Secure=' . $this->getComponent()->cookieSecure . "; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            });
            var currentUrl = window.location.href;
            var policyUrl = '" . Url::to($this->policyLink, true) . "';
            if (currentUrl==policyUrl) {
                // update cookie settings page if cookieconsent status changed on this page
                window.location.reload();
            }
        }");
    }

    private function fixOptInDismissButtonText()
    {
        if ($this->getComponent()->complianceType !== Component::COMPLIANCE_TYPE_OPT_IN) {
            return;
        }

        $this->pluginOptions['content']['dismiss'] = Yii::t('cookieconsent/widget', 'deny');
    }
}
