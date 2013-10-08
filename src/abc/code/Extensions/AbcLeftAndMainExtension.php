<?php

/**
 * @author AzT3K
 */
class AbcLeftAndMainExtension extends LeftAndMainExtension {
	
	public function init() {
		LeftAndMainHelper::process_requirements();
	}	
	
}

