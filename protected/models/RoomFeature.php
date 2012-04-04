<?php

/**
 * This is the model class for table "room_feature".
 *
 * The followings are the available columns in table 'room_feature':
 * @property integer $room_id
 * @property integer $feature_id
 * @property string $details
 * @property integer $verified
 * @property string $create_time
 * @property string $update_time
 */
class RoomFeature extends CActiveRecord
{
	/**
	 * @return array behavior rules for model attributes.
	 */
	public function behaviors() {
		return array(
			'CTimestampBehavior'=>array(
				'class'=>'zii.behaviors.CTimestampBehavior',
			)

		);
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RoomFeature the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'room_feature';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('room_id, feature_id, details, verified', 'required'),
			array('room_id, feature_id, verified', 'numerical', 'integerOnly'=>true),
			array('details', 'length', 'max'=>45),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('room_id, feature_id, details, verified, create_time, update_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'feature' => array(self::BELONGS_TO, 'Feature', 'feature_id'),
			'room' => array(self::BELONGS_TO, 'Room', 'room_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'room_id' => 'Room',
			'feature_id' => 'Feature',
			'details' => 'Details',
			'verified' => 'Verified',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('room_id',$this->room_id);
		$criteria->compare('feature_id',$this->feature_id);
		$criteria->compare('details',$this->details,true);
		$criteria->compare('verified',$this->verified);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}