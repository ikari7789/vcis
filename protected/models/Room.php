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
 *
 * The followings are the available model relations:
 * @property Floor $floor
 * @property Feature[] $features
 */
class Room extends CActiveRecord
{
	
	private $_oldValues = array();
	
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
	 * @return Room the static model class
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
			array('status, floor_id', 'numerical', 'integerOnly'=>true),
			array('number', 'length', 'max'=>10),
			array('front_image, back_image, map_image', 'length', 'max'=>255, 'allowEmpty'=>true),
			array('front_image, back_image, map_image',
				'file',
				'types'=>'jpg, gif, png',
				'allowEmpty'=>true,
				'maxSize'=>1024 * 1024 * 10, // 10MB
				'tooLarge'=>'The file was larger than 50MB. Please upload a smaller file.',
			),
			array('description', 'safe'),
			//array('front_image, back_image, map_image', 'unsafe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, number, front_image, back_image, map_image, status, description, floor_id, create_time, update_time', 'safe', 'on'=>'search'),
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
			'floor' => array(self::BELONGS_TO, 'Floor', 'floor_id'),
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
		$criteria->compare('number',$this->number,true);
		//$criteria->compare('front_image',$this->front_image,true);
		//$criteria->compare('back_image',$this->back_image,true);
		//$criteria->compare('map_image',$this->map_image,true);
		$criteria->compare('status',$this->status);
		//$criteria->compare('description',$this->description,true);
		$criteria->compare('floor_id',$this->floor_id);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function beforeSave() {
		return parent::beforeSave();
	}
	
	public function afterSave() {
		Yii::trace('begin','Room::afterSave');

		// Define save directory
		$baseDir = Yii::getPathOfAlias('siteDir');
		$uploadDir = $baseDir.'/images/rooms/';
		Yii::trace('Image save location: '.$uploadDir,'Room::afterSave');
		$baseFname = $this->floor->building_id.'_'.$this->floor->id.'_'.$this->id.'_';
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
					if ($img->width > $img->height)
						$img->resizeToWidth(582);
					else
						$img->resizeToHeight(322);
					$img->save($file);	
					
					$update = true;
					Yii::trace('Map Image: File saved successfully.','Room::afterSave');
				} else {
					Yii::trace('Map Image: Error in saving file.','Room::afterSave');
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
					$img->save($uploadDir.basename($file,'.'.$fileExt).'_large.'.$fileExt);
					if ($img->width > $img->height)
						$img->resizeToWidth(425);
					else
						$img->resizeToHeight(318);
					$img->save($file);	
					
					$update = true;
					Yii::trace('Back Image: File saved successfully.','Room::afterSave');
				} else {
					Yii::trace('Back Image: Error in saving file.','Room::afterSave');
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
					$img->save($uploadDir.basename($file,'.'.$fileExt).'_large.'.$fileExt);
					if ($img->width > $img->height)
						$img->resizeToWidth(425);
					else
						$img->resizeToHeight(318);
					$img->save($file);
					
					$update = true;
					Yii::trace('Front Image: File saved successfully.','Room::afterSave');
				} else {
					Yii::trace('Front Image: Error in saving file.','Room::afterSave');	
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

	public function afterFind()
	{
		$this->_oldValues = $this->attributes;
		Yii::trace('Model backup created.','Room::afterFind');
		return parent::afterFind();
	}

	public function afterDelete()
	{
		Yii::trace('Begin','Room::afterDelete');
		
		// Define delete directory
		$baseDir = Yii::getPathOfAlias('siteDir');
		$deleteDir = $baseDir.'/images/rooms/';
		Yii::trace('Image save location: '.$deleteDir,'Room::afterDelete');
		
		// Delete map_image
		Yii::trace('Attempting to delete: '.$deleteDir.$this->_oldValues['map_image'],'Room::afterDelete');
		if (file_exists($deleteDir.$this->_oldValues['map_image']))
		{
			if (unlink($deleteDir.$this->_oldValues['map_image']))
				Yii::trace($deleteDir.$this->_oldValues['map_image'].' deleted','Room::afterDelete');
			else
				Yii::trace('Error in deleting: '.$deleteDir.$this->_oldValues['map_image'],'Room::afterDelete');
		}
		
		// Delete front_image
		Yii::trace('Attempting to delete: '.$deleteDir.$this->_oldValues['front_image'],'Room::afterDelete');
		if (file_exists($deleteDir.$this->_oldValues['front_image']))
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
		if (file_exists($deleteDir.$largeFile))
		{
			if (unlink($deleteDir.$largeFile))
				Yii::trace($deleteDir.$largeFile.' deleted','Room::afterDelete');
			else
				Yii::trace('Error in deleting: '.$deleteDir.$largeFile,'Room::afterDelete');
		}

		// Delete back_image
		Yii::trace('Attempting to delete: '.$deleteDir.$this->_oldValues['back_image'],'Room::afterDelete');
		if (file_exists($deleteDir.$this->_oldValues['back_image']))
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
		if (file_exists($deleteDir.$largeFile))
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
		$model->verified = $verified;
		Yii::trace('$model->verified: '.$model->verified,'Room::createFeature');
		
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
			if ($model->details == $details && $model->verified == $verified)
			{
				Yii::trace('No changes found in feature. Returning.','Room::addFeature');
				return true;
			}
			$model->details = $details;
			$model->verified = $verified;
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