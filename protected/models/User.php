<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $last_login_time
 * @property string $create_time
 * @property string $update_time
 * @property integer $create_user_id
 * @property integer $update_user_id
 *
 * The followings are the available model relations:
 * @property Building[] $buildings
 * @property Building[] $buildings1
 * @property Category[] $categories
 * @property Category[] $categories1
 * @property Feature[] $features
 * @property Feature[] $features1
 * @property Floor[] $floors
 * @property Floor[] $floors1
 * @property Room[] $rooms
 * @property Room[] $rooms1
 * @property User $createUser
 * @property User[] $users
 * @property User $updateUser
 * @property User[] $users1
 */
class User extends ActiveRecordBase
{
	
	private $_oldValues = array();
	public $password_repeat;
	public $currentPassword;
	public $role;
	
	const ROLE_ADMINISTRATOR = 'administrator';
	const ROLE_EMPLOYEE = 'employee';
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Building the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, username, password', 'required'),
			array('email, username', 'unique'),
			array('email', 'email'),
			array('password', 'compare'),
			array('password', 'length', 'min'=>5),
			array('email, username, password', 'length', 'max'=>256),
			array('password_repeat, role', 'safe'),
			array('currentPassword', 'safe', 'on'=>'create'),
			array('currentPassword', 'required', 'on'=>'update'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, email, username, password, last_login_time, create_time, update_time, create_user_id, update_user_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'buildings' => array(self::HAS_MANY, 'Building', 'create_user_id'),
			'buildings1' => array(self::HAS_MANY, 'Building', 'update_user_id'),
			'categories' => array(self::HAS_MANY, 'Category', 'create_user_id'),
			'categories1' => array(self::HAS_MANY, 'Category', 'update_user_id'),
			'features' => array(self::HAS_MANY, 'Feature', 'create_user_id'),
			'features1' => array(self::HAS_MANY, 'Feature', 'update_user_id'),
			'floors' => array(self::HAS_MANY, 'Floor', 'create_user_id'),
			'floors1' => array(self::HAS_MANY, 'Floor', 'update_user_id'),
			'rooms' => array(self::HAS_MANY, 'Room', 'create_user_id'),
			'rooms1' => array(self::HAS_MANY, 'Room', 'update_user_id'),
			'createUser' => array(self::BELONGS_TO, 'User', 'create_update_user'),
			'users' => array(self::HAS_MANY, 'User', 'create_user_id'),
			'updateUser' => array(self::BELONGS_TO, 'User', 'update_user_id'),
			'users1' => array(self::HAS_MANY, 'User', 'update_user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'email' => 'Email',
			'username' => 'Username',
			'password' => 'Password',
			'last_login_time' => 'Last Login Time',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
			'create_user_id' => 'Create User',
			'update_user_id' => 'Update User',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		//$criteria->compare('id',$this->id);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('last_login_time',$this->last_login_time,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('create_user_id',$this->create_user_id);
		$criteria->compare('update_user_id',$this->update_user_id);
		
		$criteria->order='username ASC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Save current record to temp variable to be able to rollback any changes.
	 */
	public function afterFind()
	{
		$this->_oldValues = $this->attributes;
		Yii::trace('Model backup created.','Building::afterFind');
		return parent::afterFind();
	}
	
	/**
	 * perform one-way encryption on the password before we store it in the database.
	 */
	protected function afterValidate()
	{
		parent::afterValidate();
		$this->password = $this->encrypt($this->password);
	}
	
	protected function afterDelete()
	{
		$this->removeAuth();
	}
	
	public function removeAuth()
	{
		$auth = Yii::app()->authManager;
		$authItems = $auth->getAuthItems(NULL,$this->id);
		foreach ($authItems as $authItem)
			$authItem->revoke($this->_oldValues['id']);
	}
	
	public function encrypt($value)
	{
		return md5($value);
	}
	
	public function getAuthRole()
	{
		$auth = Yii::app()->authManager;
		$authItems = $auth->getAuthItems(2,$this->id);
		$roles = array();
		
		foreach ($authItems as $authItems)
			$roles[] = $authItems->getName();
		
		return $roles[0];
		
	}
	
	public function getUserRoles()
	{
		return array(
			self::ROLE_EMPLOYEE=>'Employee',
			self::ROLE_ADMINISTRATOR=>'Administrator',
		);
	}
}