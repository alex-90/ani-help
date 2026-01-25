<?php

namespace app\controllers;
use app\components\SecureController;

class AdminController extends SecureController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
