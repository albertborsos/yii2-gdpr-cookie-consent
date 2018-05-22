<?php
/** @var $this \yii\web\View */
/** @var $documents []] */
?>
<h3><?= Yii::t('cookieconsent/policy', 'Privacy Policy')?></h3>

<table class="table table-striped">
    <tr><th><?= Yii::t('cookieconsent/policy', 'Available Documents')?></th></tr>
    <?php foreach ($documents as $document):?>
    <tr>
        <td><?= \yii\helpers\Html::a(Yii::t('cookieconsent/policy', $document['name']), $document['url'], [
            'target' => '_blank',
        ])?></td>
    </tr>
    <?php endforeach; ?>
</table>
