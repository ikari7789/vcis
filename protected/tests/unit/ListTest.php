<?php
Yii::import('application.controllers.ListController');
class ListTest extends CTestCase
{
	public function testActionAdd()
	{
		$list = new ListController('listTest');
	}
	
	public function testActionIndex()
	{
		$list = new ListController('listTest');
	}
	
	public function testActionRemove()
	{
		$list = new ListController('listTest');
	}
	
	public function testActionUpdate()
	{
		$list = new ListController('listTest');
	}
	
	public function testActionClear()
	{
		$list = new ListController('listTest');
	}
}
?>