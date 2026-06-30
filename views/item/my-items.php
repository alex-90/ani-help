<?php

use app\models\Item;
use app\models\ItemStatus;
use app\models\Reason;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\bootstrap5\BootstrapPluginAsset;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

BootstrapPluginAsset::register($this);

$this->title = 'Мои сборы';
// $this->params['breadcrumbs'][] = $this->title;

$reasonList = ArrayHelper::map(Reason::find()->orderBy('name')->all(), 'id', 'name');
?>
<div class="item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать сбор', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name' => [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->name), ['site/show', 'id' => $model->id]);
                }
            ],
            // 'description:ntext',
            'status' => [
                'attribute' => 'status_id',
                'label' => 'Статус',
                'value' => function ($model) {
                    return $model->status->name;
                }
            ],
            // 'status',
            'created_at',
            'updated_at',
            //'category_id',
            //'before_dt',
            //'created_by_id',
            //'sum',
            // 'published',
            'published' => [
                'attribute' => 'published',
                'label' => '',
                'format' => 'raw',
                'value' => function ($model) {
                    // не опубликован: можно опубликовать и редактировать
                    if (!$model->published) {
                        $publish = Html::button('Опубликовать', [
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'data' => [
                                'bs-toggle' => 'modal',
                                'bs-target' => '#publishItemModal',
                                'id' => $model->id,
                                'name' => $model->name,
                            ],
                        ]);

                        $edit = Html::a(
                            'Редактировать',
                            ['update', 'id' => $model->id],
                            ['class' => 'btn btn-sm btn-primary']
                        );

                        return $publish . ' ' . $edit;
                    }

                    // опубликован и уже остановлен — снять с публикации нельзя
                    if ($model->status_id == ItemStatus::STATUS_CLOSED) {
                        $badge = Html::tag('span', 'Сбор остановлен', ['class' => 'badge bg-secondary']);

                        $reason = $model->reason_id && $model->reason
                            ? $model->reason->name
                            : $model->reason_text;

                        if ($reason) {
                            $badge .= ' ' . Html::tag('em', Html::encode($reason), ['class' => 'text-muted']);
                        }

                        return $badge;
                    }

                    // опубликован: ограниченное редактирование (сумма, дозапись, картинки) или остановка
                    $edit = Html::a(
                        'Обновить сбор',
                        ['update', 'id' => $model->id],
                        ['class' => 'btn btn-sm btn-outline-primary']
                    );

                    $stop = Html::button('Остановить сбор', [
                        'class' => 'btn btn-sm btn-danger js-stop-item',
                        'data' => [
                            'bs-toggle' => 'modal',
                            'bs-target' => '#stopItemModal',
                            'id' => $model->id,
                            'name' => $model->name,
                        ],
                    ]);

                    return $edit . ' ' . $stop;
                }
            ],
        ],
    ]); ?>


</div>

<?php $publishUrl = Url::toRoute(['toggle-published']); ?>
<div class="modal fade" id="publishItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <?= Html::beginForm($publishUrl, 'post', ['class' => 'modal-content', 'id' => 'publish-item-form']) ?>
            <div class="modal-header">
                <h5 class="modal-title">Опубликовать сбор</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p>Опубликовать сбор «<span id="publish-item-name"></span>»?</p>
                <div class="alert alert-warning mb-0">
                    После публикации <strong>редактирование сбора будет невозможно</strong>.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <?= Html::submitButton('Опубликовать', ['class' => 'btn btn-success']) ?>
            </div>
        <?= Html::endForm() ?>
    </div>
</div>

<?php $stopUrl = Url::toRoute(['stop']); ?>
<div class="modal fade" id="stopItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <?= Html::beginForm($stopUrl, 'post', ['class' => 'modal-content', 'id' => 'stop-item-form']) ?>
            <div class="modal-header">
                <h5 class="modal-title">Остановить сбор</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Сбор «<span id="stop-item-name"></span>» будет остановлен. Укажите причину.</p>

                <div class="mb-3">
                    <label class="form-label">Причина из списка</label>
                    <?= Html::dropDownList('reason_id', null, $reasonList, [
                        'class' => 'form-select',
                        'prompt' => '— выберите причину —',
                        'id' => 'stop-reason-id',
                    ]) ?>
                </div>

                <div class="mb-2">
                    <label class="form-label">Или укажите свою причину</label>
                    <?= Html::textarea('reason_text', '', [
                        'class' => 'form-control',
                        'rows' => 3,
                        'id' => 'stop-reason-text',
                        'placeholder' => 'Текст причины…',
                    ]) ?>
                </div>
                <div class="invalid-feedback d-block" id="stop-reason-error" style="display:none!important;">
                    Укажите причину из списка или текстом.
                </div>

                <div class="form-check mt-3">
                    <?= Html::checkbox('dead', false, ['class' => 'form-check-input', 'id' => 'stop-ritual']) ?>
                    <label class="form-check-label" for="stop-ritual">
                        Поставить ч/б изображение (животное погибло)
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <?= Html::submitButton('Остановить сбор', ['class' => 'btn btn-danger']) ?>
            </div>
        <?= Html::endForm() ?>
    </div>
</div>

<?php
$baseStopUrl = $stopUrl;
$basePublishUrl = $publishUrl;
$this->registerJs(<<<JS
var publishModal = document.getElementById('publishItemModal');
var publishForm = document.getElementById('publish-item-form');
var publishBaseUrl = '{$basePublishUrl}';

publishModal.addEventListener('show.bs.modal', function (event) {
    var btn = event.relatedTarget;
    var id = btn.getAttribute('data-id');
    var name = btn.getAttribute('data-name') || '';

    publishForm.setAttribute('action', publishBaseUrl + (publishBaseUrl.indexOf('?') === -1 ? '?' : '&') + 'id=' + encodeURIComponent(id));
    document.getElementById('publish-item-name').textContent = name;
});

var stopModal = document.getElementById('stopItemModal');
var stopForm = document.getElementById('stop-item-form');
var stopBaseUrl = '{$baseStopUrl}';

stopModal.addEventListener('show.bs.modal', function (event) {
    var btn = event.relatedTarget;
    var id = btn.getAttribute('data-id');
    var name = btn.getAttribute('data-name') || '';

    // подставляем id сбора в action формы
    stopForm.setAttribute('action', stopBaseUrl + (stopBaseUrl.indexOf('?') === -1 ? '?' : '&') + 'id=' + encodeURIComponent(id));
    document.getElementById('stop-item-name').textContent = name;

    // сброс полей
    document.getElementById('stop-reason-id').value = '';
    document.getElementById('stop-reason-text').value = '';
    document.getElementById('stop-reason-error').style.display = 'none';
});

stopForm.addEventListener('submit', function (e) {
    var reasonId = document.getElementById('stop-reason-id').value;
    var reasonText = document.getElementById('stop-reason-text').value.trim();
    if (!reasonId && !reasonText) {
        e.preventDefault();
        document.getElementById('stop-reason-error').style.display = 'block';
    }
});
JS);
?>
