<?php
/** @var $this \yii\web\View */
/** @var $form \albertborsos\cookieconsent\domains\forms\CookieSettingsForm */
/** @var $categories array */
?>
<?php $form = \yii\bootstrap\ActiveForm::begin([
    'id' => 'cookie-settings',
    'layout' => 'horizontal',
    'enableAjaxValidation' => true,
    'validateOnType' => true,
    'fieldConfig' => [
        'template' => '{label}{beginWrapper}{input}{error}{hint}{endWrapper}',
        'horizontalCssClasses' => [
            'label' => 'col-xs-3',
            'offset' => 'col-xs-offset-3',
            'wrapper' => 'col-xs-9',
            'error' => '',
            'hint' => '',
        ],
    ],
]) ?>
<?php foreach ($categories as $category): ?>
    <?= $form->field($model, 'options[' . $category . ']')
        ->widget(\dosamigos\switchinput\SwitchBox::class, [
            'model' => $model,
            'attribute' => 'options[' . $category . ']',
            'inlineLabel' => false,
            'clientOptions' => [
                'onText' => Yii::t('cookieconsent/form', 'input.allowed'),
                'offText' => Yii::t('cookieconsent/form', 'input.disallowed'),
                'size' => 'large',
                'onColor' => 'success',
                'offColor' => 'danger',
            ],
        ])
        ->label($model->getAttributeLabel($category), ['class' => 'control-label col-xs-3 text-right'])
        ->hint($model->getAttributeHint($category)) ?>
<?php endforeach; ?>
<div class="row">
    <div class="col-xs-offset-3">
        <?= \yii\helpers\Html::submitButton(Yii::t('cookieconsent/widget', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= \yii\helpers\Html::a(Yii::t('cookieconsent/widget', 'Reset to default'), \yii\helpers\Url::to(['/cookie-beallitasok'], true), [
                'class' => 'btn btn-default cc-revoke-custom',
        ]) ?>
    </div>
</div>
<?php \yii\bootstrap\ActiveForm::end() ?>
