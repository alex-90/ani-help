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

// Опубликованный сбор редактируется ограниченно: заблокированы все поля,
// кроме суммы и дозаписи текста; картинки можно добавлять.
$restricted = !$model->isNewRecord && $model->published;
?>

<div class="item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($restricted): ?>
        <div class="alert alert-info">
            Сбор опубликован. Менять можно только сумму, добавлять текст (в конец описания)
            и загружать изображения.
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'disabled' => $restricted]) ?>

    <?php if ($restricted): ?>
        <?= $form->field($model, 'description')->textarea(['rows' => 6, 'disabled' => true]) ?>
        <?= $form->field($model, 'appendText')->textarea([
            'rows' => 4,
            'placeholder' => 'Новый текст добавится в конец описания после пометки UPD',
        ])->label('Дописать') ?>
    <?php else: ?>
        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    <?php endif; ?>

    <?= $form->field($model, 'status_id')->dropDownList(
        ArrayHelper::map(ItemStatus::find()->orderBy('name')->all(), 'id', 'name'),
        ['prompt' => 'Выберите статус', 'style' => 'max-width: 200px', 'disabled' => $restricted]
    ) ?>

    <?= $form->field($model, 'category_id')->dropDownList(
        ArrayHelper::map(Category::find()->orderBy('name')->all(), 'id', 'name'),
        ['prompt' => 'Выберите категорию', 'style' => 'max-width: 200px', 'disabled' => $restricted]
    ) ?>

    <?= $form->field($model, 'before_dt')->input('date', ['style' => 'max-width: 200px', 'disabled' => $restricted]) ?>

    <?= $form->field($model, 'sum_need')->textInput(['maxlength' => true, 'style' => 'max-width: 200px']) ?>

    <?php if ($model->isNewRecord): ?>
        <div class="alert alert-info">
            Сохраните сбор, чтобы загрузить изображения.
        </div>
    <?php else: ?>
        <?php
        // уже загруженные изображения сбора
        $existingImages = $model->getImages()->orderBy('position')->all();
        $initialPreview = [];
        $initialPreviewConfig = [];
        foreach ($existingImages as $img) {
            $initialPreview[] = $img->url;
            $initialPreviewConfig[] = [
                'caption' => $img->title,
                'key' => $img->id,
                'url' => Url::toRoute(['item/delete-image', 'id' => $img->id]),
            ];
        }
        ?>
        <?php echo $form->field($model, 'imageFile')->widget(FileInput::class, [
            'options' => ['accept' => 'image/*', 'multiple' => true],
            // 'bsVersion' => '5.x', // ОБЯЗАТЕЛЬНО укажите версию, которую установили
            'pluginOptions' => [
                'browseLabel' => 'Загрузить фото',
                'showPreview' => true,
                'showCaption' => false,
                'showRemove' => false,
                'showUpload' => false,       // кнопка Upload не нужна — грузим автоматически
                'uploadUrl' => Url::toRoute(['item/upload', 'id' => $model->id]), // ваш ajax-эндпоинт
                'uploadAsync' => true,
                'maxFileSize' => 5000, // в КБ
                'allowedFileExtensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'dropZoneEnabled' => false, // скрываем блок "Drag & drop files here …"
                'overwriteInitial' => false,
                // показываем уже загруженные фото
                'initialPreview' => $initialPreview,
                'initialPreviewAsData' => true,
                'initialPreviewConfig' => $initialPreviewConfig,
                'previewSettings' => [
                    // мелкие квадратные превью
                    'image' => ['width' => '100px', 'height' => '100px'],
                ],
            ],
            'pluginEvents' => [
                // автозагрузка сразу после выбора файлов
                'filebatchselected' => 'function(event, files) {
                    $(this).fileinput("upload");
                }',
                'fileuploaded' => 'function(event, data, previewId, index) {
                    console.log("uploaded", data.response);
                }',
                // сохраняем новый порядок после drag&drop
                'filesorted' => 'function(event, params) {
                    var data = {};
                    data[yii.getCsrfParam()] = yii.getCsrfToken();
                    data.order = $.map(params.stack, function(f) { return f.key; });
                    $.post("' . Url::toRoute(['item/reorder-images', 'id' => $model->id]) . '", data);
                }',
            ],
        ])->label(false); ?>

        <?php
        // мелкие квадратные превью в виде аккуратной сетки
        $this->registerCss('
            /* убираем некрасивую рамку/фон контейнера .file-preview */
            .item-form .file-preview {
                border: 0;
                padding: 0;
                background: transparent;
                box-shadow: none;
            }
            /* прячем угловой крестик, очищающий весь инпут */
            .item-form .fileinput-remove {
                display: none !important;
            }
            /* прячем длинный общий статус-бар "Done" внизу */
            .item-form .file-preview-status,
            .item-form .kv-upload-progress {
                display: none !important;
            }
            .item-form .file-preview-thumbnails {
                display: flex;
                flex-wrap: wrap;
                gap: .5rem;
            }
            /* стиль Bootstrap .img-thumbnail */
            .item-form .file-preview-frame {
                margin: 0 !important;
                padding: .25rem;
                background-color: var(--bs-body-bg, #fff);
                border: 1px solid var(--bs-border-color, #dee2e6);
                border-radius: .375rem;
                box-shadow: none;
                transition: box-shadow .15s ease-in-out;
            }
            .item-form .file-preview-frame:hover {
                box-shadow: 0 .25rem .75rem rgba(0,0,0,.1);
            }
            .item-form .file-preview-frame .kv-file-content {
                width: 100px;
                height: 100px;
            }
            .item-form .file-preview-frame img {
                width: 100px !important;
                height: 100px !important;
                object-fit: cover;
                border-radius: .25rem;
            }
        ');
        ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
