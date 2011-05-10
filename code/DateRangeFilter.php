<?php

class DateRangeFilter extends SearchFilter {

	protected $min;
	protected $max;
	
	function setMin($min) {
		$this->min = $min;
	}
	
	function setMax($max) {
		$this->max = date('Y-m-d',strtotime("+1 day",strtotime($max)));
	}
	

	function apply(SQLQuery $query) {
		$query->where(sprintf(
			"%s >= '%s' AND %s < '%s'",
			$this->getDbName(),
			Convert::raw2sql($this->min),
			$this->getDbName(),
			Convert::raw2sql($this->max)
		));
	}

}