<?php
class ABCSilverStripeTest extends SapphireTest
{

    public function testSetup() {
        Image::add_extension('AbcImageExtension');
        File::add_extension('AbcFileExtension');
        LeftAndMain::add_extension('AbcLeftAndMainExtension');
        SiteTree::add_extension('AbcSiteTreeExtension');
        Security::add_extension('AbcSecurityExtension');
        Controller::add_extension('AbcControllerExtension');
    }

    /**
     * @depends testSetup
     */
    public function testSMTPMailerSetConf() {

        // phpunit is a bit broken so we manually call the dependent tests;
        $this->testSetup();

    }
}
