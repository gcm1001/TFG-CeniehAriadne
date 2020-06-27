<?php
/**
 * Base class for CENIEH Export tests.
 */
class CENIEHExport_Test_AppTestCase extends Omeka_Test_AppTestCase
{
    const PLUGIN_NAME = 'CENIEHExport';

    public function setUp()
    {
        parent::setUp();

        // Authenticate and set the current user
        $this->user = $this->db->getTable('User')->find(1);
        $this->_authenticateUser($this->user);
        
        $pluginHelper = new Omeka_Test_Helper_Plugin;
        $pluginHelper->install('DublinCoreExtended');
        $pluginHelper->setUp(self::PLUGIN_NAME);
        Omeka_Test_Resource_Db::$runInstaller = true;
    }

    public function assertPreConditions()
    {
        $this->assertEquals(1, total_records('Item'), 'There should be one item.');
    }

    protected function _createOne($index = null)
    {
        // Omeka adds one item by default.
        $this->assertEquals(1, total_records('Item'));

        $metadata = array();
        $elementTexts = array();
        $elementTexts['Dublin Core']['Title'][] = array('text' => 'Test item', 'html' => false);
        return insert_item($metadata, $elementTexts);
    }
    
    protected function _createOneCollection($index = null)
    {
        $this->assertEquals(0, total_records('Collection'), 'There should be no collections.');

        $metadata = array();
        $elementTexts = array();
        $elementTexts['Dublin Core']['Title'][] = array('text' => 'Test collection', 'html' => false);
        return insert_collection($metadata, $elementTexts);
    }
    
    protected function files_are_equal($xmlA, $xmlB){
       if(filesize($xmlA) !== filesize($xmlB))
            return false;

        // Check if content is different
        $xmlAh = fopen($xmlA, 'rb');
        $xmlBh = fopen($xmlB, 'rb');

        $result = true;
        while(!feof($xmlAh))
        {
          if(fread($xmlAh, 8192) != fread($xmlBh, 8192))
          {
            $result = false;
            break;
          }
        }

        fclose($xmlAh);
        fclose($xmlBh);

        return $result;
    }
}
