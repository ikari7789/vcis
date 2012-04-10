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
}
