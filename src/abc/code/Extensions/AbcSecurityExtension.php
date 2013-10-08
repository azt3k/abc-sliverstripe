<?php

/**
 * @author AzT3K
 */
class AbcSecurityExtension extends Extension {
	
	public function onAfterInit() {
		
		$controller		= $this->owner;
		$params			= (object) $controller->getURLParams();
		
		if ($params->Action == 'ping') LeftAndMainHelper::process_requirements();
		
	}
	
}

