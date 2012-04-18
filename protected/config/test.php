<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
			// uncomment the following to provide test database connection
			'db'=>array(
				'connectionString'=>'mysql:host=mysql.standingmist.com;dbname=standingmist_vcis_test',
				'emulatePrepare'=>true,
				'username'=>'vcis',
				'password'=>'mcssad2',
				'charset'=>'utf8',
			),
		),
	)
);
