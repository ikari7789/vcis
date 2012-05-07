<?php

Yii::import('application.vendors.*');
require_once('Zend/Search/Lucene.php');

class SearchController extends Controller
{
	private $_indexFiles = '/runtime/search';
	
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionCreate()
	{
		$rootPath = pathinfo(Yii::app()->request->scriptFile);
		$index = Zend_Search_Lucene::create($rootPath['dirname'].$this->_indexFiles);
		
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(
			new Search_Analyzer()
		);
		
		// Add documents to the database.
		$rooms = Room::model()->findAll();
		$indexedRooms = '';
		foreach ($rooms as $room)
		{
			$doc = new Zend_Search_Lucene_Document();
			$url = CController::createAbsoluteUrl('room/view', array('id'=>$room->id));
			$doc->addField(Zend_Search_Lucene_Field::unIndexed('link', $url));
			$doc->addField(Zend_Search_Lucene_Field::unStored('id', $room->id));
			$doc->addField(Zend_Search_Lucene_Field::text('room_number', $room->number));
			$doc->addField(Zend_Search_Lucene_Field::text('room_status', ($room->status == 0) ? 'in operation':'out of operation'));
			$doc->addField(Zend_Search_Lucene_Field::text('room_description', $room->description));
			$doc->addField(Zend_Search_Lucene_Field::keyword('floor_level', 'floor '.$room->floor->level));
			$doc->addField(Zend_Search_Lucene_Field::text('building_name', $room->building->name));
			
			$featureNames = '';
			$featureDetails = '';
			foreach ($room->room_features as $roomFeature)
			{
				$doc->addField(Zend_Search_Lucene_Field::text(
					str_replace(' ', '_', strtolower($roomFeature->feature->name)),
					$roomFeature->feature->name.' '.$roomFeature->details)
				);
			}
			/*$doc->addField(Zend_Search_Lucene_Field::text('feature_names', $featureNames));
			$doc->addField(Zend_Search_Lucene_Field::text('feature_details', $featureDetails));*/
		
			$index->addDocument($doc);
			$indexedRooms .= '<li>'.$room->building->name.' - Floor '.$room->floor->level.' - '.$room->number.'</li>';
		}

		$index->optimize();
		$index->commit();
		$this->render('create', array('indexedRooms'=>$indexedRooms));
	}

	public function actionSearch()
	{
		if (isset($_GET['terms']))
		{
			$rootPath = pathinfo(Yii::app()->request->scriptFile);
			$index = new Zend_Search_Lucene($rootPath['dirname'].$this->_indexFiles);
			$results = $index->find($_GET['terms']);
			$this->render('search', array('results' => $results));
		}
		else
		{
			$this->render('index');
		}
	}
	
	public function actionUpdate()
	{
		$rootPath = pathinfo(Yii::app()->request->scriptFile);
		$removePath = $rootPath['dirname'].$this->_indexFiles;
		$this->actionCreate();
	}
	
	/**
     * Delete a file or recursively delete a directory
     *
     * @param string $str Path to file or directory
     */
    private function recursiveDelete($str) 
    {
        if(is_file($str)){
            return @unlink($str);
        }
        elseif(is_dir($str)){
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path){
                $this->recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }
}
