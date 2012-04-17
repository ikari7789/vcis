<?php

class RbacCommand extends CConsoleCommand
{
	private $_authManager;
	
	public function getHelp()
	{
		return <<<EOD
USAGE
	rbac
	
DESCRIPTION
	This command generates an initial RBAC authorization hierarchy.
	
EOD;
	}
	
	/**
	 * Execute the action.
	 * @param array command line parameters specific for this command
	 */
	public function run($args)
	{
		// ensure that an authManager is defined as this is
		// mandatory for creating an auth heirarchy
		if (($this->_authManager=Yii::app()->authManager)===NULL)
		{
			echo "Error: an authorization manager, named 'authManager' must be configured to use this command.\n";
			echo "If you already added 'authManager' component in application configuration,\n";
			echo "please quit and re-enter the yiic shell.\n";
			return;
		}
		
		// provide the opportunity for the user to abort the request
		echo "This command will create two roles: Administrator, Employee and the following permissions:\n";
		echo "create, read, update, and delete user\n";
		echo "create, read, update, and delete building\n";
		echo "create, read, update, and delete floor\n";
		echo "create, read, update, and delete room\n";
		echo "create, read, update, and delete room feature\n";
		echo "create, read, update, and delete feature\n";
		echo "create, read, update, and delete feature category\n";
		echo "Would you like to continue? [Yes|No] ";
		
		// check the input from the user and continue if they indicated yes to the above question
		if (!strncasecmp(trim(fgets(STDIN)),'y',1))
		{
			// first we need to remove all operations, roles, child relationships and assignments
			$this->_authManager->clearAll();
			
			// create the lowest level operations for users
			$this->_authManager->createOperation("createUser","create a new user");
			$this->_authManager->createOperation("readUser","read user profile information");
			$this->_authManager->createOperation("updateUser","update a user's information");
			$this->_authManager->createOperation("deleteUser","remove a user");
			
			// create the lowest level operations for buildings
			$this->_authManager->createOperation("createBuilding","create a new building");
			$this->_authManager->createOperation("readBuilding","read building information");
			$this->_authManager->createOperation("updateBuilding","update a building's information");
			$this->_authManager->createOperation("deleteBuilding","remove a building");
			
			// create the lowest level operations for floors
			$this->_authManager->createOperation("createFloor","create a new floor");
			$this->_authManager->createOperation("readFloor","read floor information");
			$this->_authManager->createOperation("updateFloor","update a floor's information");
			$this->_authManager->createOperation("deleteFloor","remove a floor");
			
			// create the lowest level operations for rooms
			$this->_authManager->createOperation("createRoom","create a new room");
			$this->_authManager->createOperation("readRoom","read room information");
			$this->_authManager->createOperation("updateRoom","update a room's information");
			$this->_authManager->createOperation("deleteRoom","remove a room");
			
			// create the lowest level operations for room features
			$this->_authManager->createOperation("createRoomFeature","create a new room feature");
			$this->_authManager->createOperation("readRoomFeature","read room feature information");
			$this->_authManager->createOperation("updateRoomFeature","update a room feature's information");
			$this->_authManager->createOperation("deleteRoomFeature","remove a room feature");
			
			// create the lowest level operations for features
			$this->_authManager->createOperation("createFeature","create a new feature");
			$this->_authManager->createOperation("readFeature","read feature information");
			$this->_authManager->createOperation("updateFeature","update a feature's information");
			$this->_authManager->createOperation("deleteFeature","remove a feature");
			
			// create the lowest level operations for categories
			$this->_authManager->createOperation("createCategory","create a new category");
			$this->_authManager->createOperation("readCategory","read category information");
			$this->_authManager->createOperation("updateCategory","update a category's information");
			$this->_authManager->createOperation("deleteCategory","remove a category");
			
			// create the employee role and add the appropriate permissions as children to this role
			$role=$this->_authManager->createRole("employee","User able to create and update inventory");
			
			$role->addChild("readBuilding");
			$role->addChild("readFloor");
			$role->addChild("readRoom");
			$role->addChild("readRoomFeature");
			$role->addChild("readFeature");
			$role->addChild("readCategory");
			
			$role->addChild("createBuilding");
			$role->addChild("createFloor");
			$role->addChild("createRoom");
			$role->addChild("createRoomFeature");
			$role->addChild("createFeature");
			$role->addChild("createCategory");
			
			$role->addChild("updateBuilding");
			$role->addChild("updateFloor");
			$role->addChild("updateRoom");
			$role->addChild("updateRoomFeature");
			$role->addChild("updateFeature");
			$role->addChild("updateCategory");
			
			// create the administrator role and add the appropriate permissions as children to this role
			$role=$this->_authManager->createRole("administrator","Super user capable of all CRUD abilities");
			$role->addChild("employee");
			
			$role->addChild("readUser");			
			
			$role->addChild("createUser");
			
			$role->addChild("updateUser");
			
			$role->addChild("deleteUser");
			$role->addChild("deleteBuilding");
			$role->addChild("deleteFloor");
			$role->addChild("deleteRoom");
			$role->addChild("deleteRoomFeature");
			$role->addChild("deleteFeature");
			$role->addChild("deleteCategory");
			
			// provide a message indicating success
			echo "Authorization hierarchy successfully generated.";
		}
	}
}

?>