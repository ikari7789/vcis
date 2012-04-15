<?php
abstract class ActiveRecordBase extends CActiveRecord
{	
	/**
	 * @return array behavior rules for model attributes.
	 */
	public function behaviors() {
		return array(
			'CTimestampBehavior'=>array(
				'class'=>'zii.behaviors.CTimestampBehavior',
			)

		);
	}
	
	/**
	 * Prepares create_time, create_user_id, update_time,
	 * and update_user_id attributes before performing validation.
	 */
	protected function beforeValidate()
	{
		if ($this->isNewRecord)
		{
			// set the create date, last updated date
			// and the user doing the creating
			$this->create_user_id=$this->update_user_id=Yii::app()->user_id;
		}
		else
		{
			// not a new record, so just set the last updated time
			// and last updated user id
			$this->update_user_id=Yii::app()->user->id;
		}
		
		return parent::beforeValidate();
	}

}