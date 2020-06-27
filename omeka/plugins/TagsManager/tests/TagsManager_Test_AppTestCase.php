<?php
/**
 * Base class for TagsManager tests.
 */
class TagsManager_Test_AppTestCase extends Omeka_Test_AppTestCase
{
    const PLUGIN_NAME = 'TagsManager';

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
        $this->assertEquals(0, total_records('RecordsTag'), 'There should be no relation tags.');
        $this->assertEquals(0, total_records('Tag'), 'There should be no tags.');
    }

    protected function _createOne($index = null)
    {
        // Omeka adds one item by default.
        $this->assertEquals(1, total_records('Item'));

        $metadata = array();
        $elementTexts = array();
        $elementTexts['Dublin Core']['Title'][] = array('text' => 'title 1', 'html' => false);
        $elementTexts['Dublin Core']['Creator'][] = array('text' => 'creator #1', 'html' => false);
        $elementTexts['Dublin Core']['Date'][] = array('text' => 2001, 'html' => false);
        $elementTexts['Dublin Core']['Subject'][] = array('text' => 'tag #1', 'html' => false);
        return insert_item($metadata, $elementTexts);
    }
}
