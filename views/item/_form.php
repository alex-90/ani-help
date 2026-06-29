<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;
use app\models\Category;
use app\models\ItemStatus;

/** @var yii\web\View $this */
/** @var app\models\Item $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status_id')->dropDownList(
        ArrayHelper::map(ItemStatus::find()->orderBy('name')->all(), 'id', 'name'),
        ['prompt' => 'Выберите статус']
    ) ?>

    <?= $form->field($model, 'category_id')->dropDownList(
        ArrayHelper::map(Category::find()->orderBy('name')->all(), 'id', 'name'),
        ['prompt' => 'Выберите категорию']
    ) ?>

    <?= $form->field($model, 'before_dt')->input('date', ['style' => 'max-width: 200px']) ?>

    <?= $form->field($model, 'sum_need')->textInput(['maxlength' => true, 'style' => 'max-width: 200px']) ?>

    <?php if ($model->isNewRecord): ?>
        <div class="alert alert-info">
            Сохраните сбор, чтобы загрузить изображения.
        </div>
    <?php else: ?>
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
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
