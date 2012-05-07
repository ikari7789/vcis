<?php

class RoomController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('admin','create','update','delete'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{		
		$model = $this->loadModel($id);
		$roomFeatures = array();
		foreach ($model->room_features as $feature) {
			$roomFeatures[$feature->feature->category->name][$feature->feature->name]['description'] = $feature->feature->description;
			$roomFeatures[$feature->feature->category->name][$feature->feature->name]['details'] = $feature;
		}
		
		$oldest = array();
		
		ksort($roomFeatures);
		foreach($roomFeatures as $categoryName => &$category)
		{
			ksort($category);
			$oldest[$categoryName] = time();
			
			foreach($category as $feature)
			{
				if (strtotime($feature['details']->verification_time) < $oldest[$categoryName])
				{
					if ($feature['details']->verification_time != '0000-00-00 00:00:00')
						$oldest[$categoryName] = strtotime($feature['details']->verification_time);
					else
						$oldest[$categoryName] = 0;
				}
			}
		}
		
		$rootPath = pathinfo(Yii::app()->request->scriptFile);
		$imageLocation = $rootPath['dirname'].'/images/rooms/'.$model->front_image;
		if (is_dir($imageLocation) || !file_exists($imageLocation))
			$model->front_image = 'front-default.jpg';
				
		$imageLocation = $rootPath['dirname'].'/images/rooms/'.$model->back_image;
		if (is_dir($imageLocation) || !file_exists($imageLocation))
			$model->back_image = 'back-default.jpg';

		$this->render('view',array(
			'model'=>$model,
			'roomFeatures'=>$roomFeatures,
			'oldest'=>$oldest,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		if (!Yii::app()->user->checkAccess('createRoom', Yii::app()->user->id))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		Yii::trace('Begin','RoomController::actionCreate');
		
		$model=new Room;
		Yii::trace("New Room model created.\n".print_r($model->getAttributes(), true),'RoomController::actionCreate');
		
		$buildings=CHtml::listData(Building::model()->findAll(array('order'=>'name ASC')), 'id', 'name');
		Yii::trace("Buildings loaded.",'RoomController::actionCreate');
		
		$floors=array();
		Yii::trace('Empty $floors array initialized.','RoomController::actionCreate');
		
		$categories=Category::model()->findAll(array('order'=>'name ASC'));
		Yii::trace("Categories loaded.",'RoomController::actionCreate');

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		Yii::trace('Is $_POST[\'Room\'] set?','RoomController::actionCreate');
		if(isset($_POST['Room']))
		{
			Yii::trace('$_POST[\'Room\'] is set.','RoomController::actionCreate');
			$model->attributes=$_POST['Room'];			
			$model->map_image = CUploadedFile::getInstance($model,'map_image');
			$model->front_image = CUploadedFile::getInstance($model,'front_image');
			$model->back_image = CUploadedFile::getInstance($model,'back_image');
			Yii::trace("Save entered data to \$model:\n".print_r($model->getAttributes(), true),'RoomController::actionCreate');
			
			if($model->save())
			{
				Yii::trace('$model saved successfully.','RoomController::actionCreate');
				
				// Save room features
				Yii::trace('Features to save?','RoomController::actionCreate');
				if (isset($_POST['RoomFeature']))
				{
					Yii::trace('Found features to save.','RoomController::actionCreate');
					foreach ($_POST['RoomFeature'] as $featureId => $feature)
					{
						if (!empty($feature['details']))
						{
							if (isset($feature['verified']))
								$feature['verified'] = 0;
							Yii::trace('Attempting to save feature: '.$featureId,'RoomController::actionCreate');
							$model->addFeature($featureId, $feature['details'], $feature['verified']);
						}
					}
				}
				
				Yii::trace('Redirect to view: admin','RoomController::actionCreate');
				$this->redirect(array('admin')); //$this->redirect(array('view','id'=>$model->id));
			}
		}

		Yii::trace('Render view: create','RoomController::actionCreate');
		$this->render('create',array(
			'model'=>$model,
			'buildings'=>$buildings,
			'floors'=>$floors,
			'categories'=>$categories,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		if (!Yii::app()->user->checkAccess('updateRoom', Yii::app()->user->id))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		Yii::trace('Begin','RoomController::actionUpdate');
		$model=$this->loadModel($id);
		Yii::trace('Model loaded','RoomController::actionUpdate');
		
		$buildings=CHtml::listData(Building::model()->findAll(array('order'=>'name ASC')), 'id', 'name');
		Yii::trace('Buildings loaded','RoomController::actionUpdate');
		
		$floors=CHtml::listData(Building::model()->findByPk($model->floor->building->id)->floors, 'id', 'level');
		Yii::trace('Floors loaded','RoomController::actionUpdate');
		
		$categories=Category::model()->findAll(array('order'=>'name ASC'));
		Yii::trace('Categories loaded','RoomController::actionUpdate');
		
		$roomFeatures = array();
		foreach ($model->room_features as $feature) {
			$roomFeatures[$feature->feature->id]['details'] = $feature->details;
			if (isset($feature->verification_time))
				$roomFeatures[$feature->feature->id]['verified'] = 1;
		}
		Yii::trace('Room Features loaded.','RoomController::actionUpdate');

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);
		
		Yii::trace('Is $_POST[\'Room\'] set?','RoomController::actionUpdate');
		if(isset($_POST['Room']))
		{
			Yii::trace('$_POST[\'Room\'] is set.','RoomController::actionUpdate');
			$map_image = $model->map_image;
			$front_image = $model->front_image;
			$back_image = $model->back_image;
			$model->attributes=$_POST['Room'];
			
			// Set map_image if new
			$new_map_image = CUploadedFile::getInstance($model, 'map_image');
			if (is_object($new_map_image) && get_class($new_map_image)==='CUploadedFile')
			{
				Yii::trace('Map image is new.','RoomController::actionUpdate');
				$model->map_image = $new_map_image;
			}
			else
			{
				$model->map_image = $map_image;
			}
			
			// Set front image if new
			$new_front_image = CUploadedFile::getInstance($model, 'front_image');
			if (is_object($new_front_image) && get_class($new_front_image)==='CUploadedFile')
			{
				Yii::trace('Front image is new.','RoomController::actionUpdate');
				$model->front_image = $new_front_image;
			}
			else
			{
				$model->front_image = $front_image;
			}
			
			// Set back image if new
			$new_back_image = CUploadedFile::getInstance($model, 'back_image');
			if (is_object($new_back_image) && get_class($new_back_image)==='CUploadedFile')
			{
				Yii::trace('Back image is new.','RoomController::actionUpdate');
				$model->back_image = $new_back_image;
			}
			else
			{
				$model->back_image = $back_image;
			}
			
			Yii::trace('Attempting to save changes.','RoomController::actionUpdate');
			Yii::trace("Model data:\n".print_r($model->getAttributes(), TRUE),'RoomController::actionUpdate');
			if($model->save()) 
			{
				Yii::trace('Save successful. Seeing if there are features to save.','RoomController::actionUpdate');
				// Save room features
				if (isset($_POST['RoomFeature']))
				{
					Yii::trace('Feature information found.','RoomController::actionUpdate');
					foreach ($_POST['RoomFeature'] as $featureId => $feature)
					{
						Yii::trace('Checking if feature ID: '.$featureId.' needs updating.','RoomController::actionUpdate');
						if (!empty($feature['details']))
						{
							Yii::trace('Feature has been entered for room. Attempting to add to database.','RoomController::actionUpdate');
							if (!isset($feature['verified']))
								$feature['verified'] = 0;
							Yii::trace("Attempting to add:\nFeature ID: ".$featureId."\nFeature Details: ".$feature['details']."\nFeature Verified: ".$feature['verified'],'RoomController::actionUpdate');
							$model->addFeature($featureId, $feature['details'], $feature['verified']);
						}
						else
						{
							Yii::trace('Nothing entered. Delete value from database.','RoomController::actionUpdate');
							$model->removeFeature($featureId);
						}
					}
				}
				
				Yii::trace('Redirecting to view: admin','RoomController::actionUpdate');
				$this->redirect(array('admin')); //$this->redirect(array('view','id'=>$model->id));
			}
		}

		Yii::trace('Rendering view: update','RoomController::actionUpdate');
		$this->render('update',array(
			'model'=>$model,
			'buildings'=>$buildings,
			'floors'=>$floors,
			'categories'=>$categories,
			'roomFeatures'=>$roomFeatures,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if (!Yii::app()->user->checkAccess('deleteRoom', Yii::app()->user->id))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Room');
		
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		if (!Yii::app()->user->checkAccess('manageRoom', Yii::app()->user->id))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		$model=new Room('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Room']))
			$model->attributes=$_GET['Room'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Room::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='room-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
