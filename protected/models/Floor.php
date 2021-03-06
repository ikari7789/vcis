<?php

/**
 * This is the model class for table "floor".
 *
 * The followings are the available columns in table 'floor':
 * @property integer $id
 * @property integer $level
 * @property string $map_image
 * @property integer $building_id
 * @property string $create_time
 * @property string $update_time
 * @property integer $create_user_id
 * @property integer $update_user_id
 *
 * The followings are the available model relations:
 * @property Building $building
 * @property User $createUser
 * @property User $updateUser
 * @property Room[] $rooms
 */
class Floor extends ActiveRecordBase
{
	private $_oldValues = array();
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Building the static model class
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
		return 'floor';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('level, building_id', 'required'),
			array('level, building_id', 'numerical', 'integerOnly'=>true),
			array('map_image', 'length', 'max'=>255),
			array('map_image', 'file',
				'maxSize' => 1024 * 1024 * 2, // max filesize allowed
				'tooLarge' => 'File must be less than 2MB',
				'types' => array('jpg', 'jpeg', 'gif', 'png'),
				'wrongType' => 'File can only be (.jpg, .gif, .png)',
				'allowEmpty' => true,
			),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, level, map_image, building_id, create_time, update_time, create_user_id, update_user_id', 'safe', 'on'=>'search'),
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
			'building' => array(self::BELONGS_TO, 'Building', 'building_id'),
			'createUser' => array(self::BELONGS_TO, 'User', 'create_user_id'),
			'updateUser' => array(self::BELONGS_TO, 'User', 'update_user_id'),
			'rooms' => array(self::HAS_MANY, 'Room', 'floor_id'),
			'roomCount' => array(self::STAT, 'Room', 'floor_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'level' => 'Level',
			'map_image' => 'Map Image',
			'building_id' => 'Building',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
			'create_user_id' => 'Create User',
			'update_user_id' => 'Update User',
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

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.level',$this->level);
		$criteria->compare('t.map_image',$this->map_image,true);
		$criteria->compare('t.building_id',$this->building_id);
		$criteria->compare('t.create_time',$this->create_time,true);
		$criteria->compare('t.update_time',$this->update_time,true);
		$criteria->compare('t.create_user_id',$this->create_user_id);
		$criteria->compare('t.update_user_id',$this->update_user_id);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	protected function beforeSave() {
		Yii::trace('Begin','Floor::beforeSave');
		
		// Define save directory
		$rootPath = pathinfo(Yii::app()->request->scriptFile);
		$baseDir = $rootPath['dirname'];
		$uploadDir = $baseDir.'/images/floors/';
		Yii::trace('Image save location: '.$uploadDir,'Floor::beforeSave');
		
		// if new map_image attempted to be uploaded
		// backup old image
		if (isset($this->_oldValues['map_image']) && is_object($this->map_image) && get_class($this->map_image)==='CUploadedFile') {
			$oldFile = $uploadDir.$this->_oldValues['map_image'];
			$newFile = $oldFile.'.bak';
			if (!is_dir($oldFile) && file_exists($oldFile))
				rename($oldFile, $newFile);
		}

		return parent::beforeSave();
	}
	
	protected function afterSave() {		
		Yii::trace('Begin','Floor::afterSave');

		// Define save directory
		$rootPath = pathinfo(Yii::app()->request->scriptFile);
		$baseDir = $rootPath['dirname'];
		$uploadDir = $baseDir.'/images/floors/';
		Yii::trace('Image save location: '.$uploadDir,'Floor::afterSave');
		$update = false;

		// Make sure the image is an uploaded image, otherwise leave image alone
		if (is_object($this->map_image) && get_class($this->map_image)==='CUploadedFile') {
			Yii::trace('Map Image: CUploadedFile object found.','Floor::afterSave');
			
			// Define filename
			$newfname = $this->building_id.'_'.$this->id.'_map.'.$this->map_image->extensionName;
			Yii::trace('Map Image: New filename: '.$newfname,'Floor::afterSave');
			
			Yii::trace('Map Image: Full filename + directory: '.$uploadDir.$newfname,'Floor::afterSave');
			// Save file
			if ($this->map_image->saveAs($uploadDir.$newfname)) {
				$this->map_image = $newfname;
				
				// resize image
				$file = $uploadDir.$newfname;
				$img = Yii::app()->simpleImage->load($file);
				if ($img->getWidth() > 582)
					$img->resizeToWidth(582);
				$img->save($file);	
				
				$update = true;
				Yii::trace('Map Image: File saved successfully.','Floor::afterSave');
				
				if (isset($this->_oldValues['map_image'])) {
					// delete backed up image if it exists
					$imgBak = $this->_oldValues['map_image'].'.bak';
					if (!is_dir($uploadDir.$imgBak) && file_exists($uploadDir.$imgBak))
						unlink($uploadDir.$imgBak);
				}
			} else {
				Yii::trace('Map Image: Error in saving file.','Floor::afterSave');
				
				if (isset($this->_oldValues['map_image'])) {
					// restore backed up image if it exists
					$imgBak = $this->_oldValues['map_image'].'.bak';
					$imgRestore = substr($imgBak, 0, -4);
					if (!is_dir($uploadDir.$imgBak) && file_exists($uploadDir.$imgBak))
						rename($uploadDir.$imgBak, $uploadDir.$imgRestore);
				}
			}
		}
		
		// Save new filename to record
		if ($update) {
			if ($this->isNewRecord)
				$this->isNewRecord = false;
			$this->update();
		}
		
		Yii::trace('End','Floor::afterSave');;
		return parent::afterSave();
	}

	public function afterDelete()
	{
		Yii::trace('Begin','Floor::afterDelete');

		// Define delete directory
		$rootPath = pathinfo(Yii::app()->request->scriptFile);
		$baseDir = $rootPath['dirname'];
		$deleteDir = $baseDir.'/images/floors/';
		Yii::trace('Image save location: '.$deleteDir,'Floor::afterSave');
		
		// Delete map_image
		if (!is_dir($deleteDir.$this->_oldValues['map_image']) && file_exists($deleteDir.$this->_oldValues['map_image']))
		{
			if (unlink($deleteDir.$this->_oldValues['map_image']))
				Yii::trace($deleteDir.$this->_oldValues['map_image'].' deleted','Floor::afterDelete');
			else
				Yii::trace('Error in deleting: '.$deleteDir.$this->_oldValues['map_image'],'Floor::afterDelete');
		}
		
		unset($this->_oldValues);
		Yii::trace('$this->_oldValues removed','Floor::afterDelete');
		
		Yii::trace('End','Floor::afterDelete');
		return parent::afterDelete();
	}
	
	/**
	 * Save current record to temp variable to be able to rollback any changes.
	 */
	public function afterFind()
	{
		$this->_oldValues = $this->attributes;
		Yii::trace('Model backup created.','Building::afterFind');
		return parent::afterFind();
	}
}