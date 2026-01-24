<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $fio
 * @property string|null $iin
 * @property string|null $phone
 * @property string|null $email
 * @property int $type
 * @property int $inactive
 * @property string $created_at
 * @property string|null $updated_at
 * @property string $password
 * @property string|null $auth_key
 *
 * @property Donate[] $donates
 * @property Item[] $items
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fio', 'created_at', 'password'], 'required'],
            [['type', 'inactive'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['fio', 'iin', 'phone', 'email'], 'string', 'max' => 45],
            [['password', 'auth_key'], 'string', 'max' => 255],
            [['iin'], 'unique'],
            [['phone'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fio' => 'Fio',
            'iin' => 'Iin',
            'phone' => 'Phone',
            'email' => 'Email',
            'type' => 'Type',
            'inactive' => 'Inactive',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'password' => 'Password',
            'auth_key' => 'Auth Key',
        ];
    }

    /**
     * Gets query for [[Donates]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDonates()
    {
        return $this->hasMany(Donate::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Items]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Item::class, ['created_user_id' => 'id']);
    }
}
