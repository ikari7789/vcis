<?php

class BuildingController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
				'actions'=>array('admin','create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete'),
				'users'=>array('admin'),
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
		
		// Load data for floor dropdown
		$floors = CHtml::listData($model->floors, 'id', 'level');
		foreach ($floors as &$floor)
			$floor='Floor '.$floor;
		
		// Load floor images
		$floorImages = array();
		foreach ($model->floors as $floorModel) {
			if ($floorModel->level == 1)
				$class='floor-image display';
			else {
				$class='floor-image';
			}
			$floorImages[] = CHtml::image(
				Yii::app()->request->baseUrl.'/images/floors/'.$floorModel->map_image,
				$model->name.' - Floor '.$floorModel->level,
				array(
					'class'=>$class,
					'id'=>'floor_'.$floorModel->id.'_map',
				)
			);
		}
		
		// Create Javascript for Floor image preloading
		$floorImageJs = '';
		foreach ($floorImages as $image)
			$floorImageJs.="$('".$image."').load(function(){\$('.map-images').prepend(\$(this))});\n";

		
		// Load rooms for first floor
		$rooms = Floor::Model()->with('rooms')->findByAttributes(array('level'=>'1','building_id'=>$id),array('order'=>'rooms.number ASC'));
		$rooms = $rooms->rooms;
		
		// Load room images
		$roomImages = array();
		foreach($rooms as $room)
			$roomImages[] = CHtml::image(
				Yii::app()->request->baseUrl.'/images/rooms/'.$room->map_image,
				$model->name.' - Floor '.$room->floor->level.' - '.$room->number,
				array(
					'class'=>'room-image',
					'id'=>'room_'.$room->id.'_map',
				)
			);
		// Create JavaScript for Room image preloading
		$roomImageJs = '';
		foreach($roomImages as $image)
			$roomImageJs.="$('".$image."').load(function(){\$('.map-images').prepend(\$(this))});\n";		

		$this->render('view',array(
			'model'=>$model,
			'floors'=>$floors,
			'floorImageJs'=>$floorImageJs,
			'rooms'=>$rooms,
			'roomImageJs'=>$roomImageJs,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Building;
		$floors=array();

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Building']))
		{
			$model->attributes=$_POST['Building'];
			$model->map_image = CUploadedFile::getInstance($model,'map_image');
			if($model->save()) {
				
				// Create floors and link to building				
				// Rework the $_FILES array
				if (isset($_FILES['Floor'])) {
					foreach ($_FILES['Floor']['name'] as $key => $name) {
						if ($_FILES['Floor']['name'][$key]['map_image'] != '') {
							// Create CUploadedFile for floor model
							$name      = $_FILES['Floor']['name'][$key]['map_image'];
							$tempName  = $_FILES['Floor']['tmp_name'][$key]['map_image'];
							$type      = $_FILES['Floor']['type'][$key]['map_image'];
							$size      = $_FILES['Floor']['size'][$key]['map_image'];
							$error     = $_FILES['Floor']['error'][$key]['map_image'];
							$map_image = new CUploadedFile($name, $tempName, $type, $size, $error);
							Yii::trace('BuildingController::actionCreate->CUploadedFile object: '.$map_image->__toString());
		
							// Create Floor object
							$floor = new Floor;
							Yii::trace('BuildingController::New Floor object instantiated.');
							$floor->level = $key;
							$floor->building_id = $model->id;
							$floor->map_image = $map_image;
							if ($floor->save())
								Yii::trace('BuildingController::floor object saved');
							else {
								Yii::trace('BuildingController::error in saving floor object');
								foreach($floor->getErrors() as $error) {
									foreach($error as $value=>$key)
										Yii::trace('BuildingController::ERROR: '.$value.'=>'.$key);
								}
							}
							$floors[] = $floor;
						}
					}
				}
				
				// Check if there were any errors in creation.
				$errors = false;
				foreach ($floors as $floor)
					if ($floor->hasErrors())
						$errors = true;
				// if no errors, finish, else, bring back to create page
				if (!$errors)
					//$this->redirect(array('view','id'=>$model->id));
					$this->redirect(array('admin'));
			}
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
			$model->attributes=$_POST['Building'];
			
			$new_map_image = CUploadedFile::getInstance($model, 'map_image');
			if (is_object($new_map_image) && get_class($new_map_image)==='CUploadedFile')
				$model->map_image = $new_map_image;
			else
				$model->map_image = $old_map_image;

			if($model->save()) {
				
				// Create floors and link to building				
				// Rework the $_FILES array
				foreach ($_FILES['Floor']['name'] as $level => $name) {
					if ($_FILES['Floor']['name'][$level]['map_image'] != '') {
						// Create CUploadedFile for floor model
						$name      = $_FILES['Floor']['name'][$level]['map_image'];
						$tempName  = $_FILES['Floor']['tmp_name'][$level]['map_image'];
						$type      = $_FILES['Floor']['type'][$level]['map_image'];
						$size      = $_FILES['Floor']['size'][$level]['map_image'];
						$error     = $_FILES['Floor']['error'][$level]['map_image'];
						$new_map_image = new CUploadedFile($name, $tempName, $type, $size, $error);
						Yii::trace('BuildingController::actionCreate->CUploadedFile object: '.$new_map_image->__toString());
	
						// Create Floor object
						if (!isset($floors[$level])) {
							$floor = new Floor;
							Yii::trace('BuildingController::New Floor object instantiated.');
							$floor->level = $level;
							$floor->building_id = $model->id;
						} else {
							$floor = $floors[$level];
						}
						if (is_object($new_map_image) && get_class($new_map_image)==='CUploadedFile')
							$floor->map_image = $new_map_image;
						if ($floor->save())
							Yii::trace('BuildingController::floor object saved');
						else {
							Yii::trace('BuildingController::error in saving floor object');
							foreach($floor->getErrors() as $error) {
								foreach($error as $value=>$key)
									Yii::trace('BuildingController::ERROR: '.$value.'=>'.$key);
							}
						}
						$floors[] = $floor;
					}
				}
				// Check if there were any errors in creation.
				$errors = false;
				foreach ($floors as $floor)
					if ($floor->hasErrors())
						$errors = true;
					
				// if no errors, finish, else, bring back to create page
				if (!$errors)
					$this->redirect(array('admin')); 	
			}
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
