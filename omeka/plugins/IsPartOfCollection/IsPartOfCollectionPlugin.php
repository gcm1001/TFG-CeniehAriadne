<?php
/**
 * PLUGIN IsPartOfCollection
 * > An item 'Is part Of' a Collection
 *
 */
class IsPartOfCollectionPlugin extends Omeka_Plugin_AbstractPlugin
{

    protected $_hooks = array('after_save_item');

    public function hookAfterSaveItem($args)
    {
        $database = get_db(); 
        $elementTable = $database->getTable('Element'); 
        $ispartofElement = $elementTable->findByElementSetNameAndElementName('Dublin Core', 'Is Part Of'); 
        $item = $args['record']; // Ítem
        $collection = get_collection_for_item($item); 
        
        $item->deleteElementTextsByElementId(array($ispartofElement->id));
        if ($collection) { 
            $collectionLink = WEB_ROOT.'/collections/show/'.$collection->id; 
            if (!empty($collectionLink)){
              $item->addTextForElement($ispartofElement, $collectionLink); 
            }
          }
        $item->saveElementTexts(); 
     }

}

