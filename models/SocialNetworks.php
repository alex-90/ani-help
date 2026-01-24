<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "social_networks".
 *
 * @property int $id
 * @property string $link
 * @property string|null $title
 * @property int $item_id
 *
 * @property Item $item
 */
class SocialNetworks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'social_networks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['link', 'item_id'], 'required'],
            [['item_id'], 'integer'],
            [['link'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 45],
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
            'link' => 'Link',
            'title' => 'Title',
            'item_id' => 'Item ID',
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
}
