<?php

namespace albertborsos\cookieconsent;

use albertborsos\cookieconsent\helpers\CookieHelper;
use albertborsos\cookieconsent\interfaces\CategoryInterface;
use albertborsos\cookieconsent\interfaces\CookieComponentInterface;
use albertborsos\cookieconsent\widgets\CookieWidget;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;

class Component extends \yii\base\Component implements CategoryInterface, CookieComponentInterface
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

    const COOKIE_OPTION_PREFIX = 'cookieconsent_option_';
    const COOKIECONSENT_STATUS = 'cookieconsent_status';

    /**
     * Suggested format in config:
     *
     * ```
     *  'extraCategories' => [
     *      'customCategory' => [
     *          'label' => 'Custom Category',
     *          'hint' => 'Description of the Custom category.',
     *      ],
     *  ],
     * ```
     *
     * @var array custom cookie categories
     */
    public $extraCategories = [];

    /**
     * Categories to hide from settings form.
     * `session` and `usagehelper` categories will be ignored from this list.
     *
     * ```
     * 'disabledCategories' => [
     *     \albertborsos\cookieconsent\helpers\CookieHelper::CATEGORY_BEHAVIOR,
     * ],
     * ```
     *
     * @var array
     */
    public $disabledCategories = [];

    /**
     * @var string compliance type
     */
    public $complianceType = self::COMPLIANCE_TYPE_OPT_IN;

    /**
     * Configure to show or hide cookie policy floating revoke button after accept or decline cookie consent.
     *
     * @var bool
     */
    public $showCookiePolicyFloatingTab = true;

    /**
     * Expiration time of the cookie categories settings.
     * Cookie settings will be stored with the following expiration time: time() + $expirationTime
     *
     * this value will be passed to `setcookie()` method.
     *
     * @var float|int
     */
    public $cookieExpire = 60 * 60 * 24 * 365;

    /**
     * this value will be passed to `setcookie()` method.
     * @var string
     */
    public $cookiePath = '/';

    /**
     * this value will be passed to `setcookie()` method.
     * @var string
     */
    public $cookieDomain = '';

    /**
     * this value will be passed to `setcookie()` method.
     * @var string
     */
    public $cookieSecure = false;

    /**
     * this value will be passed to `setcookie()` method.
     * @var string
     * @deprecated since `1.2.4` because if it has a `true` value then CookieConsent library cannot manage the cookies from the frontend it they are set via form submission
     */
    public $cookieHttpOnly = false;

    /**
     * Link to your cookie setting URL.
     *
     * @var string|array
     */
    public $urlSettings;

    /**
     * Link to your privacy policy/data protection URL.
     *
     * @var string|array
     */
    public $urlPrivacyPolicy;

    /**
     * List of the uploaded documents for `PrivacyPolicyAction`.
     * The list items should be in the following structure:
     *
     * ```
     *  'documents' => [
     *      ['name' => 'Privacy Policy', 'url' => ['/uploads/privacy-policy-2018-05-25.pdf']],
     *      ['name' => 'Terms And Conditions', 'url' => ['/uploads/terms-and-conditions-2018-05-25.pdf']],
     *  ],
     * ```
     *
     * `name` will be a translate key in `cookieconsent/policy`
     * `url` will be converted to an URL with `yii\helpers\Url::to()` method
     *
     * @var array
     */
    public $documents = [];

    /**
     * Relevant contact e-mail address to show on the privacy policy page.
     *
     * @var string email address
     */
    public $email;

    /**
     * @var boolean calculated by the compliance type
     */
    private $_defaultCookieValue;

    /**
     * @var string value of `cookieconsent_status` cookie
     */
    private $_status;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!in_array($this->complianceType, self::COMPLIANCE_TYPES)) {
            throw new InvalidArgumentException('Invalid value in "type" property!');
        }

        $this->setStatus();
        $this->generateUrls();
        $this->calculateDefaultCookieValue();
        $this->normalizeExtraCategories();
        $this->normalizeDisabledCategories();
        $this->hideCookiePolicyFloatingTab();
        $this->checkDocuments();
        parent::init();
    }

    /**
     * @return $this
     */
    public function getComponent()
    {
        return $this;
    }

    /**
     * @param array $config
     * @throws \Exception
     */
    public function registerWidget($config = [])
    {
        CookieWidget::widget($config);
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        $categories = ArrayHelper::merge(self::CATEGORIES, array_keys($this->extraCategories));

        return array_diff($categories, $this->disabledCategories);
    }

    /**
     * @param null $status
     */
    public function setStatus($status = null)
    {
        $this->_status = $status ?: ArrayHelper::getValue($_COOKIE, self::COOKIECONSENT_STATUS);
    }

    /**
     * @return mixed|null
     */
    public function getStatus()
    {
        if (empty($this->_status)) {
            $this->setStatus();
        }
        return $this->_status;
    }

    /**
     * @return string
     */
    public function getSettingsHash()
    {
        foreach (CookieHelper::MAPPING as $category => $_types) {
            $options[$category] = $this->isAllowedCategory($category);
        }

        return implode('-', $options);
    }

    /**
     * @throws InvalidConfigException
     */
    private function checkDocuments()
    {
        foreach ($this->documents as $i => $document) {
            if (!isset($document['name']) || !isset($document['url'])) {
                throw new InvalidConfigException('Invalid item format in ' . static::class . '::documents');
            }
        }
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
     * @return bool
     */
    public function isOptOut()
    {
        return $this->complianceType === self::COMPLIANCE_TYPE_OPT_OUT;
    }

    /**
     * @return bool
     */
    public function isOptIn()
    {
        return $this->complianceType === self::COMPLIANCE_TYPE_OPT_IN;
    }

    /**
     * @return bool
     */
    public function isInfo()
    {
        return $this->complianceType === self::COMPLIANCE_TYPE_INFO;
    }

    /**
     * @param null|string $category
     * @return bool
     */
    public function isAllowed($category = null)
    {
        if (!\Yii::$app instanceof \yii\web\Application) {
            return false;
        }

        if ($category) {
            return $this->isAllowedCategory($category);
        }

        // global status
        switch ($this->complianceType) {
            case self::COMPLIANCE_TYPE_INFO:
            case self::COMPLIANCE_TYPE_OPT_OUT:
                return $this->isStatusDismissed();
                break;
            case self::COMPLIANCE_TYPE_OPT_IN:
                return $this->isStatusAllowed();
                break;
        }

        return false;
    }

    /**
     * @param null $category
     * @return bool
     */
    public function isAllowedCategory($category)
    {
        if (!\Yii::$app instanceof \yii\web\Application) {
            return false;
        }

        if ($this->isRequiredToAllow($category)) {
            return true;
        }
        return ArrayHelper::getValue($_COOKIE, self::COOKIE_OPTION_PREFIX . $category, $this->getDefaultCookieValue());
    }

    /**
     * @param null $type
     * @return bool
     * @throws InvalidConfigException
     */
    public function isAllowedType($type)
    {
        if (!\Yii::$app instanceof \yii\web\Application) {
            return false;
        }

        return CookieHelper::isAllowedType($type);
    }

    /**
     * @return bool
     */
    public function isAnswered()
    {
        return $this->getStatus() !== null;
    }

    private function calculateDefaultCookieValue()
    {
        switch ($this->complianceType) {
            case self::COMPLIANCE_TYPE_INFO:
            case self::COMPLIANCE_TYPE_OPT_OUT:
                $this->_defaultCookieValue = $this->isAnswered() ? $this->isAllowed() : true;
                break;
            case self::COMPLIANCE_TYPE_OPT_IN:
                // while it is not allowed, it is false
                $this->_defaultCookieValue = $this->isAnswered() ? $this->isAllowed() : false;
                break;
        }
    }

    public function getNotAllowedTypeByComplianceType()
    {
        switch ($this->complianceType) {
            case self::COMPLIANCE_TYPE_INFO:
            case self::COMPLIANCE_TYPE_OPT_OUT:
                return self::STATUS_DISMISSED;
                break;
            case self::COMPLIANCE_TYPE_OPT_IN:
                return self::STATUS_DENIED;
                break;
        }
    }

    /**
     * @return bool
     */
    public function getDefaultCookieValue()
    {
        return $this->_defaultCookieValue;
    }

    private function normalizeExtraCategories()
    {
        foreach ($this->extraCategories as $id => $data) {
            if (!is_array($data) && is_int($id)) {
                unset($this->extraCategories[$id]);
                $id = $data;
                $data = [];
            }

            if (in_array($id, self::CATEGORIES)) {
                throw new InvalidConfigException('You cannot use "' . $id . '" default category in "extraCategories" property items.');
            }

            if (isset($data['id'])) {
                throw new InvalidConfigException('Do not set "id" for "extraCategories" property items.');
            }

            if (preg_match('/[^A-Za-z]+/', $id)) {
                throw new InvalidConfigException('Category names must contains only word characters.');
            }

            $this->extraCategories[$id] = [
                'id' => $id,
                'label' => ArrayHelper::getValue($data, 'label', Inflector::humanize($id)),
                'hint' => ArrayHelper::getValue($data, 'hint', Inflector::humanize($id)),
            ];
        }
    }

    private function normalizeDisabledCategories()
    {
        foreach (self::CATEGORIES_REQUIRED as $requiredCategory) {
            ArrayHelper::removeValue($this->disabledCategories, $requiredCategory);
        }
    }

    public function isRequiredToAllow($category)
    {
        return in_array($category, self::CATEGORIES_REQUIRED);
    }

    private function hideCookiePolicyFloatingTab()
    {
        if ($this->showCookiePolicyFloatingTab) {
            return;
        }

        \Yii::$app->view->registerCss('.cc-revoke{display:none;}', ['type' => 'text/css']);
    }

    private function generateUrls()
    {
        if (!\Yii::$app instanceof \yii\web\Application) {
            return;
        }

        $this->urlSettings = Url::to($this->urlSettings, true);
        $this->urlPrivacyPolicy = Url::to($this->urlPrivacyPolicy, true);
    }

    public function removeCookieConfig($cookieName)
    {
        $cookieParts = [
            $cookieName => '',
            'Domain' => $this->getComponent()->cookieDomain,
            'Path' => $this->getComponent()->cookiePath,
            'Secure' => $this->getComponent()->cookieSecure,
            'Expires' => 'Thu, 01 Jan 1970 00:00:01 GMT',
        ];

        $settings = '';
        foreach ($cookieParts as $attribute => $value) {
            if ($value === false) {
                continue;
            }

            if ($value === true) {
                $settings .= ' ' . $attribute . ';';
            } else {
                $settings .= ' ' . $attribute . '=' . $value . ';';
            }
        }

        return "'" . trim($settings) . "'";
    }
}
