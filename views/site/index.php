<?php

use  yii\helpers\Html;
use yii\helpers\Url;
use app\models\Item;
use app\models\ItemStatus;

/** @var yii\web\View $this */

$this->title = 'My Yii Application 1';

?>
<div class="site-index">
    <div class="items1 row row-cols-1 row-cols-md-4 g-4 m-0 p-1">
        <?php foreach ($dataProvider->getModels() as $item): ?>
            <div class="col">
                <div class="card h-100">
                    <?php $thumb = $item->getFirstImage(); ?>
                    <a href="<?= Url::to(['site/show', 'id' => $item->id]) ?>">
                        <img src="<?= $thumb ? Html::encode($thumb->smUrl()) : 'https://placehold.co/256x140' ?>"
                             class="card-img-top" style="height: 160px; object-fit: cover;" alt="<?= Html::encode($item->name) ?>">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <?= Html::a(htmlspecialchars($item->name), Url::to(['site/show', 'id' => $item->id]), [
                                'class' => 'stretched-link text-decoration-none',
                                'style' => 'color: var(--shelter-green-dark, #3c7d3f);',
                            ]) ?>
                        </h5>

                        <?php if ($item->status_id == ItemStatus::STATUS_CLOSED): ?>
                            <p class="card-text"><span class="badge bg-secondary">Сбор закрыт</span></p>
                        <?php else: ?>
                            <p class="card-text">Необходимая сумма: <?= Item::formatSum($item->sum_need) ?> KZT</p>
                        <?php endif; ?>
                        <p class="card-text">Собрано: <?= Item::formatSum($item->sum_current) ?> KZT</p>
                        <?= Html::a('Перейти', Url::to(['site/show', 'id' => $item->id]), ['class' => 'btn btn-primary mt-auto']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
