<?php

namespace app\models;
use yii\db\ActiveRecord;

class Location extends ActiveRecord
{
 
    public static function tableName()
    {
        return '{{locations}}';
    }
    
    public function rules()
    {
        return [
            // the name, email, subject and body attributes are required
            [['city', 'state', 'zip', ], 'required'],
        ];
    }

}
