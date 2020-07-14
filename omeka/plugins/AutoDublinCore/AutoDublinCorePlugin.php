<?php
/**
 * PLUGIN AutoDublinCore
 *
 */
class AutoDublinCorePlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install',
        'uninstall',
        'config_form',
        'config',
        'after_save_item',
        'after_save_collection'
        );

    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        'auto_ispartof' => true,
        'auto_source' => true,
        'auto_source_link' => 'https://cir.cenieh.es/handle/',
    );
    
   /**
    * Install the plugin.
    */   
    public function hookInstall() {
        $this->_installOptions();
    }
    
    /**
     * Uninstall the plugin.
     */
    public function hookUninstall() {
        $this->_uninstallOptions();
    }
    
    /**
     * Shows plugin configuration page.
     *
     * @return void
     */
    public function hookConfigForm($args) {
        include 'config_form.php';
    }
    
    /**
     * Processes the configuration form.
     *
     * @return void
     */
    public function hookConfig($args) {
        $post = $args['post'];
        set_option('auto_ispartof', html_escape($post['auto_ispartof']));
        set_option('auto_source', html_escape($post['auto_source']));
        set_option('auto_source_link', html_escape($post['auto_source_link']));
    }
    
    /**
     * Hook used after save item.
     *
     * @param array $args
     */
    public function hookAfterSaveItem($args) {
      
        $item = $args['record']; // Ítem
        if((boolean) get_option('auto_ispartof') && empty(metadata($item, array('Dublin Core', 'Is Part Of')))){
            $collection = get_collection_for_item($item);
            if ($collection) { 
                $collectionId = $collection->id; 
                if (!empty($collectionId)){
                  $ispartofElement = get_db()->getTable('Element')->findByElementSetNameAndElementName('Dublin Core', 'Is Part Of'); 
                  $item->addTextForElement($ispartofElement, $collectionId); 
                  $item->saveElementTexts(); 
                }
            }
        }
        
        if((boolean) get_option('auto_source') && empty(metadata($item, array('Dublin Core', 'Source')))){
            $identifiers = $item->getElementTexts('Dublin Core', 'Identifier');
            if(!empty($identifiers)){
                $sourceElement = get_db()->getTable('Element')->findByElementSetNameAndElementName('Dublin Core', 'Source'); 
                foreach($identifiers as $id){
                    if(preg_match("/http:\/\/hdl\.handle\.net\/.*/i", $id->text)){
                        $sourcelink = get_option('auto_source_link');
                        $sourceContent = preg_replace("/http:\/\/hdl\.handle\.net\//i", $sourcelink, $id->text);
                        $item->addTextForElement($sourceElement, $sourceContent); 
                        $item->saveElementTexts(); 
                        break;
                    }
                }
            }
        }
     }
     
    /**
     * Hook used after save collection.
     *
     * @param array $args
     */
     public function hookAfterSaveCollection($args) {
          $collection = $args['record'];
          if(empty(metadata($collection, array('Dublin Core', 'Identifier')))){
             $identifierElement = get_db()->getTable('Element')->findByElementSetNameAndElementName('Dublin Core', 'Identifier');
             $collection->addTextForElement($identifierElement, $collection->id);
             $collection->saveElementTexts(); 
          }
     }
}

