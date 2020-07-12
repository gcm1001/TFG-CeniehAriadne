<?php
/**
 * Base class for AdminMenuDesign tests.
 */
class AdminMenuDesign_Test_AppTestCase extends Omeka_Test_AppTestCase
{
    const PLUGIN_NAME = 'AdminMenuDesign';
    
    const SECTION_1 = 'Data-Manager';
    const SECTION_2 = 'Import-Tools';
    const SECTION_3 = 'Export-Tools';
    const SECTION_4 = 'Edit-Tools';
    const SECTION_5 = 'Others';
    
    public function setUp()
    {
        parent::setUp();

        // Authenticate and set the current user
        $this->user = $this->db->getTable('User')->find(1);
        $this->_authenticateUser($this->user);
        
        $pluginHelper = new Omeka_Test_Helper_Plugin;
        $pluginHelper->setUp(self::PLUGIN_NAME);
        Omeka_Test_Resource_Db::$runInstaller = true;
    }

    public function assertPreConditions()
    {
        $sections = json_decode(get_option('admin_nav_main_sections'), true);
        $this->assertEquals(5, count($sections), 'There should be 5 empty sections.');
        $this->assertEquals(0, count($sections[self::SECTION_1]),'Section'.self::SECTION_1.' should be empty.');
        $this->assertEquals(0, count($sections[self::SECTION_2]),'Section'.self::SECTION_2.' should be empty.');
        $this->assertEquals(0, count($sections[self::SECTION_3]),'Section'.self::SECTION_3.' should be empty.');
        $this->assertEquals(0, count($sections[self::SECTION_4]),'Section'.self::SECTION_4.' should be empty.');
        $this->assertEquals(0, count($sections[self::SECTION_5]),'Section'.self::SECTION_5.' should be empty.');
    }
}
