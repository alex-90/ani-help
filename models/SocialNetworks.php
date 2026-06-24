<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "social_networks".
 *
 * @property int $id
 * @property string $name
 *
 * @property SocialNetworkLinks[] $socialNetworkLinks
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
     * Gets query for [[SocialNetworkLinks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSocialNetworkLinks()
    {
        return $this->hasMany(SocialNetworkLinks::class, ['social_network_id' => 'id']);
    }
}
