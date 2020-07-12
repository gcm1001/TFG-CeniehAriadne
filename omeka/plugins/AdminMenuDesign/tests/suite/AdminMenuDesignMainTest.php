<?php
/**
 * Admin Menu Design plugin.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 */
class AdminMenuDesign_AdminMenuDesignMainTest extends AdminMenuDesign_Test_AppTestCase
{
    protected $_isAdminTest = true;

    public function testConfig()
    {
        $this->getRequest()->setQuery(array('name' => self::PLUGIN_NAME));
        $this->dispatch('/plugins/config');
        $this->assertEquals(200,$this->getResponse()->getHttpResponseCode());
    }
    
    public function testConfigPost()
    {
        $this->getRequest()->setQuery(array('name' => self::PLUGIN_NAME));
        $this->getRequest()->setMethod('POST')->setPost(array());
        $this->dispatch('/plugins/config');
        $this->assertRedirectTo('/plugins');
    }
}
