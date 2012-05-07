<?php

class HideChildrenDecorator extends DataObjectDecorator {

	public function augmentStageChildren(DataObjectSet $staged, $showAll = false) {

		// check if in CMS or delivering web page
		if (is_subclass_of(Controller::curr(), "LeftAndMain")) {
			if ($staged->exists()) {
				foreach ($staged as $obj) {
					$staged->remove($obj);
				}
			}
		}
		return $staged; 
	}

}

