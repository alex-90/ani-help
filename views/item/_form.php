<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Item $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'category_id')->textInput() ?>

    <?= $form->field($model, 'before_dt')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sum_need')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'imageFile')->widget(FileInput::class, [
        'options' => ['accept' => 'image/*', 'multiple' => true],
        // 'bsVersion' => '5.x', // ОБЯЗАТЕЛЬНО укажите версию, которую установили        
        'pluginOptions' => [
            'showPreview' => true,
            'showCaption' => false,
            'showRemove' => false,
            'showUpload' => true,        // показывает отдельную кнопку "Upload" для AJAX
            'uploadUrl' => Url::toRoute(['item/upload', 'id' => $model->id]), // ваш ajax-эндпоинт
            'uploadAsync' => true,
            'maxFileSize' => 5000, // в КБ
            'allowedFileExtensions' => ['jpg', 'png', 'gif'],
        ],
        'pluginEvents' => [
            'fileuploaded' => 'function(event, data, previewId, index) {
                console.log("uploaded", data.response);
            }',
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
