<?php

namespace app\controllers;

use app\models\Item;
use app\models\ItemStatus;
use app\models\Image;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\SecureController;
use yii\web\UploadedFile;

/**
 * ItemController implements the CRUD actions for Item model.
 */
class ItemController extends SecureController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'toggle-published' => ['POST'],
                        'stop' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Item models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Item::find(),
            // 'pagination' => [
            //     'pageSize' => 5
            // ],
            // 'sort' => [
            //     'defaultOrder' => [
            //         'id' => SORT_DESC,
            //     ]
            // ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Item model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Item();

        // $model->load($this->request->post());
        
        $model->created_user_id = \Yii::$app->user->id;
        $model->sum_current = 0;
        $model->status_id = ItemStatus::STATUS_OPEN;
        // echo($model->validate());
        // dd($model->getErrors());
        // dd($model->validate());
        
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                // id уже получен — переходим на update, где работает async-загрузка картинок
                return $this->redirect(['update', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->published) {
            throw new \yii\web\ForbiddenHttpException('Опубликованный сбор редактировать нельзя.');
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['own-items']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Item model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        throw new \yii\web\ForbiddenHttpException('Удаление сборов запрещено.');
    }

    /**
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Item::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionUpload($id)
    {
        $model = $this->findModel($id);

        $file = UploadedFile::getInstanceByName('Item[imageFile]');
        if ($file === null) {
            return $this->asJson(['error' => 'Файл не получен']);
        }

        $remoteKey = 'uploads/' . uniqid('img_', true) . '.' . $file->extension;
        $url = \Yii::$app->b2->uploadFile($file->tempName, $remoteKey);

        if ($url === null) {
            return $this->asJson(['error' => 'Ошибка загрузки в хранилище']);
        }

        $image = new Image();
        $image->item_id = $model->id;
        $image->url = $url;
        $image->title = $file->baseName;
        $image->position = (int) Image::find()->where(['item_id' => $model->id])->max('position') + 1;
        $image->sensitive = 0;
        $image->created_at = date('Y-m-d H:i:s');

        if (!$image->save()) {
            return $this->asJson(['error' => 'Не удалось сохранить изображение']);
        }

        return $this->asJson([
            'url' => $url,
            'imageId' => $image->id,
        ]);
    }

    public function actionDeleteImage($id)
    {
        $image = Image::findOne(['id' => $id]);
        if ($image === null) {
            return $this->asJson(['error' => 'Изображение не найдено']);
        }

        $image->delete();

        return $this->asJson([]);
    }

    public function actionReorderImages($id)
    {
        $order = \Yii::$app->request->post('order', []);
        foreach (array_values($order) as $pos => $imageId) {
            Image::updateAll(
                ['position' => $pos + 1],
                ['id' => (int) $imageId, 'item_id' => $id]
            );
        }

        return $this->asJson(['ok' => true]);
    }


    public function actionTogglePublished($id)
    {
        $model = $this->findModel($id);
        $model->published = $model->published ? 0 : 1;
        $model->save(false);

        return $this->redirect(\Yii::$app->request->referrer ?: ['own-items']);
    }

    public function actionStop($id)
    {
        $model = $this->findModel($id);

        $reasonId = \Yii::$app->request->post('reason_id');
        $reasonText = trim((string) \Yii::$app->request->post('reason_text', ''));

        $model->status_id = ItemStatus::STATUS_CLOSED;
        $model->reason_id = $reasonId !== '' && $reasonId !== null ? (int) $reasonId : null;
        $model->reason_text = $reasonText !== '' ? $reasonText : null;
        $model->save(false);

        return $this->redirect(\Yii::$app->request->referrer ?: ['own-items']);
    }

    /**
     * Проверка принадлежности сбора текущему пользователю.
     * @inheritDoc
     */
    protected function ownerId($action)
    {
        $id = \Yii::$app->request->get('id');
        if ($id === null) {
            return null;
        }

        switch ($action->id) {
            case 'update':
            case 'upload':
            case 'reorder-images':
            case 'toggle-published':
            case 'stop':
                return $this->findModel($id)->created_user_id;

            case 'delete-image':
                $image = Image::findOne(['id' => $id]);
                return $image === null
                    ? null
                    : $this->findModel($image->item_id)->created_user_id;
        }

        return null;
    }


    public function actionOwnItems()
    {
        $userId = \Yii::$app->user->id;

        $dataProvider = new ActiveDataProvider([
            'query' => Item::find()->where(['created_user_id' => $userId]),
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        return $this->render('my-items', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
