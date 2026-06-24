<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "social_network_links".
 *
 * @property int $id
 * @property string $link
 * @property string|null $title
 * @property int $item_id
 * @property int $social_network_id
 *
 * @property Item $item
 * @property SocialNetwork $socialNetwork
 */
class SocialNetworkLink extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'social_network_links';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['link', 'item_id', 'social_network_id'], 'required'],
            [['item_id', 'social_network_id'], 'integer'],
            [['link'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 45],
            [['social_network_id'], 'exist', 'skipOnError' => true, 'targetClass' => SocialNetwork::class, 'targetAttribute' => ['social_network_id' => 'id']],
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
            'social_network_id' => 'Social Network ID',
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
     * Gets query for [[SocialNetwork]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSocialNetwork()
    {
        return $this->hasOne(SocialNetwork::class, ['id' => 'social_network_id']);
    }
}
