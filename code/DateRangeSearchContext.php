<?php

class DateRangeSearchContext extends SearchContext {

	public function getSearchFields() {
		$fields = ($this->fields) ? $this->fields : singleton($this->modelClass)->scaffoldSearchFields();
		if($fields) {
			$dates = array ();
			foreach($fields as $f) {
				$type = singleton($this->modelClass)->obj($f->Name())->class;
				if($type == "Date" || $type == "SS_Datetime") {
					$dates[] = $f;
				}
			}
			foreach($dates as $d) {
				$fields->removeByName($d->Name());
				$fields->push($a = new DateField($d->Name().'_min',$d->Title()." ("._t('DateRange.START','start').")"));
				$fields->push($b = new DateField($d->Name().'_max',$d->Title()." ("._t('DateRange.END','end').")"));
				$a->setConfig('showcalendar',true);
				$b->setConfig('showcalendar',true);
				$a->setConfig('dateformat','yyyy-MM-dd');
				$b->setConfig('dateformat','yyyy-MM-dd');
			}
		}
		return $fields;
	}
	
	public function getQuery($searchParams, $sort = false, $limit = false, $existingQuery = null) {
		$query = parent::getQuery($searchParams, $sort, $limit, $existingQuery);
		if (is_object($searchParams)) {
			$searchParamArray = $searchParams->getVars();
		} 
		else {
			$searchParamArray = $searchParams;
		}		
		
 		foreach($searchParamArray as $key => $value) {
 			$min = (stristr($key,"_min") !== false);
			if($min) {
				$date_field = str_replace('_min','', $key);			
				if($filter = $this->getFilter($date_field)) {
					if(get_class($filter) == "DateRangeFilter") {
						$filter->setModel($this->modelClass);
						$min_val = $searchParamArray[$date_field."_min"];
						$max_val = $searchParamArray[$date_field."_max"];
						if($min_val && $max_val) {
							$filter->setMin($min_val);
							$filter->setMax($max_val);
							$filter->apply($query);
						}
					}
				}
			}
		}
		return $query;
	}
	

}