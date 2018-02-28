<?php

namespace albertborsos\cookieconsent\domains\forms;

use albertborsos\cookieconsent\Component;
use albertborsos\ddd\interfaces\FormObject;
use yii\base\Model;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

class CookieSettingsForm extends Model implements FormObject
{
    public $options = [];

    /**
     * @var Component
     */
    private $_component;

    public function __construct(array $config = [])
    {
        $this->setComponent(Instance::ensure('cookieConsent', Component::class));
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        $this->setDefaultStatesByType();
    }

    public function rules()
    {
        return [
            [['options'], 'each', 'rule' => ['boolean']],
            [['options'], 'sessionIsRequired'],
            [['options'], 'usageHelperIsRequired', 'when' => function () {
                if ($this->getComponent()->isOptOut()) {
                    return $this->getComponent()->isAllowed() ? $this->hasDisallowedCategory() : $this->hasAllowedCategory();
                } elseif ($this->getComponent()->isOptIn()) {
                    return $this->getComponent()->isAllowed() ? $this->hasAllowedCategory() : $this->hasDisallowedCategory();
                }
                // info type
                return false;
            }],
        ];
    }

    /**
     * @return bool
     */
    public function hasAllowedCategory()
    {
        foreach ($this->options as $key => $value) {
            if ($value == 1 && !$this->getComponent()->isRequiredToAllow($key)) {
                return true;
            }
        }

        return false;
    }
    /**
     * @return bool
     */
    public function hasDisallowedCategory()
    {
        foreach ($this->options as $key => $value) {
            if ($value == 0) {
                return true;
            }
        }

        return false;
    }

    public function attributeLabels()
    {
        $extraCategories = ArrayHelper::map($this->getComponent()->extraCategories, 'id', 'label');
        return ArrayHelper::merge([
            Component::CATEGORY_SESSION      => \Yii::t('cookieconsent/form', 'label.session'),
            Component::CATEGORY_ADS          => \Yii::t('cookieconsent/form', 'label.ads'),
            Component::CATEGORY_BEHAVIOR     => \Yii::t('cookieconsent/form', 'label.behavior'),
            Component::CATEGORY_PERFORMANCE  => \Yii::t('cookieconsent/form', 'label.performance'),
            Component::CATEGORY_USAGE_HELPER => \Yii::t('cookieconsent/form', 'label.usage-helper'),
        ], $extraCategories);
    }

    public function attributeHints()
    {
        $extraCategories = ArrayHelper::map($this->getComponent()->extraCategories, 'id', 'hint');
        return ArrayHelper::merge([
            Component::CATEGORY_SESSION      => \Yii::t('cookieconsent/form', 'hint.session'),
            Component::CATEGORY_ADS          => \Yii::t('cookieconsent/form', 'hint.ads'),
            Component::CATEGORY_BEHAVIOR     => \Yii::t('cookieconsent/form', 'hint.behavior'),
            Component::CATEGORY_PERFORMANCE  => \Yii::t('cookieconsent/form', 'hint.performance'),
            Component::CATEGORY_USAGE_HELPER => \Yii::t('cookieconsent/form', 'hint.usage-helper'),
        ], $extraCategories);
    }

    public function sessionIsRequired($attribute)
    {
        $sessionCategory = ArrayHelper::getValue($this->{$attribute}, Component::CATEGORY_SESSION);

        if (!$sessionCategory) {
            $this->addError($attribute . '.' . Component::CATEGORY_SESSION, \Yii::t('cookieconsent/widget', 'form.error.session-is-required'));
        }
    }

    public function usageHelperIsRequired($attribute)
    {
        $usageHelperCategory = ArrayHelper::getValue($this->{$attribute}, Component::CATEGORY_USAGE_HELPER);

        if (!$usageHelperCategory) {
            $this->addError($attribute . '.' . Component::CATEGORY_USAGE_HELPER, \Yii::t('cookieconsent/widget', 'form.error.usage-helper-is-required'));
        }
    }

    private function setDefaultStatesByType()
    {
        foreach ($this->getComponent()->getCategories() as $category) {
            $this->options[$category] = $this->getComponent()->isAllowed($category);
        }
    }

    /**
     * @return Component|object
     */
    private function getComponent()
    {
        return $this->_component;
    }

    private function setComponent(Component $component)
    {
        $this->_component = $component;
    }
}
