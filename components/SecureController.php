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

        // Запрещаем действия с чужими записями.
        $ownerId = $this->ownerId($action);
        if ($ownerId !== null && $ownerId != Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException('Вы не можете выполнять действия с чужой записью.');
        }

        // Custom beforeAction logic
        return true;
    }

    /**
     * Возвращает id пользователя-владельца записи, с которой работает экшен,
     * или null, если для экшена нет проверки принадлежности.
     * Контроллеры переопределяют этот метод.
     *
     * @param \yii\base\Action $action
     * @return int|null
     */
    protected function ownerId($action)
    {
        return null;
    }
}