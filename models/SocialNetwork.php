<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "social_networks".
 *
 * @property int $id
 * @property string $name
 *
 * @property SocialNetworkLink[] $SocialNetworkLink
 */
class SocialNetwork extends \yii\db\ActiveRecord
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
     * Gets query for [[SocialNetworkLink]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSocialNetworkLink()
    {
        return $this->hasMany(SocialNetworkLink::class, ['social_network_id' => 'id']);
    }
}
