<?php

class AbcImageExtension extends DataExtension {

	public static $fallback_image = null;

	public static $db = array(
		'CapturedBy'	=> 'Varchar(255)',
		'Location'		=> 'Varchar(255)',		
		'DateCaptured'	=> 'Date'
	);
	
	public static $summary_fields = array(
		'Title'			=> 'Title',
		'Filename'		=> 'Filename',
		'CMSThumbnail'	=> 'Preview'
	);

    public function getCMSFields() {  	
	
        $fields = parent::getCMSFields();
        
        // Set some fields
        $fields->addFieldToTab( 'Root.Main', new TextField( 'Location' ) );
		$fields->addFieldToTab( 'Root.Main', new TextField( 'CapturedBy' ) ); 

		// Configure the date field
		$df = new DateField( 'DateCaptured', 'Date Captured (dd/mm/yyyy)' );
		$df->setLocale('en_NZ');
		$df->setConfig('dateformat', 'dd/MM/YYYY');
		$df->setConfig('showcalendar','true'); 
		$fields->addFieldToTab( 'Root.Main', $df );	

        return $fields;
    }

	public function getCMSFields_forPopup() {
		$fields = $this->getCMSFields();
		$fields->removeByName('current-image');
		$fields->push( new LiteralField( 'Padding' , '<br /><br />') );
		return $fields;
	}	

	public function isValid(){
		return !$this->owner->Filename || !is_file($_SERVER['DOCUMENT_ROOT'].'/'.$this->owner->Filename) ? false : true ;
	}

	protected function failSafe(){
		if (!$this->isValid()){
			if ( !$image = DataObject::get_one('Image',"Filename='".self::$fallback_image."'") ){
				$this->owner->Filename = self::$fallback_image;
				$this->owner->write();
			}else{
				$this->owner->ID = $image->ID;
				$this->owner->Filename = self::$fallback_image;
			}
		}
	}	

	public function CroppedImageAbsoluteURL($w, $h){
		$this->failSafe();
		return !$this->isValid() ? false : Director::absoluteBaseURL().str_replace('%2F','/',rawurlencode($this->owner->CroppedImage($w, $h)->getFilename()));
	}

	public function SetWidthAbsoluteURL($w){
		$this->failSafe();
		return !$this->isValid() ? false : Director::absoluteBaseURL().str_replace('%2F','/',rawurlencode($this->owner->setWidth($w)->getFilename()));
	}
	
	public function SetSizeAbsoluteURL($w, $h) {
		$this->failSafe();
		return !$this->isValid() ? false : Director::absoluteBaseURL().str_replace('%2F','/',rawurlencode($this->owner->SetSize($w,$h)->getFilename()));
	}

}