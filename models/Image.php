<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Image".
 *
 * @property int $id
 * @property int $item_id
 * @property string $url
 * @property string $url_big 750x400
 * @property string $url_sm 256x140
 * @property string $url_big_bw 750x400, чёрно-белое
 * @property string $url_sm_bw 256x140, чёрно-белое
 * @property string|null $title
 * @property int $position
 * @property string $created_at
 * @property int $sensitive
 *
 * @property Item $item
 */
class Image extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'images';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id', 'url', 'created_at'], 'required'],
            [['item_id', 'position', 'sensitive'], 'integer'],
            [['url', 'url_big', 'url_sm', 'url_big_bw', 'url_sm_bw'], 'string'],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::class, 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Item ID',
            'url' => 'Url',
            'title' => 'Title',
            'position' => 'Position',
            'created_at' => 'Created At',
            'sensitive' => 'Sensitive',
        ];
    }

    /**
     * Gets query for [[Item]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::class, ['id' => 'item_id']);
    }

    /**
     * URL большого изображения (750x400). Если животное погибло — чёрно-белое.
     */
    public function bigUrl(): string
    {
        return $this->item && $this->item->dead ? $this->url_big_bw : $this->url_big;
    }

    /**
     * URL превью (256x140). Если животное погибло — чёрно-белое.
     */
    public function smUrl(): string
    {
        return $this->item && $this->item->dead ? $this->url_sm_bw : $this->url_sm;
    }
}
