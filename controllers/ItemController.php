<?php

namespace app\controllers;

use app\models\Item;
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

        $model->load($this->request->post());
        
        $model->created_user_id = \Yii::$app->user->id;
        $model->created_at = date('Y-m-d H:i:s');
        $model->sum_current = 0;
        // $model->validate();
        
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
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

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
        
        // dd('AAA');
        $file = UploadedFile::getInstanceByName('Item[imageFile]');
        $fileName = $file->baseName . '.' . $file->extension;

        
        $remoteKey = 'uploads/' . uniqid('img_', true) . '.' . $file->extension;
        $url = \Yii::$app->b2->uploadFile($file->tempName, $remoteKey);

        // dd($url);
        if ($url === null) {
            return $this->asJson(['error' => 'Ошибка загрузки в хранилище']);
        }
        
        // $doc = new Document();
        // $doc->item_id = $model->id;
        // $doc->name = $file->baseName;
        // $doc->description = $file->baseName;
        // $doc->status = 1;
        // $doc->created_at = date('Y-m-d H:i:s');
        // $doc->sum_current = 0;
        // $doc->sum_need = Item::formatSum($model->sum_need);
        // $doc->created_user_id = \Yii::$app->user->id;
        // $doc->save();
        dd($file->name);
        dd($file->extension);
        dd($file->baseName);
        // dd(UploadedFile::getInstanceByName('Item[imageFile]'));

        // dd(UploadedFile::getInstanceByName('imageFile[0]'));
        // dd($model);
        if ($model->imageFile) {
            dd($model->imageFile);

            // $model->imageFile->saveAs('uploads/' . $model->imageFile->baseName . '.' . $model->imageFile->extension);
            // $model->imageFile = null;
            // $model->save();
        }
        
        return $this->redirect(['view', 'id' => $model->id]);
    }
}
