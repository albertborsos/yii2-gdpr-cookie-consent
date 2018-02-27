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
        $this->_component = Instance::ensure('cookieConsent', Component::class);
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
        ];
    }

    public function attributeLabels()
    {
        return [
            Component::CATEGORY_SESSION      => \Yii::t('cookieconsent/form', 'label.session'),
            Component::CATEGORY_ADS          => \Yii::t('cookieconsent/form', 'label.ads'),
            Component::CATEGORY_BEHAVIOR     => \Yii::t('cookieconsent/form', 'label.behavior'),
            Component::CATEGORY_PERFORMANCE  => \Yii::t('cookieconsent/form', 'label.performance'),
            Component::CATEGORY_USAGE_HELPER => \Yii::t('cookieconsent/form', 'label.usage-helper'),
        ];
    }

    public function attributeHints()
    {
        return [
            Component::CATEGORY_SESSION      => \Yii::t('cookieconsent/form', 'hint.session'),
            Component::CATEGORY_ADS          => \Yii::t('cookieconsent/form', 'hint.ads'),
            Component::CATEGORY_BEHAVIOR     => \Yii::t('cookieconsent/form', 'hint.behavior'),
            Component::CATEGORY_PERFORMANCE  => \Yii::t('cookieconsent/form', 'hint.performance'),
            Component::CATEGORY_USAGE_HELPER => \Yii::t('cookieconsent/form', 'hint.usage-helper'),
        ];
    }

    public function sessionIsRequired($attribute)
    {
        $sessionCategory = ArrayHelper::getValue($this->{$attribute}, Component::CATEGORY_SESSION);

        if (!$sessionCategory) {
            $this->addError($attribute . '.' . Component::CATEGORY_SESSION, \Yii::t('cookieconsent/widget', 'form.error.session-is-required'));
        }
    }

    private function setDefaultStatesByType()
    {
        foreach ($this->_component->getCategories() as $category) {
            $this->options[$category] = $this->_component->isAllowed($category);
        }
    }
}
