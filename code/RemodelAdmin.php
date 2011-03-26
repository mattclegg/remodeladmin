<?php
class RemodelAdmin extends ModelAdmin {

	public static $collection_controller_class = "RemodelAdmin_CollectionController";

	public static $record_controller_class = "RemodelAdmin_RecordController";
	
	public static $parent_page_type = "SiteTree";
	
	public function init() {
	    parent::init();
	    // Remove all the junk that will break ModelAdmin
	    $config = HtmlEditorConfig::get_active();
	    $buttons = array('undo','redo','separator','cut','copy','paste','pastetext','pasteword','spellchecker','separator','sslink','unlink','anchor','separator','advcode','search','replace','selectall','visualaid','separator');
	    $config->setButtonsForLine(2,$buttons);
	    Requirements::javascript('remodeladmin/javascript/remodeladmin.js');
	    Requirements::css('remodeladmin/css/remodeladmin.css');

	}



	public function SearchClassSelector() {
		return "dropdown";
	}
	
	public function ListView() {
		return $this->search(array(), $this->SearchForm());
	}
	
	public function getParentPage() {
		if($parent = $this->stat('parent')) {
			if(is_numeric($parent)) {
				return DataObject::get_by_id($this->modelClass(), $parent);
			}
			elseif(is_string($parent)) {
				return SiteTree::get_by_link($parent);
			}			
		}
		return false;	
	}

}

class RemodelAdmin_CollectionController extends ModelAdmin_CollectionController {

	function add($request) {
		$class = $this->modelClass;
		$record = new $class();
		$record->write();
		$class = $this->parentController->getRecordControllerClass($this->getModelClass());
		$response = new $class($this, $request, $record->ID);
		return $response->edit($request);
	}
	
	function getSearchQuery($searchCriteria) {
		$query = parent::getSearchQuery($searchCriteria);
		if(!is_subclass_of($this->getModelClass(),"SiteTree")) {
			return $query;
		}
		$query->orderby("`SiteTree`.LastEdited DESC");
		if($page = $this->parentController->getParentPage()) {
			$query->where[] = "ParentID = $page->ID";					
		}
		return $query;
	}
	


}

class RemodelAdmin_RecordController extends ModelAdmin_RecordController {

	public function EditForm() {
		$form = parent::EditForm();
		if(is_subclass_of($this->currentRecord->class,"SiteTree")) {

			$live_link = Controller::join_links($this->currentRecord->Link(),'?stage=Live');
			$stage_link = Controller::join_links($this->currentRecord->Link(),'?stage=Stage');
	
			
			$form->setActions($this->currentRecord->getCMSActions());
			$form->Fields()->insertFirst(new LiteralField('view','<div class="publishpreviews clr">'._t('RemodelAdmin.VIEW','View Page').': <a target="_blank" href="'.$live_link.'">'._t('RemodelAdmin.VIEWLIVE','Live Site').'</a> <a target="_blank" href="'.$stage_link.'">'._t('RemodelAdmin.VIEWDRAFT','Draft Site').'</a></div></div>'));
	
			if($parent = $this->parentController->parentController->getParentPage()) {
				$form->Fields()->push(new HiddenField('ParentID','', $parent->ID));
			}
			elseif($parent_class = $this->parentController->stat('parent_page_type')) {
				$form->Fields()->push(new SimpleTreeDropdownField('ParentID', _t('RemodelAdmin.PARENTPAGE','Parent page'), $parent_class));
			}
		
		}		
    	$form->Fields()->insertFirst(new LiteralField('back','<div class="modelpagenav clr"><button id="list_view">&laquo; '._t('RemodelAdmin.BACKTOLIST','Back to list view').'</button></div>'));		

		return $form;	
	}
		
