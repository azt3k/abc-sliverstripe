<?php

/**
 * @author AzT3K
 */
class AbcControllerExtension extends Extension {
	
	public function onAfterInit() {
		RequirementsHelper::process_requirements();
	}
	
}

