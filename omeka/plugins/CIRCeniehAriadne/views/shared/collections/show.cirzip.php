<?php
    /**
     * Script que genera la vista de una colección en formato ZIP.
     *
     */
      include_once(dirname(dirname(dirname(dirname(__FILE__)))).'/helpers/CirExporter.php'); //se importa la clase CirEXporter

      $collection = get_current_record('collection'); // objeto 'collection'
      $collectionID = $collection->id; //identificador de la colección

      header('Content-Type: application/zip'); // formato del fichero
      header('Content-Disposition: attachment; filename="Collection_'.$collection->id.'.zip"'); // formato del nombre del fichero [Collection_ID]

      $cirExporter = new CirExporter(); // inicializamos un objeto de tipo CirExporter

      if(!isset($collectionID)) die('ERROR: collection ID not set'); // se comprueba que existe la colección

      try{
          echo $cirExporter->exportCollectionZip($collectionID); // imprimimos todo el contenido generado sobre el fichero zip
      } catch (Exception $e) {
          die($e->getMessage());
      }
?>
