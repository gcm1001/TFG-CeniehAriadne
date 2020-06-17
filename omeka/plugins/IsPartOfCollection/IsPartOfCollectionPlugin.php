<?php
/**
 * PLUGIN IsPartOfCollection
 * > An item 'Is part Of' a Collection
 *
 */
class IsPartOfCollectionPlugin extends Omeka_Plugin_AbstractPlugin
{

    protected $_hooks = array('after_save_item','after_save_collection');

    public function hookAfterSaveItem($args)
    {
        $item = $args['record']; // Ítem
        if(empty(metadata($item, array('Dublin Core', 'Is Part Of')))){
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
     }

     public function hookAfterSaveCollection($args)
     {
       $collection = $args['record'];
       if(empty(metadata($collection, array('Dublin Core', 'Identifier')))){
          $identifierElement = get_db()->getTable('Element')->findByElementSetNameAndElementName('Dublin Core', 'Identifier');
          $collection->addTextForElement($identifierElement, $collection->id);
          $collection->saveElementTexts(); 
       }
     }
}

