<?php
/**
 * Collection Files Plugin
 *
 * @copyright Copyright 2020 , Gonzalo Cuesta Marín.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
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
        $database = get_db();
        $sql = "
        CREATE TABLE IF NOT EXISTS `$database->CollectionFile` (
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
          $database->query($sql);
        
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        // Drop the Location table
        $database = get_db();
        $database->query("DROP TABLE IF EXISTS `$database->CollectionFile`");
    }

    /**
     * Display the uninstall message.
     */
    public function hookUninstallMessage()
    { ?>
        <?= __('%sWarning%s: This will remove all the collection files %s', '<p><strong>', '</strong>', '</p>'); ?> <?php
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

    /**
     * Hook for Admin Collections Form Tabs.
     *
     * @return void
     */
    public function filterAdminCollectionsFormTabs($tabs, $args)
    {
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
    
    /**
     * Check if collection has files.
     * 
     * @param type $record Record
     * @return type bool
     */
    private function collection_has_files($record){
        $database = get_db();
        $sql = "
        SELECT COUNT(f.id)
        FROM $database->CollectionFile f
        WHERE f.collection_id = ?";
        $has_files = (int) $database->fetchOne($sql, array((int) $record->id));
        
        return (bool) $has_files;
    }
    
    /**
     * Get collection files.
     * 
     * @param type $record Record
     * @return type Files
     */
    private function get_collection_files($record){
        $database = get_db();
        $files = $database->getTable('CollectionFile')->findByCollection($record->id);
        return $files;
    }
    
    /**
     * Hook Before Save Collection.
     * 
     * @param type $args 
     */
    public function hookBeforeSaveCollection($args){
        $collection = $args['record'];
        try {
            if ($this->isset_file('collectionfile')){
                $this->insert_files_for_collection($collection, 'Upload', 'collectionfile', array('ignoreNoFile' => true));
            }
        } catch (Omeka_File_Ingest_InvalidException $e) {
            $collection->addError('File Upload', $e->getMessage());
        }
    }
    
    /**
     * Check if file is set.
     * 
     * @param type $name Name of file
     * @return type bool
     */
    protected function isset_file($name){
        return empty($name) ? false : isset($_FILES[$name]);
    }
    
    /**
     * Insert files for collection.
     * 
     * @param type $collection Collection
     * @param type $transferStrategy Transfer Strategy
     * @param type $files Files
     * @param type $options Options
     */
    private function insert_files_for_collection($collection, $transferStrategy, $files, $options = array())
    {
        $builder = new Builder_CollectionFiles(get_db());
        $builder->setRecord($collection);
        $files = $builder->addFiles($transferStrategy, $files, $options);
        foreach ($files as $key => $file) {
            $file->collection_id = $collection->id;
            $file->save();
            // Make sure we can't save it twice by mistake.
            unset($files[$key]);
        }
    }
    
    /**
     * Hook After Save Collection.
     * 
     * @param type $args 
     */
    public function hookAfterSaveCollection($args)
    {
        $database = get_db();
        if ($args['post']) {
            $post = $args['post'];
            $collection = $args['record'];
           
            // Update file order for this collection.
            if (isset($post['order'])) {
                foreach ($post['order'] as $fileId => $fileOrder) {
                    // File order must be an integer or NULL.
                    $fileOrder = (int) $fileOrder;
                    if (!$fileOrder) {
                        $fileOrder = null;
                    }
                    
                    $file = $database->getTable('CollectionFile')->find($fileId);
                    if($file){
                        $file->order = $fileOrder;
                        $file->save();
                    }
                }
            }
            
            // Delete files that have been designated by passing an array of IDs
            // through the form.
            if (isset($post['delete_files']) && ($files = $post['delete_files'])) {
                $filesToDelete = $database->getTable('CollectionFile')->findByCollection($collection->id, $files, 'id');
                foreach ($filesToDelete as $fileRecord) {
                    $fileRecord->delete();
                }
            }
        }
    }
    
    /**
     * Hook Admin Collections Form.
     * 
     * @param type $args
     */
    public function hookAdminCollectionsForm($args){ ?>
         <?= '<script type="text/javascript">
                jQuery(document).ready(function () {
                    Omeka.Collections.makeFileWindow();
                    Omeka.Collections.enableSorting();
                    Omeka.Collections.enableAddFiles('.js_escape(__('Add Another File')).');
                });
              </script>' ?>
        <?php
    }
    
    /**
     * Hook Admin Collections Show Sidebar.
     * 
     * @param type $args 
     */
    public function hookAdminCollectionsShowSidebar($args){
        $collection = $args['collection']; 
        
        $this->_p_html('<div class="panel">
        <h4>'.__('Collection Files').'</h4>
        <div id="file-list">');
        
        if (!$this->collection_has_files($collection)){ 
            $this->_p_html('<p>'.__('There are no files for this collection yet.').link_to_collection(__('Add a File'), array(), 'edit').'.</p>'); 
        } else {
            $files = $this->get_collection_files($collection); 
            $this->_p_html('<ul>'); 
            foreach ($files as $file){ 
                 $this->_p_html(link_to($file,'show', $file->original_filename)); 
                 $this->_p_html("<br>"); 
            } 
            $this->_p_html('</ul>');     
        } 
        $this->_p_html('</div> </div>'); 
    }
    
    /**
     * Prints HTML code.
     * 
     * @param type $html HTML code
     */
    private function _p_html($html){ ?>
      <?= $html ?> <?php
    }
}
