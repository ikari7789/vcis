<?php

/**
 * This is the model class for table "building".
 *
 * The followings are the available columns in table 'building':
 * @property integer $id
 * @property string $name
 * @property string $map_image
 * @property string $street_image
 * @property string $create_time
 * @property string $update_time
 * @property integer $create_user_id
 * @property integer $update_user_id
 *
 * The followings are the available model relations:
 * @property User $createUser
 * @property User $updateUser
 * @property Floor[] $floors
 */
class Building extends ActiveRecordBase
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
		return 'building';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'unique'),
			array('name', 'length', 'max'=>30),
			array('map_image, street_image', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, map_image, street_image, create_time, update_time, create_user_id, update_user_id', 'safe', 'on'=>'search'),
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
			'createUser' => array(self::BELONGS_TO, 'User', 'create_user_id'),
			'updateUser' => array(self::BELONGS_TO, 'User', 'update_user_id'),
			'floors' => array(self::HAS_MANY, 'Floor', 'building_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'map_image' => 'Map Image',
			'street_image' => 'Street Image',
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

		//$criteria->compare('id',$this->id);
		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('t.map_image',$this->map_image,true);
		$criteria->compare('t.street_image',$this->street_image,true);
		$criteria->compare('t.create_time',$this->create_time,true);
		$criteria->compare('t.update_time',$this->update_time,true);
		$criteria->compare('t.create_user_id',$this->create_user_id);
		$criteria->compare('t.update_user_id',$this->update_user_id);
		
		$criteria->order='t.name';
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
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

	protected function afterSave() {
		Yii::trace('Begin','Building::afterSave');
		
		// Define save directory
		$baseDir = Yii::getPathOfAlias('siteDir');
		$uploadDir = $baseDir.'/images/buildings/';
		Yii::trace('Image save location: '.$uploadDir,'Building::afterSave');
		$update = false;

		// Make sure the image is an uploaded image, otherwise leave image alone
		if (is_object($this->map_image) && get_class($this->map_image)==='CUploadedFile') {
				Yii::trace('Map Image: CUploadedFile object found.','Room::afterSave');
				
				// Define filename
				$newfname = $this->id.'_map.'.$this->map_image->extensionName;
				Yii::trace('Map Image: New filename: '.$newfname,'Building::afterSave');
				
				Yii::trace('Map Image: Full filename + directory: '.$uploadDir.$newfname,'Room::afterSave');
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
					Yii::trace('Map Image: File saved successfully.','Building::afterSave');
				} else {
					Yii::trace('Map Image: Error in saving file.','Building::afterSave');
				}
		}
		

		// Check street image
		if (is_object($this->street_image) && get_class($this->street_image)==='CUploadedFile') {
				Yii::trace('Street Image: CUploadedFile object found.','Building::afterSave');
				
				// Define filename
				$newfname = $this->id.'_street.'.$this->street_image->extensionName;
				Yii::trace('Street Image: New filename: '.$newfname,'Building::afterSave');
				
				Yii::trace('Street Image: Full filename + directory: '.$uploadDir.$newfname,'Building::afterSave');
				
				// Save file
				if ($this->street_image->saveAs($uploadDir.$newfname)) {
					$this->street_image = $newfname;
					
					// resize image
					$file = $uploadDir.$newfname;
					$img = Yii::app()->simpleImage->load($file);
					if ($img->getWidth() > 582)
						$img->resizeToWidth(582);
					$img->save($file);	
					
					$update = true;
					Yii::trace('Back Image: File saved successfully.','Building::afterSave');
				} else {
					Yii::trace('Back Image: Error in saving file.','Building::afterSave');
				}
		}
		
		// Save new filename to record
		if ($update) {
			if ($this->isNewRecord)
				$this->isNewRecord = false;
			$this->update();
		}
		
		Yii::trace('End','Building::afterSave');;
		return parent::afterSave();
	}
	
	public function afterDelete()
	{
		Yii::trace('Begin','Building::afterDelete');

		// Define delete directory
		$baseDir = Yii::getPathOfAlias('siteDir');
		$deleteDir = $baseDir.'/images/buildings/';
		Yii::trace('Image save location: '.$deleteDir,'Building::afterSave');
		
		// Delete map_image
		if (!is_dir($deleteDir.$this->_oldValues['map_image']) && file_exists($deleteDir.$this->_oldValues['map_image']))
		{
			if (unlink($deleteDir.$this->_oldValues['map_image']))
				Yii::trace($deleteDir.$this->_oldValues['map_image'].' deleted','Building::afterDelete');
			else
				Yii::trace('Error in deleting: '.$deleteDir.$this->_oldValues['map_image'],'Building::afterDelete');
		}

		// Delete street_image
		if (!is_dir($deleteDir.$this->_oldValues['street_image']) && file_exists($deleteDir.$this->_oldValues['street_image']))
		{
			if (unlink($deleteDir.$this->_oldValues['street_image']))
				Yii::trace($deleteDir.$this->_oldValues['street_image'].' deleted','Building::afterDelete');
			else
				Yii::trace('Error in deleting: '.$deleteDir.$this->_oldValues['street_image'],'Building::afterDelete');
		}
		
		unset($this->_oldValues);
		Yii::trace('$this->_oldValues removed','Building::afterDelete');
		
		Yii::trace('End','Building::afterDelete');
		return parent::afterDelete();
	}
}