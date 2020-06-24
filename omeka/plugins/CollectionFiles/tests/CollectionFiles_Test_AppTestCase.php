<?php
/**
 * Base class for Collection Files tests.
 */
class CollectionFiles_Test_AppTestCase extends Omeka_Test_AppTestCase
{
    const PLUGIN_NAME = 'CollectionFiles';
    
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
        $this->assertEquals(0, total_records('CollectionFile'), 'There should be no collection files.');
    }
    
    protected function _createOneCollection($index = null)
    {
        $this->assertEquals(0, total_records('Collection'), 'There should be no collections.');

        $metadata = array();
        $elementTexts = array();
        $elementTexts['Dublin Core']['Title'][] = array('text' => 'test collection', 'html' => false);
        return insert_collection($metadata, $elementTexts);
    }
    
    protected function insert_files_for_collection($collection, $transferStrategy, $files, $options = array())
    {
        $builder = new Builder_CollectionFiles(get_db());
        $builder->setRecord($collection);
        $files = $builder->addFiles($transferStrategy, $files, $options);
        $workfiles = $files;
        foreach ($workfiles as $key => $file) {
            $file->collection_id = $collection->id;
            $file->save();
            // Make sure we can't save it twice by mistake.
            unset($workfiles[$key]);
        }
        return $files;
    }
}
