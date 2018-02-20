<?php

namespace albertborsos\cookieconsent;

use albertborsos\cookieconsent\widgets\CookieWidget;
use yii\base\InvalidArgumentException;

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

    const CATEGORY_SESSION = 'session';
    const CATEGORY_ADS = 'ads';
    const CATEGORY_USAGE_HELPER = 'usage-helper';
    const CATEGORY_PERFORMANCE = 'performance';

    public $categories = [];

    /**
     * @var string compliance type
     */
    protected $_type;

    /**
     * @var string widget status
     */
    protected $_status;

    /**
     * @param array $config
     * @throws \Exception
     */
    public function registerWidget($config = [])
    {
        CookieWidget::widget($config);
    }

    /**
     * @param $status string
     */
    public function setStatus($status)
    {
        if (empty($status)) {
            return;
        }

        if (!in_array($status, self::STATUSES)) {
            throw new InvalidArgumentException('Invalid value passed to setStatus method!');
        }

        $this->_status = $status;
    }

    public function setType($type)
    {
        if (!in_array($type, self::COMPLIANCE_TYPES)) {
            throw new InvalidArgumentException('Invalid value passed to setType method!');
        }

        $this->_type = $type;
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
        return $this->_status === self::STATUS_DENIED;
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
        return $this->_status === self::STATUS_DISMISSED;
    }

    /**
     * This means user accepts cookies if compliance type is:
     *  - `opt-in`
     *
     * @return bool
     */
    protected function isStatusAllowed()
    {
        return $this->_status === self::STATUS_ALLOWED;
    }

    /**
     * @param null|string $category
     * @return bool
     */
    public function isAllowed($category = null)
    {
        switch ($this->_type) {
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
}
