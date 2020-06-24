<?php
/**
 * Base class for IsPartOfCollection tests.
 */
class IsPartOfCollection_Test_AppTestCase extends Omeka_Test_AppTestCase
{
    const PLUGIN_NAME = 'IsPartOfCollection';

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
        $this->assertEquals(0, total_records('Collection'), 'There should be no collections.');
    }

    protected function _createOneItem($index = null)
    {
        // Omeka adds one item by default.
        $this->assertEquals(1, total_records('Item'));

        $metadata = array();
        $elementTexts = array();
        $elementTexts['Dublin Core']['Title'][] = array('text' => 'test item', 'html' => false);
        return insert_item($metadata, $elementTexts);
    }
    
    protected function _createOneCollection($index = null)
    {
        $this->assertEquals(0, total_records('Collection'), 'There should be no collections.');

        $metadata = array();
        $elementTexts = array();
        $elementTexts['Dublin Core']['Title'][] = array('text' => 'test collection', 'html' => false);
        return insert_collection($metadata, $elementTexts);
    }
}
