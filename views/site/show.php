<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use app\models\ItemStatus;

/** @var yii\web\View $this */
/** @var app\models\Item $model */
/** @var app\models\Image[] $images */

$this->title = $model->name;
// $this->params['breadcrumbs'][] = ['label' => 'Items', 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="item-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <div id="itemCarousel" class="carousel slide col-md-10 mx-auto" data-bs-ride="carousel">
        <?php if (count($images) > 1): ?>
            <div class="carousel-indicators">
                <?php foreach ($images as $i => $image): ?>
                    <button type="button" data-bs-target="#itemCarousel" data-bs-slide-to="<?= $i ?>"
                        class="<?= $i === 0 ? 'active' : '' ?>"
                        <?= $i === 0 ? 'aria-current="true"' : '' ?>
                        aria-label="Фото <?= $i + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="carousel-inner">
            <?php if (empty($images)): ?>
                <div class="carousel-item active">
                    <img src="https://placehold.co/750x400" class="card-img-top" alt="...">
                </div>
            <?php else: ?>
                <?php foreach ($images as $i => $image): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <img src="<?= Html::encode($image->bigUrl()); ?>" class="card-img-top" alt="...">
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php if (count($images) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#itemCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#itemCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        <?php endif; ?>
    </div>

    <div class="item-description">
        <?php
        $lines = preg_split('/\R/u', (string) $model->description);
        foreach ($lines as $line) {
            if (preg_match('/^UPD\b(.*)$/u', $line, $m)) {
                $date = trim($m[1]);
                echo '<h1>UPD'
                    . ($date !== '' ? ' <small class="text-muted">' . Html::encode($date) . '</small>' : '')
                    . '</h1>';
            } else {
                echo Html::encode($line) . '<br>';
            }
        }

        // если сбор закрыт — показываем отметку UPD с причиной
        if ($model->status_id == ItemStatus::STATUS_CLOSED) {
            $reason = $model->reason_id && $model->reason
                ? $model->reason->name
                : $model->reason_text;

            $closedAt = $model->updated_at ? date('d.m.Y H:i', strtotime($model->updated_at)) : '';

            echo '<h1>UPD'
                . ($closedAt !== '' ? ' <small class="text-muted">' . Html::encode($closedAt) . '</small>' : '')
                . '</h1>';
            echo '<p><strong>Сбор закрыт.</strong>'
                . ($reason ? ' Причина: ' . Html::encode($reason) : '')
                . '</p>';
        }
        ?>
    </div>

</div>
