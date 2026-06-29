<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

class ReasonFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Reason';
    public $dataFile = '@app/tests/fixtures/data/reason.php';
}
