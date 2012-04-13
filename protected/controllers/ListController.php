<?php

class ListController extends Controller
{
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

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}