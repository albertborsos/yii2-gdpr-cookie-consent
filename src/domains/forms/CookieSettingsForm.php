<?php

namespace albertborsos\cookieconsent\domains\forms;

use albertborsos\cookieconsent\Component;
use albertborsos\ddd\interfaces\FormObject;
use yii\base\Model;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

class CookieSettingsForm extends Model implements FormObject
{

    /**
     * category-state key-value pairs filled from the form
     *
     * @var array
     */
    public $options = [];

    /**
     * @var Component
     */
    private $_component;

    /**
     * CookieSettingsForm constructor.
     * @param array $config
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        /** @var Component $component */
        $component = Instance::ensure('cookieConsent', Component::class);
        $this->setComponent($component);
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
            [['options'], 'optionIsRequired', 'params' => ['category' => Component::CATEGORY_SESSION]],
            [['options'], 'optionIsRequired', 'params' => ['category' => Component::CATEGORY_USAGE_HELPER]],
        ];
    }

    public function attributeLabels()
    {
        $extraCategories = ArrayHelper::map($this->getComponent()->extraCategories, 'id', 'label');
        return ArrayHelper::merge([
            Component::CATEGORY_SESSION      => \Yii::t('cookieconsent/form', 'label.session'),
            Component::CATEGORY_USAGE_HELPER => \Yii::t('cookieconsent/form', 'label.usage_helper'),
            Component::CATEGORY_SOCIAL       => \Yii::t('cookieconsent/form', 'label.social'),
            Component::CATEGORY_STATISTICS   => \Yii::t('cookieconsent/form', 'label.statistics'),
            Component::CATEGORY_ADS          => \Yii::t('cookieconsent/form', 'label.ads'),
            Component::CATEGORY_BEHAVIOR     => \Yii::t('cookieconsent/form', 'label.behavior'),
        ], $extraCategories);
    }

    public function attributeHints()
    {
        $extraCategories = ArrayHelper::map($this->getComponent()->extraCategories, 'id', 'hint');
        return ArrayHelper::merge([
            Component::CATEGORY_SESSION      => \Yii::t('cookieconsent/form', 'hint.session'),
            Component::CATEGORY_USAGE_HELPER => \Yii::t('cookieconsent/form', 'hint.usage_helper'),
            Component::CATEGORY_SOCIAL       => \Yii::t('cookieconsent/form', 'hint.social'),
            Component::CATEGORY_STATISTICS   => \Yii::t('cookieconsent/form', 'hint.statistics'),
            Component::CATEGORY_ADS          => \Yii::t('cookieconsent/form', 'hint.ads'),
            Component::CATEGORY_BEHAVIOR     => \Yii::t('cookieconsent/form', 'hint.behavior'),
        ], $extraCategories);
    }

    public function optionIsRequired($attribute, $params)
    {
        $category = $params['category'];
        $optionCategory = ArrayHelper::getValue($this->{$attribute}, $category);

        if (!$optionCategory) {
            $this->addError($attribute . '.' . $category, \Yii::t('cookieconsent/widget', 'form.error.' . $category . '-is-required'));
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
