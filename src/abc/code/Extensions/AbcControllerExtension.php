<?php

/**
 * @author AzT3K
 */
class AbcControllerExtension extends Extension {
	
	public function init() {
		
		$requirements = RequirementsHelper::get_requirements();
		
		foreach ($requirements['block'] as $file) {
			Requirements::block($file[0]);
		}
		
		foreach ($requirements['unblock'] as $file) {
			Requirements::unblock($file[0]);
		}
		
	}	
	
}

