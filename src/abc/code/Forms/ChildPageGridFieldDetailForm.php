<?php

class ChildPageGridFieldDetailForm extends VersionedGridFieldDetailForm {

    protected $parent;

    public function __construct($name = 'DetailForm', $parent = null) {
        parent::__construct($name);
        $this->parent = $parent;
    }

    public function handleItem($gridField, $request) {
		$controller = $gridField->getForm()->Controller();

		//resetting datalist on gridfield to ensure edited object is in list
		//this was causing errors when the modified object was no longer in the results
		$list = $gridField->getList();
		$list = $list->setDataQuery(new DataQuery($list->dataClass()));

		if(is_numeric($request->param('ID'))) {
			$record = $list->byId($request->param("ID"));
		} else {
			$record = Object::create($gridField->getModelClass());
		}

		$class = $this->getItemRequestClass();

		$handler = Object::create($class, $gridField, $this, $record, $controller, $this->name, $this->parent);
		$handler->setTemplate($this->template);

		// if no validator has been set on the GridField and the record has a
		// CMS validator, use that.
		if(!$this->getValidator() && method_exists($record, 'getCMSValidator')) {
			$this->setValidator($record->getCMSValidator());
		}

		return $handler->handleRequest($request, DataModel::inst());
	}

}

class ChildPageGridFieldDetailForm_ItemRequest extends VersionedGridFieldDetailForm_ItemRequest {

    private static $allowed_actions = array(
        'edit',
        'view',
        'ItemEditForm'
    );

    protected $parent;

    /**
	 *
	 * @param GridFIeld $gridField
	 * @param GridField_URLHandler $component
	 * @param DataObject $record
	 * @param RequestHandler $requestHandler
	 * @param string $popupFormName
	 */
	public function __construct($gridField, $component, $record, $requestHandler, $popupFormName, $parent = null) {
		parent::__construct($gridField, $component, $record, $requestHandler, $popupFormName);
        $this->parent = $parent;
	}

    public function ItemEditForm() {

        $form = parent::ItemEditForm();
        $actions = $this->getCMSActions();

        if(!$this->record->exists() && $this->record->is_a('SiteTree')) {

            if (!$parent_page = $this->parent)
                $parent_page = $this->getController()->currentPage();

            if($parent_page && $parent_page->exists()) {

              $this->record->ParentID = $parent_page->ID;

              // this is kind of a cheeky hack to fix the URLsegment update for new records
              if ($this->isNew()) $this->record->write();

              // update URLSegment @TODO perhaps more efficiently?
              $field = $this->record->getCMSFields()->dataFieldByName('URLSegment');
              $form->Fields()->replaceField('URLSegment',$field);
            }
        }

        $form->setActions($actions);
        return $form;
    }
}
