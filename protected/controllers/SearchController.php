<?php

Yii::import('application.vendors.*');
require_once('Zend/Search/Lucene.php');

class SearchController extends Controller
{
	private $_indexFiles = '/runtime/search';
	const CHECKBOX_SEARCH = 0;
	const NUMERIC_SEARCH = 1;
	
	
	public function actionIndex()
	{		
		$this->render('index',array(
			'advancedSearchOptions'=>$this->getAdvancedSearchOptions(),
		));
	}

	private function getAdvancedSearchOptions() {
		$categoryModels = Category::model()->findAll();

		$advancedSearchOptions = array();
		foreach ($categoryModels as $categoryModel) {
			foreach ($categoryModel->features as $featureModel) {
				$advancedSearchOptions[$categoryModel->name][$featureModel->name]['searchType'] = self::NUMERIC_SEARCH;
				$advancedSearchOptions[$categoryModel->name][$featureModel->name]['feature_id'] = $featureModel->id;
				foreach ($featureModel->featureDetails as $featureDetailsModel) {
					$advancedSearchOptions[$categoryModel->name][$featureModel->name]['details'][$featureDetailsModel->details] = $featureDetailsModel->details;
					
					// Check if search type is non-numeric
					if (!is_numeric($featureDetailsModel->details))
						$advancedSearchOptions[$categoryModel->name][$featureModel->name]['searchType'] = self::CHECKBOX_SEARCH;
				}
				
				// Remove any empty details
				if (empty($advancedSearchOptions[$categoryModel->name][$featureModel->name]['details']))
					unset($advancedSearchOptions[$categoryModel->name][$featureModel->name]);
			}
			
			// Remove any empty features
			if (empty($advancedSearchOptions[$categoryModel->name][$featureModel->name]))
				unset($advancedSearchOptions[$categoryModel->name]);
		}

		// Remove search options if nothing to search on
		if (!empty($advancedSearchOptions))
			natcasesort($advancedSearchOptions);
		
		return $advancedSearchOptions;
	}

	public function actionSearch()
	{
		if (isset($_GET['type']) && $_GET['type'] == 'advanced')
		{
			$this->render('search', array(
				'advancedSearchOptions'=>$this->getAdvancedSearchOptions(),
				'results' => $this->searchByUser($_GET)
			));
		}
		else
			$this->run('index');
	}
	
	private function searchByUser($parameters) {
		$criteria = new CDbCriteria;

		// select
		$criteria->select = array('*', );
		
		// with
		$criteria->with = array('room');

		$tmpLow = Array();
		$tmpHigh = Array();

		if (isset($parameters['type'])) {
			unset($parameters['type']);
		}

		foreach ($parameters as $key => $value) {
			// look for low value in range
			if (preg_match('/._low/', $key)) {
				$tmpLow[substr($key, 0, strpos($key, '_'))] = $value;
			// look for high value in range
			} else if (preg_match('/._high/', $key)) {
				$tmpHigh[substr($key, 0, strpos($key, '_'))] = $value;
			// all other values
			} else {
				$feature = Feature::model()->findByPk($key);
				if ($feature) {
					$search .= ' AND (t.feature_id = '.$feature->id.' AND (';
					//$criteria->addSearchCondition('t.feature_id', $feature->id);
					foreach ($value as $searchTerm) {
						$subSearch .= ' OR t.details LIKE "'.$searchTerm.'"';
						//$criteria->addSearchCondition('t.details', $searchTerm);
					}
					$subSearch = substr($subSearch, 4, strlen($subSearch));
					$search .= $subSearch.'))';
				}
			}
		}

		// Go through each of the low values
		foreach ($tmpLow as $key => $value) {
			$search .= ' AND (t.feature_id = '.$key.' AND (t.details > '.$tmpLow[$key];
			// if upper range found, limit search between the lower and upper
			if ($tmpHigh[$key]) {
				$search .= ' AND t.details < '.$tmpHigh[$key].'))';
				//$criteria->addBetweenCondition('t.details', $tmpLow[$key], $tmpHigh[$key]);
				// delete upper range value from tempHigh
				unset($tmpHigh[$key]);
			} else {
				// if no upper range, find all values higher than lower cutoff
				$search .= ' AND t.details < '.PHP_INT_MAX.'))';
				//$criteria->addBetweenCondition('t.details', $tmpLow[$key], PHP_INT_MAX);
			}
			unset($tmpLow[$key]);
		}

		// Get under for any leftover upper ranges
		foreach ($tmpHigh as $key => $value) {
			$search .= ' AND (t.feature_id = '.$key.' AND (t.details > 0 AND t.details < '.$tmpHigh[$key].'))';
			//$criteria->addBetweenCondition('t.details', 0, $tmpHigh[$key]);
		}

		$search = substr($search, 5, strlen($search));
		$criteria->condition = $search;

		return new CActiveDataProvider('RoomFeature',
			array(
				'criteria'=>$criteria,
				'pagination'=>array(
					'pageSize'=>10,
				),
				'sort'=>array(
					'defaultOrder'=>'room.number',
					'attributes'=>array(
						'*',
					),
				),
			)
		);
	}
}
