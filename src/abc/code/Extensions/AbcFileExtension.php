<?php

class AbcFileExtension extends DataExtension {

	// this doesn't even work
	private static $allowed_extensions = array(
		'','ace','arc','arj','asf','au','avi','bmp','bz2','cab','cda','css','csv','dmg','doc','docx',
		'flv','f4v','gif','gpx','gz','hqx','htm','html','ico','jar','jpeg','jpg','js','json','kml', 'm4a','m4v',
		'mid','midi','mkv','mov','mp3','mp4','mpa','mpeg','mpg','ogg','ogv','pages','pcx','pdf','pkg',
		'png','pps','ppt','pptx','ra','ram','rm','rtf','sit','sitx','swf','tar','tgz','tif','tiff',
		'ttf','otf','txt','wav','webm','webmv','wma','wmv','xhtml','xls','xlsx','xml','zip','zipx',
	);

	public static function get_allowed_extensions() {
		return self::$allowed_extensions;
	}

	public function getMimeType(){
		$return = explode('-', $this->owner->getFileType());
		return $return[0];
	}

	public function getFileSize(){
		return $this->owner->getSize();
	}

}
