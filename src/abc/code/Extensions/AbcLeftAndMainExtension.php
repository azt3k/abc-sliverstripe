<?php

/**
 * @author AzT3K
 */
class AbcLeftAndMainExtension extends LeftAndMainExtension {
	
	public function onAfterInit() {
		LeftAndMainHelper::process_requirements();
	}	
	
}

