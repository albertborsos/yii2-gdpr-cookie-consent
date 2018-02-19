<?php

namespace albertborsos\cookieconsent\widgets;

use albertborsos\cookieconsent\assets\CookieConsentAsset;
use Yii;
use yii\base\Widget;
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

    const COMPLIANCE_TYPE_JUST_TELL = null;
    const COMPLIANCE_TYPE_OPT_IN = 'opt-in';
    const COMPLIANCE_TYPE_OPT_OUT = 'opt-out';

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
     * @var array
     */
    public $paletteConfig = [];

    /**
     * @var string
     */
    public $layout = self::LAYOUT_BLOCK;

    /**
     * @var null|string
     */
    public $complianceType = self::COMPLIANCE_TYPE_JUST_TELL;

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

    public function init()
    {
        parent::init();
        $this->preparePluginOptions();
        $this->registerAssets();
    }

    private function preparePluginOptions()
    {
        if (!empty($this->position)) {
            $this->pluginOptions['position'] = $this->position;

            if ($this->position === self::POSITION_TOP_PUSHDOWN) {
                $this->pluginOptions['position'] = self::POSITION_TOP;
                $this->pluginOptions['static'] = true;
            }
        }

        if (empty(ArrayHelper::getValue($this->pluginOptions, 'palette'))) {
            $this->pluginOptions['palette'] = self::DEFAULT_PALETTE;
        }

        if (!empty($this->paletteConfig)) {
            $this->pluginOptions['palette'] = $this->paletteConfig;
        }

        if (!empty($this->layout)) {
            $this->pluginOptions['layout'] = $this->layout;
        }

        if (!empty($this->complianceType)) {
            $this->pluginOptions['type'] = $this->complianceType;
        }

        if (!empty($this->message)) {
            $this->pluginOptions['content']['message'] = $this->message;
        }

        if (!empty($this->dismissButtonText)) {
            $this->pluginOptions['content']['dismiss'] = $this->dismissButtonText;
        }

        if ($this->complianceType === self::COMPLIANCE_TYPE_OPT_OUT && !empty($this->denyButtonText)) {
            $this->pluginOptions['content']['deny'] = $this->denyButtonText;
        } else {
            unset($this->pluginOptions['content']['deny']);
        }

        if ($this->complianceType === self::COMPLIANCE_TYPE_OPT_IN && !empty($this->allowButtonText)) {
            $this->pluginOptions['content']['allow'] = $this->allowButtonText;
        } else {
            unset($this->pluginOptions['content']['allow']);
        }

        if (!empty($this->policyLinkText)) {
            $this->pluginOptions['content']['policy'] = $this->policyLinkText;
        }

        if (!empty($this->policyLink)) {
            $this->pluginOptions['content']['href'] = Url::to($this->policyLink, true);
        }
    }

    private function registerAssets()
    {
        if (!Yii::$app instanceof \yii\web\Application) {
            return;
        }

        $view = Yii::$app->getView();
        CookieConsentAsset::register($view);
        $view->registerJs('window.cookieconsent.initialise(' . Json::encode($this->pluginOptions) . ');', View::POS_READY);
    }
}
