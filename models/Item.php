<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "item".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $created_at
 * @property string|null $updated_at
 * @property int $status_id
 * @property int $category_id
 * @property string|null $before_dt
 * @property int $created_user_id
 * @property float $sum_need
 * @property float $sum_current
 * @property string|null $currency
 * @property int|null $reason_id
 * @property string|null $reason_text
 *
 * @property Category $category
 * @property User $createdUser
 * @property Document[] $documents
 * @property Favourite[] $Favourite
 * @property Image[] $Image
 * @property Invoice[] $Invoice
 * @property Item[] $item1s
 * @property Item[] $item2s
 * @property Reason $reason
 * @property RelatedItems[] $relatedItems
 * @property RelatedItems[] $relatedItems0
 * @property SocialNetworkLink[] $SocialNetworkLink
 * @property ItemStatus $status
 */
class Item extends \yii\db\ActiveRecord
{
    public $imageFile;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'items';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                // колонки хранятся как строки datetime
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['name', 'created_at', 'status_id', 'category_id', 'created_user_id', 'sum_need', 'sum_current'], 'required', 'message' => 'Необходимо заполнить «{attribute}»'],
            // [['name', 'created_at', 'status_id', 'category_id', 'created_user_id', 'sum_need', 'sum_current'], 'required', 'message' => '{attribute} не может быть пустым'],
            [['name', 'created_at', 'status_id', 'created_user_id', 'sum_need', 'sum_current'], 'required', 'message' => '{attribute} не может быть пустым'],
            [['description'], 'string'],
            [['created_at', 'updated_at', 'before_dt'], 'safe'],
            [['status_id', 'category_id', 'created_user_id', 'reason_id'], 'integer'],
            [['sum_need', 'sum_current'], 'number'],
            [['name', 'currency'], 'string', 'max' => 45],
            [['reason_text'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemStatus::class, 'targetAttribute' => ['status_id' => 'id']],
            [['reason_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reason::class, 'targetAttribute' => ['reason_id' => 'id']],
            [['created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'description' => 'Описание',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'status_id' => 'Статус',
            'category_id' => 'Категория',
            'before_dt' => 'До какой даты желательно собрать',
            'created_user_id' => 'Создал',
            'sum_need' => 'Необходимая сумма',
            'sum_current' => 'Текущая сумма',
            'currency' => 'Валюта',
            'reason_id' => 'Причина закрытия сбора',
            'reason_text' => 'Причина закрытия сбора  (текст)',
            'published' => 'Опубликован',
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
     * Gets query for [[Favourite]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavourite()
    {
        return $this->hasMany(Favourite::class, ['item_id' => 'id']);
    }

    /**
     * Gets query for [[Image]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        return $this->hasMany(Image::class, ['item_id' => 'id']);
    }

    /**
     * Gets query for [[Invoice]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasMany(Invoice::class, ['item_id' => 'id']);
    }

    /**
     * Gets query for [[Item1s]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem1s()
    {
        return $this->hasMany(Item::class, ['id' => 'item1_id'])->viaTable('related_items', ['item2_id' => 'id']);
    }

    /**
     * Gets query for [[Item2s]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem2s()
    {
        return $this->hasMany(Item::class, ['id' => 'item2_id'])->viaTable('related_items', ['item1_id' => 'id']);
    }

    /**
     * Gets query for [[Reason]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReason()
    {
        return $this->hasOne(Reason::class, ['id' => 'reason_id']);
    }

    /**
     * Gets query for [[RelatedItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRelatedItems()
    {
        return $this->hasMany(RelatedItems::class, ['item1_id' => 'id']);
    }

    /**
     * Gets query for [[RelatedItems0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRelatedItems0()
    {
        return $this->hasMany(RelatedItems::class, ['item2_id' => 'id']);
    }

    /**
     * Gets query for [[SocialNetworkLink]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSocialNetworkLink()
    {
        return $this->hasMany(SocialNetworkLink::class, ['item_id' => 'id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(ItemStatus::class, ['id' => 'status_id']);
    }


    public function beforeValidate()
    {
        $this->name = trim(strip_tags($this->name));
        $this->description = trim(strip_tags($this->description));

        // created_at обязателен в rules(), а TimestampBehavior ставит его только
        // на beforeSave (после валидации) — поэтому задаём его здесь для новых записей
        if ($this->isNewRecord && empty($this->created_at)) {
            $this->created_at = date('Y-m-d H:i:s');
        }

        return parent::beforeValidate();
    }


    public static function formatSum($sum)
    {
        return number_format($sum, 2, '.', ' ');
    }

}
