<?php
/**
 * Collection Files
 *
 * @license GPLv3
 */

/**
 * "Collection Files" plugin.
 *
 */
class CollectionFilesPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_elementSetName = 'Monitor';

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install',
        'uninstall',
        'uninstall_message',
        'admin_head',
        'admin_collections_form',
        'admin_collections_show_sidebar',
        'before_save_collection',
        'after_save_collection',
        'define_routes',
        'define_acl',
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'admin_collections_form_tabs',
    );
    
    /**
     * HOOK: Defining routes.
     *
     * @param array $args
     */
    public function hookDefineRoutes($args)
    {
        $args['router']->addConfig(new Zend_Config_Ini(dirname(__FILE__) . '/routes.ini', 'routes'));
    }
    
    /**
     * Define the plugin's access control list.
     *
     * @param array $args Parameters supplied by the hook
     * @return void
     */
    public function hookDefineAcl($args)
    {
        $args['acl']->addResource('CollectionFiles');
    }
    
    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        $db = get_db();
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->CollectionFile` (
            `id` int unsigned NOT NULL auto_increment,
            `collection_id` int unsigned NOT NULL,
            `order` int(10) unsigned DEFAULT NULL,
            `size` bigint unsigned NOT NULL,
            `has_derivative_image` tinyint(1) NOT NULL,
            `authentication` char(32) collate utf8_unicode_ci default NULL,
            `mime_type` varchar(255) collate utf8_unicode_ci default NULL,
            `type_os` varchar(255) collate utf8_unicode_ci default NULL,
            `filename` text collate utf8_unicode_ci NOT NULL,
            `original_filename` text collate utf8_unicode_ci NOT NULL,
            `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
            `added` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
            `stored` tinyint(1) NOT NULL default '0',
            `metadata` mediumtext collate utf8_unicode_ci NOT NULL,
            PRIMARY KEY  (`id`),
            KEY `collection_id` (`collection_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
          $db->query($sql);
        
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        // Drop the Location table
        $db = get_db();
        $db->query("DROP TABLE IF EXISTS `$db->CollectionFile`");
    }

    /**
     * Display the uninstall message.
     */
    public function hookUninstallMessage()
    {
        echo __('%sWarning%s: This will remove all the collection files %s', '<p><strong>', '</strong>', '</p>');
    }

    /**
     * Hook for admin head.
     *
     * @return void
     */
    public function hookAdminHead()
    {
        queue_css_file('collectionfiles');
        queue_js_file('collections');
    }

 
    public function filterAdminCollectionsFormTabs($tabs, $args)
    {
        $tab = $tabs[$this->_elementSetName];
        $record = $args['collection'];
        $view = get_view();
        
        // Update the tab.
        $tabs['Files'] = $view->partial('file/file-input-partial-col.php', array(
            'collection' => $record,
            'has_files' => $this->collection_has_files($record),
            'files' => $this->get_collection_files($record),
        ));
        
        return $tabs;
    }
    
    private function collection_has_files($record){
        $db = get_db();
        $sql = "
        SELECT COUNT(f.id)
        FROM $db->CollectionFile f
        WHERE f.collection_id = ?";
        $has_files = (int) $db->fetchOne($sql, array((int) $record->id));
        
        return (bool) $has_files;
    }
    
    private function get_collection_files($record){
        $db = get_db();
        $files = $db->getTable('CollectionFile')->findByCollection($record->id);
        return $files;
    }
    
    public function hookBeforeSaveCollection($args){
        try {
            $collection = $args['record'];

            if (!empty($_FILES['collectionfile'])) {
                $files = $this->insert_files_for_collection($collection, 'Upload', 'collectionfile', array('ignoreNoFile' => true));
                
                foreach ($files as $key => $file) {
                    $file->collection_id = $collection->id;
                    $file->save();
                    // Make sure we can't save it twice by mistake.
                    unset($files[$key]);
                }
            }
        } catch (Omeka_File_Ingest_InvalidException $e) {
            $this->addError('File Upload', $e->getMessage());
        }
    }
    private function insert_files_for_collection($collection, $transferStrategy, $files, $options = array())
    {
        $builder = new Builder_CollectionFiles(get_db());
        $builder->setRecord($collection);
        return $builder->addFiles($transferStrategy, $files, $options);
    }
    
    public function hookAfterSaveCollection($args)
    {
        $collection = $args['record'];
        $db = get_db();
        if ($args['post']) {
            $post = $args['post'];
            
            // Update file order for this item.
            if (isset($post['order'])) {
                foreach ($post['order'] as $fileId => $fileOrder) {
                    // File order must be an integer or NULL.
                    $fileOrder = (int) $fileOrder;
                    if (!$fileOrder) {
                        $fileOrder = null;
                    }
                    
                    $file = $db->getTable('CollectionFile')->find($fileId);
                    if($file){
                        $file->order = $fileOrder;
                        $file->save();
                    }
                }
            }
            
            // Delete files that have been designated by passing an array of IDs
            // through the form.
            if (isset($post['delete_files']) && ($files = $post['delete_files'])) {
                $filesToDelete = $db->getTable('CollectionFile')->findByCollection($collection->id, $files, 'id');
                foreach ($filesToDelete as $fileRecord) {
                    $fileRecord->delete();
                }
            }
        }
    }
    
    public function hookAdminCollectionsForm($args){
        echo '<script type="text/javascript">
                jQuery(document).ready(function () {
                    Omeka.Collections.makeFileWindow();
                    Omeka.Collections.enableSorting();
                    Omeka.Collections.enableAddFiles('.js_escape(__('Add Another File')).');
                });
              </script>';
    }
    
    public function hookAdminCollectionsShowSidebar($args){
        $collection = $args['collection'];
        
        echo '<div class="panel">
        <h4>'.__('Collection Files').'</h4>
        <div id="file-list">';
        
        if (!$this->collection_has_files($collection)){
            echo '<p>'.__('There are no files for this collection yet.').link_to_collection(__('Add a File'), array(), 'edit').'.</p>';
        } else {
            $files = $this->get_collection_files($collection);
            echo '<ul>';
            foreach ($files as $file){
                echo link_to($file,'show', $file->original_filename);
                echo "<br>";
            }
            echo '</ul>';            
        }
        echo '</div> </div>';
    }
    
}
