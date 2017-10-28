<?php

namespace app\models;
use yii\db\ActiveRecord;

class Location extends ActiveRecord
{
 
    public static function tableName()
    {
        return '{{locations}}';
    }

}
