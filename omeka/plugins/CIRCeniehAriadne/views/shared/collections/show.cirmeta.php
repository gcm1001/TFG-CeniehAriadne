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

    if(!isset($collectionID)) return 'ERROR: collection ID not set'; // se comprueba que existe la colección

      try{ ?>
      <?= $cirExporter->exportCollectionMeta($collectionID); ?>
<?php } catch(Exception $e) {
          return $e->getMessage();
      } 
?>