	public function publish($data, $form, $request) {
		if($this->currentRecord && !$this->currentRecord->canPublish()) 
			return Security::permissionFailure($this);

		$form->saveInto($this->currentRecord);		
		$this->currentRecord->doPublish();

		if(Director::is_ajax()) {
			return new SS_HTTPResponse(
				Convert::array2json(array(
					'html' => $this->EditForm()->forAjaxTemplate(),
					'message' => _t('ModelAdmin.PUBLISHED','Published')
				)),				
				200
			);
		} else {
			Director::redirectBack();
		}
	}
	
	public function unpublish($data, $form, $request) {
		if($this->currentRecord && !$this->currentRecord->canDeleteFromLive()) 
			return Security::permissionFailure($this);
		
		$this->currentRecord->doUnpublish();
		
		if(Director::is_ajax()) {
			return new SS_HTTPResponse(
				Convert::array2json(array(
					'html' => $this->EditForm()->forAjaxTemplate(),
					'message' => _t('ModelAdmin.UNPUBLISHED','Unpublished')
				)),				
				200
			);
		} else {
			Director::redirectBack();
		}

	}
	
	protected function performRollback($id, $version) {
		$record = DataObject::get_by_id($this->currentRecord->class, $id);
		if($record && !$record->canEdit()) 
			return Security::permissionFailure($this);
		
		$record->doRollbackTo($version);
		return $record;
	}
	
	public function rollback($data, $form, $request) {
		$record = $this->performRollback($this->currentRecord->ID, "Live");
		if(Director::is_ajax()) {
			return new SS_HTTPResponse(
				Convert::array2json(array(
					'html' => $this->EditForm()->forAjaxTemplate(),
					'message' => _t('ModelAdmin.ROLLEDBACK','Rolled back version')
				)),				
				200
			);
		} else {
			Director::redirectBack();
		}
	}
	
		
	public function delete($data, $form, $request) {
		$record = $this->currentRecord;
		if($record && !$record->canDelete())
			return Security::permissionFailure();
		
		// save ID and delete record
		$recordID = $record->ID;
		$record->delete();
		
		if(Director::is_ajax()) {
			$body = "";
			return new SS_HTTPResponse(
				Convert::array2json(array(
					'html' => $this->EditForm()->forAjaxTemplate(),
					'message' => _t('ModelAdmin.DELETED','Deleted')
				)),				
				200
			);
		} else {
			Director::redirectBack();
		}
	}
	
	public function save($data, $form, $request) {
		if($this->currentRecord && !$this->currentRecord->canEdit()) 
			return Security::permissionFailure($this);

		$form->saveInto($this->currentRecord);		
		$this->currentRecord->write();

		if(Director::is_ajax()) {
			return new SS_HTTPResponse(
				Convert::array2json(array(
					'html' => $this->EditForm()->forAjaxTemplate(),
					'message' => _t('ModelAdmin.SAVED','Saved')
				)),				
				200
			);
		} else {
			Director::redirectBack();
		}
	}
	
	public function deletefromlive($data, $form, $request) {
		Versioned::reading_stage('Live');
		$record = $this->currentRecord;
		if($record && !($record->canDelete() && $record->canDeleteFromLive())) 
			return Security::permissionFailure($this);

		
		$descRemoved = '';
		$descendantsRemoved = 0;
		
		// before deleting the records, get the descendants of this tree
		if($record) {
			$descendantIDs = $record->getDescendantIDList();

			// then delete them from the live site too
			$descendantsRemoved = 0;
			foreach( $descendantIDs as $descID )
				if( $descendant = DataObject::get_by_id('SiteTree', $descID) ) {
					$descendant->doDeleteFromLive();
					$descendantsRemoved++;
				}

			// delete the record
			$record->doDeleteFromLive();
		}

		Versioned::reading_stage('Stage');

		if(Director::is_ajax()) {
			$body = $this->parentController->ListView()->getBody();
			return new SS_HTTPResponse(
				Convert::array2json(array(
					'html' => $body,
					'message' => _t('ModelAdmin.DELETEDFROMLIVE','Deleted')
				)),				
				200
			);
		} else {
			Director::redirectBack();
		}
	}
}