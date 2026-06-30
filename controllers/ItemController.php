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

        // Опубликованный сбор редактируется ограниченно: можно изменить только сумму,
        // дописать текст (старый не меняется) и добавить картинки. Остальные поля заблокированы.
        if ($model->published) {
            if ($this->request->isPost) {
                $post = $this->request->post('Item', []);

                if (isset($post['sum_need'])) {
                    $model->sum_need = $post['sum_need'];
                }

                $append = trim((string) ($post['appendText'] ?? ''));
                if ($append !== '') {
                    $prefix = (string) $model->description === '' ? '' : $model->description . "\n\n";
                    $model->description = $prefix . 'UPD ' . date('d.m.Y H:i') . "\n" . $append;
                }

                if ($model->save()) {
                    return $this->redirect(['own-items']);
                }
            }

            return $this->render('update', [
                'model' => $model,
            ]);
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

        $src = $this->createGdImage($file->tempName);
        if ($src === null) {
            return $this->asJson(['error' => 'Не удалось обработать изображение']);
        }

        $base = 'uploads/' . uniqid('img_', true);

        // оригинал + цветные и чёрно-белые производные размеры
        $url      = \Yii::$app->b2->uploadFile($file->tempName, $base . '.' . $file->extension);
        $urlBig   = $this->uploadGdImage($this->cropResize($src, 750, 400), $base . '_big.jpg');
        $urlSm    = $this->uploadGdImage($this->cropResize($src, 256, 140), $base . '_sm.jpg');
        $urlBigBw = $this->uploadGdImage($this->cropResize($src, 750, 400, true), $base . '_big_bw.jpg');
        $urlSmBw  = $this->uploadGdImage($this->cropResize($src, 256, 140, true), $base . '_sm_bw.jpg');

        if (in_array(null, [$url, $urlBig, $urlSm, $urlBigBw, $urlSmBw], true)) {
            return $this->asJson(['error' => 'Ошибка загрузки в хранилище']);
        }

        $image = new Image();
        $image->item_id = $model->id;
        $image->url = $url;
        $image->url_big = $urlBig;
        $image->url_sm = $urlSm;
        $image->url_big_bw = $urlBigBw;
        $image->url_sm_bw = $urlSmBw;
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

    /**
     * Создаёт GD-ресурс из загруженного файла (jpg/png/gif).
     * @return \GdImage|null
     */
    private function createGdImage(string $path)
    {
        $info = @getimagesize($path);
        if ($info === false) {
            return null;
        }

        switch ($info[2]) {
            case IMAGETYPE_JPEG: return @imagecreatefromjpeg($path) ?: null;
            case IMAGETYPE_PNG:  return @imagecreatefrompng($path) ?: null;
            case IMAGETYPE_GIF:  return @imagecreatefromgif($path) ?: null;
        }

        return null;
    }

    /**
     * Масштабирует с обрезкой по центру до точных размеров (cover) и при необходимости делает ч/б.
     * @param \GdImage $src
     * @return \GdImage
     */
    private function cropResize($src, int $w, int $h, bool $grayscale = false)
    {
        $sw = imagesx($src);
        $sh = imagesy($src);

        $scale = max($w / $sw, $h / $sh);
        $cropW = (int) round($w / $scale);
        $cropH = (int) round($h / $scale);
        $srcX  = (int) round(($sw - $cropW) / 2);
        $srcY  = (int) round(($sh - $cropH) / 2);

        $dst = imagecreatetruecolor($w, $h);
        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $w, $h, $cropW, $cropH);

        if ($grayscale) {
            imagefilter($dst, IMG_FILTER_GRAYSCALE);
        }

        return $dst;
    }

    /**
     * Сохраняет GD-ресурс во временный JPG и загружает в B2. GD-ресурс освобождается.
     * @param \GdImage $img
     * @return string|null
     */
    private function uploadGdImage($img, string $remoteKey): ?string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'img') . '.jpg';
        imagejpeg($img, $tmp, 85);

        $url = \Yii::$app->b2->uploadFile($tmp, $remoteKey);
        @unlink($tmp);

        return $url;
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
        $model->dead = \Yii::$app->request->post('dead') ? 1 : 0;
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
