<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoices".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $item_id
 * @property float $sum
 * @property string|null $currency
 * @property string|null $comment
 * @property string $created_at
 * @property string $status
 * @property int $on_duty
 *
 * @property Item $item
 * @property User $user
 */
class Invoices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'item_id', 'on_duty'], 'integer'],
            [['item_id', 'created_at', 'status'], 'required'],
            [['sum'], 'number'],
            [['created_at'], 'safe'],
            [['currency', 'comment', 'status'], 'string', 'max' => 45],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::class, 'targetAttribute' => ['item_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'item_id' => 'Item ID',
            'sum' => 'Sum',
            'currency' => 'Currency',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'status' => 'Status',
            'on_duty' => 'On Duty',
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
