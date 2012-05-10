<?php

class BuildingController extends Controller
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
				'actions'=>array('index','view','ajaxFloors'),
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
		// Load building model
		$model = Building::model()->with('floors')->findByPk($id,array('order'=>'floors.level ASC'));
		
		if (!$model)
			throw new CHttpException(404,'The requested page does not exist.');
		
		// Load data for floor dropdown
		$floors = CHtml::listData($model->floors, 'id', 'level');
		$floorImageJs = '';
		$rooms = array(new Room);
		$roomImageJs = '';
		$streetImageJs = '';
		if ($floors)
		{
			foreach ($floors as &$floor) {
				if ($floor == 0)
					$floor = 'Basement';
				else
					$floor='Floor '.$floor;
			}
		
			// Load floor images
			$floorImages = array();
			$rootPath = pathinfo(Yii::app()->request->scriptFile);
			foreach ($model->floors as $floorModel) {
				$imageLocation = $rootPath['dirname'].'/images/floors/'.$floorModel->map_image;
				if (!is_dir($imageLocation) && file_exists($imageLocation))
					$imageLink = Yii::app()->request->baseUrl.'/images/floors/'.$floorModel->map_image;
				else
					$imageLink = Yii::app()->request->baseUrl.'/images/floors/map-default-'.$floorModel->level.'.jpg';
				
				$floorImages[] = CHtml::image(
					$imageLink,
					$model->name.' - Floor '.$floorModel->level,
					array(
						'class'=>'floor-image',
						'id'=>'floor_'.$floorModel->id.'_map',
					)
				);
			}
			
			// Create Javascript for Floor image preloading
			foreach ($floorImages as $image)
				$floorImageJs.="$('".$image."').load(function(){\$('.map-images').prepend(\$(this))});\n";
	
			/*
			
			// Load rooms for first floor
			$rooms = Floor::Model()->with('rooms')->findByAttributes(array('level'=>'1','building_id'=>$id),array('order'=>'rooms.number ASC'));
			$rooms = $rooms->rooms;
			
			// Load room images
			$roomImages = array();
			foreach($rooms as $room) {
				$imageLocation = $rootPath['dirname'].'/images/rooms/'.$room->map_image;
				if (!is_dir($imageLocation) && file_exists($imageLocation))
					$imageLink = Yii::app()->request->baseUrl.'/images/rooms/'.$room->map_image;
				else
				{
					$imageLocation = $rootPath['dirname'].'/images/floors/'.$room->floor->map_image;
					if (!is_dir($imageLocation) && file_exists($imageLocation))
						$imageLink = Yii::app()->request->baseUrl.'/images/floors/'.$room->floor->map_image;
					else
						$imageLink = Yii::app()->request->baseUrl.'/images/floors/map-default-'.$room->floor->level.'.jpg';
				}
				
				$roomImages[] = CHtml::image(
					$imageLink,
					$model->name.' - Floor '.$room->floor->level.' - '.$room->number,
					array(
						'class'=>'room-image',
						'id'=>'room_'.$room->id.'_map',
					)
				);
			}
			// Create JavaScript for Room image preloading
			foreach($roomImages as $image)
				$roomImageJs.="$('".$image."').load(function(){\$('.map-images').prepend(\$(this))});\n";
		
			*/
			
			// Create JavaScript for Street Image preloading
			$imageLocation = $rootPath['dirname'].'/images/buildings/'.$model->street_image;
			if (!is_dir($imageLocation) && file_exists($imageLocation))
				$imageLink = Yii::app()->request->baseUrl.'/images/buildings/'.$model->street_image;
			else
				$imageLink = Yii::app()->request->baseUrl.'/images/buildings/street-default.jpg';
			
			// Create JavaScript for Street Image preloading
			$image = CHtml::image(
				$imageLink,
				$model->name,
				array(
					'class'=>'street-image display',
					'id'=>'building_'.$model->id.'_street',
				)
			);
			$streetImageJs="$('".$image."').load(function(){\$('.map-images').prepend(\$(this))});\n";
		}	

		$this->render('view',array(
			'model'=>$model,
			'floors'=>$floors,
			'floorImageJs'=>$floorImageJs,
			'rooms'=>$rooms,
			'roomImageJs'=>$roomImageJs,
			'streetImageJs'=>$streetImageJs,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		if (!Yii::app()->user->checkAccess('createBuilding', Yii::app()->user->id))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		$model=new Building;
		$floors=array();

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Building']))
		{
			$model->attributes=$_POST['Building'];
			$model->map_image = CUploadedFile::getInstance($model,'map_image');
			$model->street_image = CUploadedFile::getInstance($model,'street_image');
			if($model->save()) {
					
				// check for basement
				$floorStart = 1;
				if (isset($_POST['basement']))
					$floorStart = 0;
				
				// Create floors and link to building				
				// Rework the $_FILES array				
				for ($floorNum = $floorStart; $floorNum <= $_POST['floorNum']; $floorNum++)
				{
					// Create Floor object
					$floor = new Floor;
					Yii::trace('BuildingController::New Floor object instantiated.');
					
					$floor->level = $floorNum;
					$floor->building_id = $model->id;
					
					if (isset($_FILES['Floor']['name'][$floorNum]['map_image']))
					{
						if ($_FILES['Floor']['name'][$floorNum]['map_image'] != '')
						{
							// Create CUploadedFile for floor model
							$name      = $_FILES['Floor']['name'][$floorNum]['map_image'];
							$tempName  = $_FILES['Floor']['tmp_name'][$floorNum]['map_image'];
							$type      = $_FILES['Floor']['type'][$floorNum]['map_image'];
							$size      = $_FILES['Floor']['size'][$floorNum]['map_image'];
							$error     = $_FILES['Floor']['error'][$floorNum]['map_image'];
							$map_image = new CUploadedFile($name, $tempName, $type, $size, $error);
							Yii::trace('BuildingController::actionCreate->CUploadedFile object: '.$map_image->__toString());
							
							$floor->map_image = $map_image;
						} // end if
					} // end if
					
					if ($floor->save())
						Yii::trace('BuildingController::floor object saved');
					else 
					{
						Yii::trace('BuildingController::error in saving floor object');
						foreach($floor->getErrors() as $error)
						{
							foreach($error as $value=>$key)
								Yii::trace('BuildingController::ERROR: '.$value.'=>'.$key);
						}
					} // end if
					
					$floors[] = $floor;
				} // end for
				
				// Check if there were any errors in creation.
				$errors = false;
				foreach ($floors as $floor)
					if ($floor->hasErrors())
						$errors = true;
				// if no errors, finish, else, bring back to create page
				if (!$errors)
					$this->redirect(array('admin'));
			} // end if model->save
		}

		$this->render('create',array(
			'model'=>$model,
			'floors'=>$floors,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		if (!Yii::app()->user->checkAccess('updateBuilding', Yii::app()->user->id))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		$model=$this->loadModel($id);
		$floors=array();
		foreach($model->floors as $floor) {
			$floors[$floor->level] = $floor;
		}

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Building']))
		{
			$old_map_image = $model->map_image;
			$old_street_image = $model->street_image;
			$model->attributes=$_POST['Building'];
			
			$new_map_image = CUploadedFile::getInstance($model, 'map_image');
			if (is_object($new_map_image) && get_class($new_map_image)==='CUploadedFile')
				$model->map_image = $new_map_image;
			else
				$model->map_image = $old_map_image;
			
			$new_street_image = CUploadedFile::getInstance($model, 'street_image');
			if (is_object($new_street_image) && get_class($new_street_image)==='CUploadedFile')
				$model->street_image = $new_street_image;
			else
				$model->street_image = $old_street_image;
			
			if($model->save()) {
				
				// Create floors and link to building				
				// Rework the $_FILES array
				$floorStart = 1;
				if (isset($_FILES['Floor']['name'][0]['map_image']))
					$floorStart = 0;
				
				$maxFloors = count($floors);
				if ($floorStart == 1)
					$maxFloors = count($floors) + 1;
				
				for ($floorNum = $floorStart; $floorNum < $maxFloors; $floorNum++)
				{
					// Create Floor object
					$floor = $floors[$floorNum];
					Yii::trace('Updating floor record.','BuildingController::actionUpdate');
					
					if (isset($_FILES['Floor']['name'][$floorNum]['map_image'])
					 && !empty($_FILES['Floor']['name'][$floorNum]['map_image']))
					{
						// Create CUploadedFile for floor model
						$name      = $_FILES['Floor']['name'][$floorNum]['map_image'];
						$tempName  = $_FILES['Floor']['tmp_name'][$floorNum]['map_image'];
						$type      = $_FILES['Floor']['type'][$floorNum]['map_image'];
						$size      = $_FILES['Floor']['size'][$floorNum]['map_image'];
						$error     = $_FILES['Floor']['error'][$floorNum]['map_image'];
						$new_map_image = new CUploadedFile($name, $tempName, $type, $size, $error);
						Yii::trace('CUploadedFile object: '.$new_map_image,'BuildingController::actionUpdate');
						
						if (is_object($new_map_image) && get_class($new_map_image)==='CUploadedFile')
							$floor->map_image = $new_map_image;
					} // end if
					
					if ($floor->save())
						Yii::trace('floor object saved','BuildingController::actionUpdate');
					else 
					{
						Yii::trace('Error in saving floor object','BuildingController::actionUpdate');
						foreach($floor->getErrors() as $error)
						{
							foreach($error as $value=>$key)
								Yii::trace('ERROR: '.$value.'=>'.$key,'BuildingController::actionUpdate');
						}
					} // end if
					
					$floors[$floorNum] = $floor;
				} // end for
				
				// Check if there were any errors in creation.
				$errors = false;
				foreach ($floors as $floor)
					if ($floor->hasErrors())
						$errors = true;
				// if no errors, finish, else, bring back to create page
				if (!$errors)
					$this->redirect(array('admin'));
			} // end if model->save
		}

		$this->render('update',array(
			'model'=>$model,
			'floors'=>$floors,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if (!Yii::app()->user->checkAccess('deleteBuilding', Yii::app()->user->id))
			throw new CHttpException(403, 'You are not authorized to perform this action.');
		
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
		$dataProvider=new CActiveDataProvider('Building');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$this->layout='//layouts/column1';
		if (!Yii::app()->user->checkAccess('manageBuilding', Yii::app()->user->id))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		$model=new Building('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Building']))
			$model->attributes=$_GET['Building'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	/**
	 * Return a list of floors related to a building
	 */
	public function actionAjaxFloors()
	{
		if (isset($_POST['building_id'])){
			$model = $this->loadModel($_POST['building_id']);
			$data = CHtml::listData($model->floors, 'id', 'level');
			foreach($data as $value=>$name)
			{
				if ($name == 0)
					echo CHtml::tag('option',array('value'=>$value),CHtml::encode('Basement'),true);
				else
					echo CHtml::tag('option',array('value'=>$value),CHtml::encode($name),true);
			}
		}
	}
	
	public function actionAjaxMapImage()
	{
		if (Yii::app()->request->isPostRequest)
		{
			if (isset($_POST['building_id']))
			{
				$model = Building::model()->findByPk($_POST['building_id']);
				
				echo $model->map_image;
			}
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Building::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='building-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
