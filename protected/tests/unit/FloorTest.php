<?php
class FloorTest extends CDbTestCase
{	
	public $fixtures = array(
		'buildings'=>'Building',
		'users'=>'User',
	);
	
	public function testCreate()
	{
		// CREATE a new Building
		$newBuilding = new Building;
		$newBuildingName = 'Test Building Creation';
		$newBuilding->setAttributes(
			array(
				'name' => $newBuildingName,
				'map_image' => '1_map.jpg',
				'street_image' => '1_street.jpg',
			)
		);
		
		// set the application user id to the first user in our users fixture data
		Yii::app()->user->setId($this->users('user1')->id);
		;
		// save the new building, triggering attribute validation
		$this->assertTrue($newBuilding->save());
		
		// READ back the newly created Project to ensure the creation worked
		$retrievedBuilding = Building::model()->findByPk($newBuilding->id);
		$this->assertTrue($retrievedBuilding instanceof Building);
		$this->assertEquals($newBuildingName,$retrievedBuilding->name);
		
		// ensure the user associated with creating the new project is the 
		// same as the application user we set when saving the project
		$this->assertEquals(Yii::app()->user->id, $retrievedBuilding->create_user_id);
	}
	
	public function testUpdate()
	{
		$building = $this->buildings('building2');
		$updatedBuildingName = 'Updated Test Building 2';
		$building->name = $updatedBuildingName;
		$this->assertTrue($building->save(false));
		// read back the record again to ensure the update worked
		$updatedBuilding=Building::model()->findByPk($building->id);
		$this->assertTrue($updatedBuilding instanceof Building);
		$this->assertEquals($updatedBuildingName,$updatedBuilding->name);
	}
	
	public function testRead()
	{
		$retrievedBuilding = $this->buildings('building1');
		$this->assertTrue($retrievedBuilding instanceof Building);
		$this->assertEquals('Test Building 1',$retrievedBuilding->name);
	}
	
	public function testDelete()
	{
		$building = $this->buildings('building2');
		$savedBuildingId = $building->id;
		$this->assertTrue($building->delete());
		$deletedBuilding = Building::model()->findByPk($savedBuildingId);
		$this->assertEquals(NULL,$deletedBuilding);
	}
}
?>