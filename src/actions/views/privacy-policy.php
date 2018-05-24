<?php
/** @var $this \yii\web\View */
/** @var $documents [] */
/** @var $email string */
?>
<h3><?= Yii::t('cookieconsent/policy', 'Privacy Policy')?></h3>

<table class="table table-striped">
    <tr><th><?= Yii::t('cookieconsent/policy', 'Available documents')?></th></tr>
    <?php if ($email): ?>
    <p><?= Yii::t('cookieconsent/policy', 'description', ['email' => $email])?></p>
    <?php endif; ?>
    <?php foreach ($documents as $document):?>
    <tr>
        <td><?= \yii\helpers\Html::a(Yii::t('cookieconsent/policy', $document['name']), $document['url'], [
            'target' => '_blank',
        ])?></td>
    </tr>
    <?php endforeach; ?>
</table>
