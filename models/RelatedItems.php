<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "related_items".
 *
 * @property int $id
 * @property int $item1_id
 * @property int $item2_id
 *
 * @property Item $item1
 * @property Item $item2
 */
class RelatedItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'related_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item1_id', 'item2_id'], 'required'],
            [['item1_id', 'item2_id'], 'integer'],
            [['item2_id', 'item1_id'], 'unique', 'targetAttribute' => ['item2_id', 'item1_id']],
            [['item1_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::class, 'targetAttribute' => ['item1_id' => 'id']],
            [['item2_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::class, 'targetAttribute' => ['item2_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item1_id' => 'Item1 ID',
            'item2_id' => 'Item2 ID',
        ];
    }

    /**
     * Gets query for [[Item1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem1()
    {
        return $this->hasOne(Item::class, ['id' => 'item1_id']);
    }

    /**
     * Gets query for [[Item2]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem2()
    {
        return $this->hasOne(Item::class, ['id' => 'item2_id']);
    }
}
