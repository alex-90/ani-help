<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Donate $model */

$this->title = 'Create Donate';
$this->params['breadcrumbs'][] = ['label' => 'Donates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="donate-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
