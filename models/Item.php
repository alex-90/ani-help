<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "item".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $status
 * @property string $created_at
 * @property string|null $updated_at
 * @property int $category_id
 * @property string|null $before_dt
 * @property int $created_user_id
 * @property float $sum_need
 * @property float $sum_current
 *
 * @property Category $category
 * @property User $createdUser
 * @property Document[] $documents
 * @property Donate[] $donates
 * @property SocialNetworks[] $socialNetworks
 */
class Item extends \yii\db\ActiveRecord
{
    public $imageFile;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'created_at', 'category_id', 'created_user_id', 'sum_need', 'sum_current'], 'required'],
            [['description'], 'string'],
            [['status', 'category_id', 'created_user_id'], 'integer'],
            [['created_at', 'updated_at', 'before_dt'], 'safe'],
            [['sum_need', 'sum_current'], 'number'],
            [['name'], 'string', 'max' => 45],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_user_id' => 'id']],

            
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif'],
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
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'category_id' => 'Category ID',
            'before_dt' => 'Before Dt',
            'created_user_id' => 'Created User ID',
            'sum_need' => 'Sum Need',
            'sum_current' => 'Sum Current',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[CreatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_user_id']);
    }

    /**
     * Gets query for [[Documents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(Document::class, ['item_id' => 'id']);
    }

    /**
     * Gets query for [[Donates]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDonates()
    {
        return $this->hasMany(Donate::class, ['item_id' => 'id']);
    }

    /**
     * Gets query for [[SocialNetworks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSocialNetworks()
    {
        return $this->hasMany(SocialNetworks::class, ['item_id' => 'id']);
    }

    public function beforeValidate()
    {
        $this->name = trim(strip_tags($this->name));
        $this->description = trim(strip_tags($this->description));
        
        return parent::beforeValidate();
    }

    public static function formatSum($sum)
    {
        return number_format($sum, 2, '.', ' ');
    }
}
