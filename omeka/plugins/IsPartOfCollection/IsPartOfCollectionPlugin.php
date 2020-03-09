<?php
/**
 * PLUGIN IsPartOfCollection
 * > An item 'Is part Of' a Collection
 *
 */
class IsPartOfCollectionPlugin extends Omeka_Plugin_AbstractPlugin
{

    protected $_hooks = 'after_save_item';

    public function hookAfterSaveItem($args)
    {
        $db = get_db(); //Base de datos
        $elementTable = $db->getTable('Element'); // Tabla 'Element'
        $ispartofElement = $elementTable->findByElementSetNameAndElementName('Dublin Core', 'Is Part Of'); // Elemento 'Is Part Of'
        $item = $args['record']; // Ítem
        $collection = get_collection_for_item($item); // Colección asociada al ítem
        // Eliminamos los campos anteriores del elemento 'Is Part Of'
        $item->deleteElementTextsByElementId(array($ispartofElement->id));

        if ($collection) { // Si tiene una colección asociada
          $collectionId = metadata($collection, array('Dublin Core', 'Identifier')); // y si esta además tiene un identificador
            if (!empty($collectionId)){
              $item->addTextForElement($ispartofElement, $collectionId); // actualizamos el elemento 'Is part Of' del ítem con el identificador de su colección
            }
          }
        $item->saveElementTexts(); // guardamos los cambios efectuados
     }

}
?>
