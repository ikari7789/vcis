<?php

class ListController extends Controller
{
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','add','remove','clear','update'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function actionAdd()
	{
		if (Yii::app()->request->isPostRequest)
		{
			Yii::trace('Find room by ID: '.$_POST['room_id'],'ListController::actionAdd');
			$room = Room::model()->findbyPk($_POST['room_id']);
			if ($room)
			{
				$session = Yii::app()->session;
				Yii::trace('Checking to see if list initialized.','ListController::actionAdd');
				if (!isset($session['list']))
				{
					$session['list']=new RoomList;
					Yii::trace('Current session doesn\'t have a room list initialized. Created','ListController::actionAdd');
				}
				$result = $session['list']->addRoom($room->id);
				if (!$result)
					echo 'Room already in list';
				else
					echo 'Room added to list';
				Yii::trace('Room added to list.','ListController::actionAdd');
			}
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionIndex()
	{
		$session = Yii::app()->session;
		if (!isset($session['list']))
			$session['list'] = new RoomList;
		$list = $session['list'];
		$rooms = $session['list']->getRooms();
		
		$rootPath = pathinfo(Yii::app()->request->scriptFile);
		foreach($rooms as $room) {
			$imageLocation = $rootPath['dirname'].'/images/rooms/'.$room->front_image;
			if (is_dir($imageLocation) || !file_exists($imageLocation))
				$room->front_image = 'front-default.jpg';
		}
		
		$this->render('index',array('list'=>$list,'rooms'=>$rooms));
	}

	public function actionRemove()
	{
		if (Yii::app()->request->isPostRequest)
		{
			Yii::trace('Attempting to remove room '.$_POST['room_id'].' from list.','ListController::actionRemove');
			$session = Yii::app()->session;
			Yii::trace('Checking to see if list initialized.','ListController::actionAdd');
			if (!isset($session['list']))
			{
				$session['list']=new RoomList;
				Yii::trace('Current session doesn\'t have a room list initialized. Created','ListController::actionAdd');
			}
			$session['list']->removeRoom($_POST['room_id']);
			echo 'Room removed from list';
			Yii::trace('Room removed','ListContoller::actionRemove');
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	public function actionUpdate()
	{
		if (Yii::app()->request->isPostRequest)
		{
			if (isset($_POST['room_id']))
			{
				$session = Yii::app()->session;
				unset($session['list']);
				$session['list']=new RoomList;
				foreach($_POST['room_id'] as $pos => $id)
				{
					$session['list']->addRoom($id);
				}
			}
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	public function actionClear()
	{
		if (Yii::app()->request->isPostRequest)
		{
			unset(Yii::app()->session['list']);
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
}