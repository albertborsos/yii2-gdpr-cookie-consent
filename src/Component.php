<?php

namespace albertborsos\cookieconsent;

use albertborsos\cookieconsent\widgets\CookieWidget;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;

class Component extends \yii\base\Component
{

    const STATUS_DENIED = 'deny';
    const STATUS_DISMISSED = 'dismiss';
    const STATUS_ALLOWED = 'allow';

    const STATUSES = [
        self::STATUS_DENIED,
        self::STATUS_DISMISSED,
        self::STATUS_ALLOWED,
    ];

    const COMPLIANCE_TYPE_INFO = 'info';
    const COMPLIANCE_TYPE_OPT_IN = 'opt-in';
    const COMPLIANCE_TYPE_OPT_OUT = 'opt-out';

    const COMPLIANCE_TYPES = [
        self::COMPLIANCE_TYPE_INFO,
        self::COMPLIANCE_TYPE_OPT_IN,
        self::COMPLIANCE_TYPE_OPT_OUT,
    ];

    const CATEGORY_SESSION      = 'session';
    const CATEGORY_ADS          = 'ads';
    const CATEGORY_USAGE_HELPER = 'usage-helper';
    const CATEGORY_PERFORMANCE  = 'performance';
    const CATEGORY_BEHAVIOR     = 'behavior';

    const CATEGORIES = [
        self::CATEGORY_SESSION,
        self::CATEGORY_ADS,
        self::CATEGORY_USAGE_HELPER,
        self::CATEGORY_PERFORMANCE,
        self::CATEGORY_BEHAVIOR,
    ];

    const COOKIE_OPTION_PREFIX = 'cookieconsent_option_';

    /**
     * @var array custom cookie categories
     */
    public $extraCategories = [];

    /**
     * @var string compliance type
     */
    public $complianceType;

    /**
     * @var boolean calculated by the compliance type
     */
    private $_defaultCookieValue;

    public function init()
    {
        if (!in_array($this->complianceType, self::COMPLIANCE_TYPES)) {
            throw new InvalidArgumentException('Invalid value in "type" property!');
        }

        $this->setDefaultCookieValue();
        parent::init();
    }

    /**
     * @param array $config
     * @throws \Exception
     */
    public function registerWidget($config = [])
    {
        CookieWidget::widget($config);
    }

    public function getCategories()
    {
        return ArrayHelper::merge(self::CATEGORIES, $this->extraCategories);
    }

    public function getStatus()
    {
        return ArrayHelper::getValue($_COOKIE, 'cookieconsent_status');
    }

    /**
     * This means user NEVER accepts cookies!
     *
     * This means user refuses cookies if compliance type is:
     *  - `opt-out`
     *
     * @return bool
     */
    protected function isStatusDenied()
    {
        return $this->getStatus() === self::STATUS_DENIED;
    }

    /**
     * This means user accepts cookies if compliance type is:
     *  - `opt-out`
     *  - `info`
     *
     * This means user refuses cookies if compliance type is:
     *  - `opt-in`
     *
     * @return bool
     */
    protected function isStatusDismissed()
    {
        return $this->getStatus() === self::STATUS_DISMISSED;
    }

    /**
     * This means user accepts cookies if compliance type is:
     *  - `opt-in`
     *
     * @return bool
     */
    protected function isStatusAllowed()
    {
        return $this->getStatus() === self::STATUS_ALLOWED;
    }

    /**
     * @param null|string $category
     * @return bool
     */
    public function isAllowed($category = null)
    {
        if ($category) {
            return \Yii::$app->request->cookies->getValue(self::COOKIE_OPTION_PREFIX . $category, $this->getDefaultCookieValue());
        }

        // global status
        switch ($this->complianceType) {
            case self::COMPLIANCE_TYPE_INFO:
                return $this->isStatusDismissed();
                break;
            case self::COMPLIANCE_TYPE_OPT_OUT:
                return $this->isStatusDenied();
                break;
            case self::COMPLIANCE_TYPE_OPT_IN:
                return $this->isStatusAllowed();
                break;
        }

        return false;
    }

    private function setDefaultCookieValue()
    {
        switch ($this->complianceType) {
            case self::COMPLIANCE_TYPE_INFO:
            case self::COMPLIANCE_TYPE_OPT_OUT:
                $this->_defaultCookieValue = true;
                break;
            default:
                $this->_defaultCookieValue = false;
                break;
        }
    }

    public function getDefaultCookieValue()
    {
        return $this->_defaultCookieValue;
    }
}
