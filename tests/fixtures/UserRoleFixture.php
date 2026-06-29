<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

class UserRoleFixture extends ActiveFixture
{
    public $modelClass = 'app\models\UserRole';
    public $dataFile = '@app/tests/fixtures/data/user_roles.php';
}
