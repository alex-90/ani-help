<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

class ItemStatusFixture extends ActiveFixture
{
    public $modelClass = 'app\models\ItemStatus';
    public $dataFile = '@app/tests/fixtures/data/item_status.php';
}
