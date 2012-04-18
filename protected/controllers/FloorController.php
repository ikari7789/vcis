<?php

class FloorController extends Controller
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
				'actions'=>array('index','view','ajaxRooms'),
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
	 * Returns a list of rooms associated with a floor
	 */
	public function actionAjaxRooms()
	{
		if (Yii::app()->request->isPostRequest) {
			echo '<div>';
			$model = Floor::Model()->with('rooms')->findByPk($_POST['floors'],array('order'=>'rooms.number ASC'));
			foreach($model->rooms as $room) {
				echo '<li>'.CHtml::link($room->number, array('room/view', 'id'=>$room->id), array('id'=>'room_'.$room->id,'class'=>'room')).'</li>';
				echo CHtml::image(
					Yii::app()->request->baseUrl.'/images/rooms/'.$room->map_image,
					$model->building->name.' - Floor '.$model->level.' - '.$room->number,
					array(
						'class'=>'room-image',
						'id'=>'room_'.$room->id.'_map',
					)
				);
			}
			
			echo '</div>';
		}
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		if (!Yii::app()->user->checkAccess('createFloor', Yii::app()->user->id))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		$model=new Floor;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Floor']))
		{
			$model->attributes=$_POST['Floor'];
				
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		if (!Yii::app()->user->checkAccess('updateFloor', Yii::app()->user->id))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Floor']))
		{
			$model->attributes=$_POST['Floor'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if (!Yii::app()->user->checkAccess('deleteFloor', Yii::app()->user->id))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		Yii::trace('Yii::app()->request->isPostRequest: '.ii::app()->request->isPostRequest,'RoomController::actionDelete');
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
		$dataProvider=new CActiveDataProvider('Floor');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		if (!Yii::app()->user->checkAccess('manageFloor', Yii::app()->user->id))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		$model=new Floor('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Floor']))
			$model->attributes=$_GET['Floor'];

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
		$model=Floor::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='floor-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
