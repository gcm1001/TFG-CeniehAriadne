<?php
  /**
   * Script que genera la vista de una colección en formato XML.
   *
   */

    include_once(dirname(dirname(dirname(dirname(__FILE__)))).'/helpers/CirExporter.php'); // se importa la clase CirEXporte

    $collection = get_current_record('collection'); // objeto 'collection'
    $collectionID = $collection->id; // identificador de la colección

    header('Content-Disposition: attachment; filename="Collection_'.$collection->id.'.cir.xml"'); // formato del nombre del fichero [Collection_ID.cir]

    $cirExporter = new CirExporter(); // inicializamos un objeto de tipo CirExporter

    if(!isset($collectionID)) die('ERROR: collection ID not set'); // se comprueba que existe la colección

    try{
        echo $cirExporter->exportCollectionMeta($collectionID);  // imprimimos todo el contenido generado sobre el fichero xml
    } catch (Exception $e) {
        die($e->getMessage());
    }
?>
