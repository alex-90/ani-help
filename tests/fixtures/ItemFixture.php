<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

class ItemFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Item';
    public $dataFile = '@app/tests/fixtures/data/items.php';
    public $depends = [
        'tests\fixtures\UserFixture',
        'tests\fixtures\ItemStatusFixture',
        'tests\fixtures\ReasonFixture',
    ];
}
