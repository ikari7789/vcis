<?php

/**
 * This is the model class for table "room".
 *
 * The followings are the available columns in table 'room':
 * @property integer $id
 * @property string $number
 * @property string $front_image
 * @property string $back_image
 * @property string $map_image
 * @property integer $status
 * @property string $description
 * @property integer $floor_id
 * @property string $create_time
 * @property string $update_time
 * @property integer $create_user_id
 * @property integer $update_user_id
 *
 * The followings are the available model relations:
 * @property Floor $floor
 * @property User $createUser
 * @property User $updateUser
 * @property Feature[] $features
 */
class Room extends ActiveRecordBase
{
	private $_oldValues = array();
	public $building_name;
	public $floor_level;
	public $status_text;
	
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
		return 'room';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('number, status, floor_id', 'required'),
			array('number', 'unique'),
			array('number', 'match', 'pattern'=>'/[A-Z]{2}\d+/', 'message'=>'Must be in format of 2 capital letters and a number. Ex. HH1000'),
			array('status, floor_id', 'numerical', 'integerOnly'=>true),
			array('number', 'length', 'max'=>10),
			array('front_image, back_image, map_image', 'length', 'max'=>255),
			array('front_image, back_image, map_image', 'file',
				'maxSize' => 1024 * 1024 * 2, // max filesize allowed
				'tooLarge' => 'File must be less than 2MB',
				'types' => array('jpg', 'jpeg', 'gif', 'png'),
				'wrongType' => 'File can only be (.jpg, .gif, .png)',
				'allowEmpty'=>true,
			),
			array('description','safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, building_name, floor_level, status_text, number, front_image, back_image, map_image, status, description, floor_id, create_time, update_time, create_user_id, update_user_id', 'safe', 'on'=>'search'),
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
			'floor' => array(self::HAS_ONE, 'Floor', array('id'=>'floor_id')),
			'building' => array(self::HAS_ONE, 'Building', array('building_id'=>'id'), 'through'=>'floor'),
			'createUser' => array(self::HAS_ONE, 'User', 'create_user_id'),
			'updateUser' => array(self::HAS_ONE, 'User', 'update_user_id'),
			'features' => array(self::MANY_MANY, 'Feature', 'room_feature(room_id, feature_id)'),
			'room_features' => array(self::HAS_MANY, 'RoomFeature', 'room_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'number' => 'Number',
			'front_image' => 'Front Image',
			'back_image' => 'Back Image',
			'map_image' => 'Map Image',
			'status' => 'Status',
			'description' => 'Description',
			'floor_id' => 'Floor',
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

		// sub query to retrieve the count of floors
		$status_text_sql = "CASE t.status WHEN 0 THEN 'offline' WHEN 1 THEN 'online' END";
		
		// select
		$criteria->select = array(
			'*',
			$status_text_sql." as status_text",
		);
		
		// with
		$criteria->with = array('floor', 'building');

		//$criteria->compare('id',$this->id);
		$criteria->compare('building.name',$this->building_name,true);
		$criteria->compare('floor.level',$this->floor_level);
		$criteria->compare('t.number',$this->number,true);
		//$criteria->compare('t.front_image',$this->front_image,true);
		//$criteria->compare('t.back_image',$this->back_image,true);
		//$criteria->compare('t.map_image',$this->map_image,true);
		$criteria->compare($status_text_sql,$this->status_text);
		//$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.create_time',$this->create_time,true);
		$criteria->compare('t.update_time',$this->update_time,true);
		//$criteria->compare('t.create_user_id',$this->create_user_id);
		//$criteria->compare('t.update_user_id',$this->update_user_id);
		
		//$criteria->order='building.name, floor.level, t.number';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'building.name, t.number',
				'attributes'=>array(
					'building_name'=>array(
						'asc'=>'building.name',
						'desc'=>'building.name DESC',
						'label'=>'Building',
					),
					'floor_level'=>array(
						'asc'=>'floor.level',
						'desc'=>'floor.level DESC',
						'label'=>'Floor',
					),
					'status_text'=>array(
						'asc'=>'status_text',
						'desc'=>'status_text DESC',
						'label'=>'Status',
					),
					'*',
				),
			),
		));
	}
	
	/**
	 * Save current record to temp variable to be able to rollback any changes.
	 */
	public function afterFind()
	{		
		$this->_oldValues = $this->attributes;
		Yii::trace('Model backup created.','Building::afterFind');
		
		$rootPath = pathinfo(Yii::app()->request->scriptFile);
		$imageLocation = $rootPath['dirname'].'/images/rooms/'.$this->front_image;
		if (is_dir($imageLocation) || !file_exists($imageLocation))
			$this->front_image = 'front-default.jpg';
				
		$imageLocation = $rootPath['dirname'].'/images/rooms/'.$this->back_image;
		if (is_dir($imageLocation) || !file_exists($imageLocation))
			$this->back_image = 'back-default.jpg';
		
		return parent::afterFind();
	}

	protected function beforeSave() {
		Yii::trace('Begin','Room::beforeSave');
		
		// Define save directory
		$rootPath = pathinfo(Yii::app()->request->scriptFile);
		$baseDir = $rootPath['dirname'];
		$uploadDir = $baseDir.'/images/floors/';
		Yii::trace('Image save location: '.$uploadDir,'Room::beforeSave');
		// if new map_image attempted to be uploaded
		// backup old image
		if (isset($this->_oldValues['map_image']) && is_object($this->map_image) && get_class($this->map_image)==='CUploadedFile') {
			$oldFile = $uploadDir.$this->_oldValues['map_image'];
			$newFile = $oldFile.'.bak';
			if (!is_dir($oldFile) && file_exists($oldFile))
				rename($oldFile, $newFile);
		}

		// if new front_image attempted to be uploaded
		// backup old image
		if (isset($this->_oldValues['front_image']) && is_object($this->front_image) && get_class($this->front_image)==='CUploadedFile') {
			// front_image
			$oldFile = $uploadDir.$this->_oldValues['front_image'];
			$newFile = $oldFile.'.bak';
			if (!is_dir($oldFile) && file_exists($oldFile))
				rename($oldFile, $newFile);
			
			// front_image_large
			$oldFile = $uploadDir.substr($this->_oldValues['front_image'],0,-4).'_large'.substr($this->_oldValues['front_image'],-4).'.bak';
			$newFile = $oldFile.'.bak';
			if (!is_dir($oldFile) && file_exists($oldFile))
				rename($oldFile, $newFile);
		}
		
		// if new back_image attempted to be uploaded
		// backup old image
		if (isset($this->_oldValues['back_image']) && is_object($this->back_image) && get_class($this->back_image)==='CUploadedFile') {
			// back_image
			$oldFile = $uploadDir.$this->_oldValues['back_image'];
			$newFile = $oldFile.'.bak';
			if (!is_dir($oldFile) && file_exists($oldFile))
				rename($oldFile, $newFile);
			
			// back_image_large
			$oldFile = $uploadDir.substr($this->_oldValues['back_image'],0,-4).'_large'.substr($this->_oldValues['back_image'],-4).'.bak';
			$newFile = $oldFile.'.bak';
			if (!is_dir($oldFile) && file_exists($oldFile))
				rename($oldFile, $newFile);
		}

		return parent::beforeSave();
	}
	
	public function afterSave() {
		Yii::trace('begin','Room::afterSave');

		// Define save directory
		$rootPath = pathinfo(Yii::app()->request->scriptFile);
		$baseDir = $rootPath['dirname'];
		$uploadDir = $baseDir.'/images/rooms/';
		Yii::trace('Image save location: '.$uploadDir,'Room::afterSave');
		/*
		Yii::trace('Building ID: '.$this->building->id,'Room::afterSave');
		Yii::trace('Floor ID: '.$this->floor->id,'Room::afterSave');
		Yii::trace('Room ID: '.$this->id,'Room::afterSave');
		$baseFname = $this->building->id.'_'.$this->floor->id.'_'.$this->id.'_';
		*/
		$floor = Floor::model()->findByPk($this->floor_id);
		Yii::trace('Building ID: '.$floor->building->id,'Room::afterSave');
		Yii::trace('Floor ID: '.$floor->id,'Room::afterSave');
		Yii::trace('Room ID: '.$this->id,'Room::afterSave');
		$baseFname = $floor->building->id.'_'.$floor->id.'_'.$this->id.'_';
		$update = false;

		// Make sure the image is an uploaded image, otherwise leave image alone
		if (is_object($this->map_image) && get_class($this->map_image)==='CUploadedFile') {
			Yii::trace('Map Image: CUploadedFile object found.','Room::afterSave');
			
			// Define filename
			$newfname = $baseFname.'map.'.$this->map_image->extensionName;
			Yii::trace('Map Image: New filename: '.$newfname,'Room::afterSave');
			
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
				Yii::trace('Map Image: File saved successfully.','Room::afterSave');
				
				if (isset($this->_oldValues['map_image'])) {
					// delete backed up image if it exists
					$imgBak = $this->_oldValues['map_image'].'.bak';
					if (!is_dir($uploadDir.$imgBak) && file_exists($uploadDir.$imgBak))
						unlink($uploadDir.$imgBak);
				}
			} else {
				Yii::trace('Map Image: Error in saving file.','Room::afterSave');
				
				if (isset($this->_oldValues['map_image'])) {
					// restore backed up image if it exists
					$imgBak = $this->_oldValues['map_image'].'.bak';
					$imgRestore = substr($imgBak, 0, -4);
					if (!is_dir($uploadDir.$imgBak) && file_exists($uploadDir.$imgBak))
						rename($uploadDir.$imgBak, $uploadDir.$imgRestore);
				}
			}
		}

		// Check back image
		if (is_object($this->back_image) && get_class($this->back_image)==='CUploadedFile') {
			Yii::trace('Back Image: CUploadedFile object found.','Room::afterSave');
			
			// Define filename
			$newfname = $baseFname.'back.'.$this->back_image->extensionName;
			Yii::trace('Back Image: New filename: '.$newfname,'Room::afterSave');
			
			Yii::trace('Back Image: Full filename + directory: '.$uploadDir.$newfname,'Room::afterSave');
			
			// Save file
			if ($this->back_image->saveAs($uploadDir.$newfname)) {
				$fileExt = $this->back_image->extensionName;
				$this->back_image = $newfname;
				
				// resize image
				$file = $uploadDir.$newfname;
				$img = Yii::app()->simpleImage->load($file);
				// keep original image
				$filePath = $uploadDir.basename($file,'.'.$fileExt).'_large.'.$fileExt;
				$img->save($filePath);
				if ($img->getWidth() > 425)
					$img->resizeToWidth(425);
				$img->save($file);	
				
				$update = true;
				Yii::trace('Back Image: File saved successfully.','Room::afterSave');
			
				if (isset($this->_oldValues['back_image'])) {
					// delete backed up back_image if it exists
					$imgBak = $this->_oldValues['back_image'].'.bak';
					if (!is_dir($uploadDir.$imgBak) && file_exists($uploadDir.$imgBak))
						unlink($uploadDir.$imgBak);
					
					// delete backed up back_image_large if it exists
					$imgBak = substr($this->_oldValues['back_image'],0,-4).'_large'.substr($this->_oldValues['back_image'],-4).'.bak';
					if (!is_dir($uploadDir.$imgBak) && file_exists($uploadDir.$imgBak))
						unlink($uploadDir.$imgBak);
				}
			} else {
				Yii::trace('Back Image: Error in saving file.','Room::afterSave');
				
				if (isset($this->_oldValues['back_image'])) {
					// restore backed up back_image if it exists
					$imgBak = $this->_oldValues['back_image'].'.bak';
					$imgRestore = substr($imgBak, 0, -4);
					if (!is_dir($uploadDir.$imgBak) && file_exists($uploadDir.$imgBak))
						rename($uploadDir.$imgBak, $uploadDir.$imgRestore);	
					
					// restore backed up back_image_large if it exists
					$imgBak = substr($this->_oldValues['back_image'],0,-4).'_large'.substr($this->_oldValues['back_image'],-4).'.bak';
					$imgRestore = substr($imgBak, 0, -4);
					if (!is_dir($uploadDir.$imgBak) && file_exists($uploadDir.$imgBak))
						rename($uploadDir.$imgBak, $uploadDir.$imgRestore);
				}
			}
		}

		// Check front image
		if (is_object($this->front_image) && get_class($this->front_image)==='CUploadedFile') {
			Yii::trace('Front Image: CUploadedFile object found.','Room::afterSave');
			
			// Define filename
			$newfname = $baseFname.'front.'.$this->front_image->extensionName;
			Yii::trace('Front Image: New filename: '.$newfname,'Room::afterSave');
			
			Yii::trace('Front Image: Full filename + directory: '.$uploadDir.$newfname,'Room::afterSave');
			
			// Save file
			if ($this->front_image->saveAs($uploadDir.$newfname)) {
				$fileExt = $this->front_image->extensionName;
				$this->front_image = $newfname;
				
				// resize image
				$file = $uploadDir.$newfname;
				$img = Yii::app()->simpleImage->load($file);
				// keep original image
				$filePath = $uploadDir.basename($file,'.'.$fileExt).'_large.'.$fileExt;
				if (!is_dir($filePath) && file_exists($filePath))
					unlink($filePath);
				$img->save($filePath);
				if ($img->getWidth() > 425)
					$img->resizeToWidth(425);
				$img->save($file);
				
				$update = true;
				Yii::trace('Front Image: File saved successfully.','Room::afterSave');
				
				if (isset($this->_oldValues['front_image'])) {
					// delete backed up front_image if it exists
					$imgBak = $this->_oldValues['front_image'].'.bak';
					if (!is_dir($uploadDir.$imgBak) && file_exists($uploadDir.$imgBak))
						unlink($uploadDir.$imgBak);
					
					// delete backed up front_image_large if it exists
					$imgBak = substr($this->_oldValues['front_image'],0,-4).'_large'.substr($this->_oldValues['front_image'],-4).'.bak';
					if (!is_dir($uploadDir.$imgBak) && file_exists($uploadDir.$imgBak))
						unlink($uploadDir.$imgBak);
				}
			} else {
				Yii::trace('Front Image: Error in saving file.','Room::afterSave');
				
				if (isset($this->_oldValues['front_image'])) {
					// restore backed up front_image if it exists
					$imgBak = $this->_oldValues['front_image'].'.bak';
					$imgRestore = substr($imgBak, 0, -4);
					if (!is_dir($uploadDir.$imgBak) && file_exists($uploadDir.$imgBak))
						rename($uploadDir.$imgBak, $uploadDir.$imgRestore);
					
					// restore backed up front_image_large if it exists
					$imgBak = substr($this->_oldValues['front_image'],0,-4).'_large'.substr($this->_oldValues['front_image'],-4).'.bak';
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
		Yii::trace('end','Room::afterSave');
			
		return parent::afterSave();
	}

	public function afterDelete()
	{
		Yii::trace('Begin','Room::afterDelete');
		
		// Define delete directory
		$rootPath = pathinfo(Yii::app()->request->scriptFile);
		$baseDir = $rootPath['dirname'];
		$deleteDir = $baseDir.'/images/rooms/';
		Yii::trace('Image save location: '.$deleteDir,'Room::afterDelete');
		
		// Delete map_image
		Yii::trace('Attempting to delete: '.$deleteDir.$this->_oldValues['map_image'],'Room::afterDelete');
		if (!is_dir($deleteDir.$this->_oldValues['map_image']) && file_exists($deleteDir.$this->_oldValues['map_image']))
		{
			if (unlink($deleteDir.$this->_oldValues['map_image']))
				Yii::trace($deleteDir.$this->_oldValues['map_image'].' deleted','Room::afterDelete');
			else
				Yii::trace('Error in deleting: '.$deleteDir.$this->_oldValues['map_image'],'Room::afterDelete');
		}
		
		// Delete front_image
		Yii::trace('Attempting to delete: '.$deleteDir.$this->_oldValues['front_image'],'Room::afterDelete');
		if (!is_dir($deleteDir.$this->_oldValues['front_image']) && file_exists($deleteDir.$this->_oldValues['front_image']))
		{
			if (unlink($deleteDir.$this->_oldValues['front_image']))
				Yii::trace($deleteDir.$this->_oldValues['front_image'].' deleted','Room::afterDelete');
			else
				Yii::trace('Error in deleting: '.$deleteDir.$this->_oldValues['front_image'],'Room::afterDelete');
		}

		// Delete front_image_large
		$fileExt = substr($this->_oldValues['front_image'],-3);
		$largeFile = substr($this->_oldValues['front_image'],0,-4).'_large.'.$fileExt;
		Yii::trace('Attempting to delete: '.$deleteDir.$largeFile,'Room::afterDelete');
		if (!is_dir($deleteDir.$largeFile) && file_exists($deleteDir.$largeFile))
		{
			if (unlink($deleteDir.$largeFile))
				Yii::trace($deleteDir.$largeFile.' deleted','Room::afterDelete');
			else
				Yii::trace('Error in deleting: '.$deleteDir.$largeFile,'Room::afterDelete');
		}

		// Delete back_image
		Yii::trace('Attempting to delete: '.$deleteDir.$this->_oldValues['back_image'],'Room::afterDelete');
		if (!is_dir($deleteDir.$this->_oldValues['back_image']) && file_exists($deleteDir.$this->_oldValues['back_image']))
		{
			if (unlink($deleteDir.$this->_oldValues['back_image']))
				Yii::trace($deleteDir.$this->_oldValues['back_image'].' deleted','Room::afterDelete');
			else
				Yii::trace('Error in deleting: '.$deleteDir.$this->_oldValues['back_image'],'Room::afterDelete');
		}
		
		// Delete back_image_large
		$fileExt = substr($this->_oldValues['back_image'],-3);
		$largeFile = substr($this->_oldValues['back_image'],0,-4).'_large.'.$fileExt;
		Yii::trace('Attempting to delete: '.$deleteDir.$largeFile,'Room::afterDelete');
		if (!is_dir($deleteDir.$largeFile) && file_exists($deleteDir.$largeFile))
		{
			if (unlink($deleteDir.$largeFile))
				Yii::trace($deleteDir.$largeFile.' deleted','Room::afterDelete');
			else
				Yii::trace('Error in deleting: '.$deleteDir.$largeFile,'Room::afterDelete');
		}
		
		unset($this->_oldValues);
		Yii::trace('$this->_oldValues removed','Room::afterDelete');
		
		Yii::trace('End','Room::afterDelete');
		return parent::afterDelete();
	}
 
	/**
	 * Attach a feature to a room
	 * @param integer $featureId ID referrer for the feature that this detail references
	 * @param string $details The details pertaining to the specific room feature
	 * @param boolean $verification Whether the data entered has been verified or not.
	 * @return RoomFeature model
	 */
	protected function createFeature($featureId, $details, $verified=false)
	{
		Yii::trace('begin','Room::createFeature');
		$model = new RoomFeature;
		Yii::trace('New RoomFeature object created.','Room::createFeature');
		$model->room_id = $this->id;
		Yii::trace('$model->room_id: '.$model->room_id,'Room::createFeature');
		$model->feature_id = $featureId;
		Yii::trace('$model->feature_id: '.$model->feature_id,'Room::createFeature');
		$model->details = $details;
		Yii::trace('$model->details: '.$model->details,'Room::createFeature');
		if ($verified == 1)
			$model->verification_time = date( 'Y-m-d H:i:s', time());
		else
			$model->verification_time = NULL;
		Yii::trace('$model->verification_time: '.$model->verification_time,'Room::createFeature');
		
		Yii:: trace('End','Room::createFeature');
		return $model;
	}
	
	/**
	 * Attach a feature to a room
	 * @param integer $featureId ID referrer for the feature that this detail references
	 * @param string $details The details pertaining to the specific room feature
	 * @param boolean $verification Whether the data entered has been verified or not.
	 * @return boolean was the creation successful or not
	 */
	public function addFeature($featureId, $details, $verified=false)
	{
		Yii::trace('Begin','Room::addFeature');
		// See if this feature is already attached to this record, if so, get the model object
		Yii::trace('Looking to see if feature already exists.','Room::addFeature');
		$model = RoomFeature::model()->findByAttributes(array('room_id'=>$this->id,'feature_id'=>$featureId));
		if ($model === null)
		{
			Yii::trace('Feature not found, creating new feature.','Room::addFeature');
			$model = $this->createFeature($featureId, $details, $verified);
			$saveType = 'save';
		}
		else
		{
			Yii::trace('Feature: '.$model->feature->name.' found for Room: '.$model->room->number,'Room::addFeature');
			if ($model->details == $details && $model->verification_time == date( 'Y-m-d H:i:s', time()))
			{
				Yii::trace('No changes found in feature. Returning.','Room::addFeature');
				return true;
			}
			$model->details = $details;
			if ($verified)
				$model->verification_time = date( 'Y-m-d H:i:s', time());
			$saveType = 'update';
				
		}
		Yii::trace('Attempting to '.$saveType.' record.','Room::addFeature');
		if ($model->save())
		{
			Yii::trace('Save successful', 'Room::addFeature');
			Yii::trace('End','Room::addFeature');
			return true;
		}
		else
		{
			Yii::trace('Save unsuccessful: '.CHtml::errorSummary($model), 'Room::addFeature');
			Yii::trace('End','Room::addFeature');
			return $model;
		}
	}
	
	/**
	 * Remove a feature from a room
	 * @param integer $featureId
	 * @return boolean was the deletion successful or not
	 */	
	public function removeFeature($featureId)
	{
		Yii::trace('Begin','Room::removeFeature');
		Yii::trace('Loading model from database.','Room::removeFeature');
		$model=RoomFeature::model()->findByAttributes(array('room_id'=>$this->id,'feature_id'=>$featureId));
		if ($model != null)
		{
			Yii::trace('Attempting to delete feature from room.','Room::removeFeature');
			if ($model->delete())
			{
				Yii::trace('Model successfully deleted.','Room::removeFeature');
				return true;
			}
			else
			{
				Yii::trace('Error in deleting: '.CHtml::errorSummary($model), 'Room::removeFeature');
				return false;
			}
		}
		else
		{
			Yii::trace('Model not found.','Room::removeFeature');
			return false;
		}
	}
}