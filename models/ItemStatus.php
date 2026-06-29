<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "item_status".
 *
 * @property int $id
 * @property string $name
 *
 * @property Item[] $items
 */
class ItemStatus extends \yii\db\ActiveRecord
{
    /** Сбор открыт */
    const STATUS_OPEN = 1;
    /** Сбор закрыт */
    const STATUS_CLOSED = 2;
    /** Сбор приостановлен */
    const STATUS_PAUSED = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'item_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[Items]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Item::class, ['status_id' => 'id']);
    }
}
