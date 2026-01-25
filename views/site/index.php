<?php

use  yii\helpers\Html;
use yii\helpers\Url;
use app\models\Item;

/** @var yii\web\View $this */

$this->title = 'My Yii Application 1';
?>
<div class="site-index">


    <div class="items1 row row-cols-1 row-cols-md-4 g-4 m-0 p-1">

        <?php foreach ($dataProvider->getModels() as $item): ?>
            <div class="col">
                <div class="card" style="width: 18rem;">
                    <a href="<?= Url::to(['site/show', 'id' => $item->id]) ?>">
                        <img src="https://placehold.co/256x140" class="card-img-top" alt="...">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($item->name) ?></h5>

                        <p class="card-text">Необходимая сумма: <?= Item::formatSum($item->sum_need) ?> KZT</p>
                        <p class="card-text">Собрано: <?= Item::formatSum($item->sum_current) ?> KZT</p>
                        <?= Html::a('Перейти', Url::to(['site/show', 'id' => $item->id]), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4 mb-3">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-outline-secondary" href="https://www.yiiframework.com/doc/">Yii Documentation &raquo;</a></p>
            </div>
            <div class="col-lg-4 mb-3">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-outline-secondary" href="https://www.yiiframework.com/forum/">Yii Forum &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-outline-secondary" href="https://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
