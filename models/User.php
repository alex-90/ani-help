<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string|null $iin
 * @property string|null $phone
 * @property string|null $email
 * @property int $role_id
 * @property int $inactive
 * @property string $created_at
 * @property string|null $updated_at
 * @property string $password
 * @property string|null $auth_key
 * @property int $corporate
 *
 * @property Favourite[] $Favourite
 * @property Invoice[] $Invoice
 * @property Item[] $items
 * @property UserRole $role
 */
// class User extends \yii\db\ActiveRecord
class User extends ActiveRecord implements IdentityInterface

{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'role_id', 'created_at', 'password'], 'required'],
            [['role_id', 'inactive', 'corporate'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'password', 'auth_key'], 'string', 'max' => 255],
            [['iin', 'phone', 'email'], 'string', 'max' => 45],
            [['iin'], 'unique'],
            [['phone'], 'unique'],
            [['email'], 'unique'],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserRole::class, 'targetAttribute' => ['role_id' => 'id']],
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
            'iin' => 'Iin',
            'phone' => 'Phone',
            'email' => 'Email',
            'role_id' => 'Role ID',
            'inactive' => 'Inactive',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'password' => 'Password',
            'auth_key' => 'Auth Key',
            'corporate' => 'Corporate',
        ];
    }

    /**
     * Gets query for [[Favourite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavourite()
    {
        return $this->hasMany(Favourite::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Invoice]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasMany(Invoice::class, ['user_id' => 'id']);
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

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(UserRole::class, ['id' => 'role_id']);
    }


    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }
}
