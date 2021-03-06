<?php

/**
 * This is the model class for table "room_feature".
 *
 * The followings are the available columns in table 'room_feature':
 * @property integer $room_id
 * @property integer $feature_id
 * @property string $details
 * @property string $verification_time
 * @property string $create_time
 * @property string $update_time
 * @property integer $create_user_id
 * @property integer $update_user_id
 */
class RoomFeature extends ActiveRecordBase {
	private $_oldValues = array();

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Building the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'room_feature';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('room_id, feature_id, details', 'required'),
			array('room_id, feature_id', 'numerical', 'integerOnly' => true),
			array('details', 'length', 'max' => 45),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('room_id, feature_id, details, verification_time, create_time, update_time, create_user_id, update_user_id', 'safe', 'on' => 'search'),
			array('room_id, feature_id, details, verification_time,', 'safe', 'on' => 'searchByQuery'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
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
	public function attributeLabels() {
		return array('room_id' => 'Room', 'feature_id' => 'Feature', 'details' => 'Details', 'verification_time' => 'Verification Time', 'create_time' => 'Create Time', 'update_time' => 'Update Time', 'create_user_id' => 'Create User', 'update_user_id' => 'Update User', );
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria -> compare('t.room_id', $this -> room_id);
		$criteria -> compare('t.feature_id', $this -> feature_id);
		$criteria -> compare('t.details', $this -> details, true);
		$criteria -> compare('t.verification_time', $this -> verification_time, true);
		$criteria -> compare('t.create_time', $this -> create_time, true);
		$criteria -> compare('t.update_time', $this -> update_time, true);
		$criteria -> compare('t.create_user_id', $this -> create_user_id);
		$criteria -> compare('t.update_user_id', $this -> update_user_id);

		return new CActiveDataProvider($this, array('criteria' => $criteria, ));
	}

	/**
	 * Save current record to temp variable to be able to rollback any changes.
	 */
	public function afterFind() {
		$this -> _oldValues = $this -> attributes;
		Yii::trace('Model backup created.', 'Building::afterFind');
		return parent::afterFind();
	}

}
