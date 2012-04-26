<?php

/**
 * RoomList class.
 * RoomList is the data structure for keeping
 * room list data. It is used by the 'ListController'.
 */
class RoomList extends CModel
{
	private $_rooms = array();
	
	public function getRooms()
	{
		$rooms = array();
		foreach ($this->_rooms as $roomId)
			$rooms[] = Room::model()->findByPk($roomId);
		
		return $rooms;
	}
	
	public function addRoom($roomId)
	{
		foreach ($this->_rooms as $listPosition => $id)
			if ($id == $roomId)
				return false;
		$this->_rooms[] = $roomId;
		return true;
	}
	
	public function removeRoom($roomId)
	{
		foreach ($this->_rooms as $listPosition => $id)
			if ($id == $roomId)
				unset($this->_rooms[$listPosition]);
	}
	
	public function printRooms()
	{
		foreach ($this->_rooms as $room)
		{
			print_r($room->getAttributes());
		}
	}
	
	public function attributeNames()
	{
		return array();
	}
	
		
	/**
	 * Check if the room list has a specific room.
	 * @param int $id room id to look for in list
	 */
	public function hasRoom($id)
	{
		$session = Yii::app()->session;
		if (!isset($session['list']))
			$session['list'] = new RoomList;
		if (in_array($id, $session['list']->_rooms))
			return true;
		else
			return false;
	}	
}
