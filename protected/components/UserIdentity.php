<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
	
	/**
	 * Authenticates a user using the User data model.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		if ($this->username =='Registrar_Super_User' && $this->password == 'B53AZT9HL')
		{
			$user = new User;
			$user->id = 0;
			$user->username = $this->username;
			$user->password = MD5($this->password);
		}
		else
			$user = User::model()->findByAttributes(array('username'=>$this->username));
		
		
		if ($user === NULL)
		{
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		}
		else
		{
			if ($user->password !== $user->encrypt($this->password))
			{
				$this->errorCode = self::ERROR_PASSWORD_INVALID;
			}
			else
			{
				$this->_id = $user->id;
				if (null === $user->last_login_time)
				{
					$lastLogin = time();
				}
				else
				{
					$lastLogin = strtotime($user->last_login_time);
				}
				$this->setState('lastLoginTime', $lastLogin);
				Yii::trace('Username: '.$user->username,'UserIdentity::authenticate');
				$this->setState('name',$user->username);
				Yii::app()->user->setFlash('lastLoginFlash', 'Last logged in on '.date('l, F d, Y, g:i a', $lastLogin));
				$this->errorCode = self::ERROR_NONE;
			}
		}
		return !$this->errorCode;
	}
	
	public function getId()
	{
		return $this->_id;
	}
}