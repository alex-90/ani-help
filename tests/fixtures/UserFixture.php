<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'app\models\User';
    public $dataFile = '@app/tests/fixtures/data/users.php';
    public $depends = [
        'tests\fixtures\UserRoleFixture',
    ];
}
