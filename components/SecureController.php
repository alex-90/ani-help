<?php

namespace app\components;

use Yii;
use yii\web\Controller;

class SecureController extends Controller {
    public function init() {
        parent::init();
        // Custom initialization code
    }

    public function beforeAction($action) {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if(Yii::$app->user->isGuest) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action');            
        }

        // Custom beforeAction logic
        return true;
    }
}