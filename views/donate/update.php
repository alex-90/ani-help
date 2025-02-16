<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Donate $model */

$this->title = 'Update Donate: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Donates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="donate-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
