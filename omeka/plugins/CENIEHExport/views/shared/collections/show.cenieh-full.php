<?php
  /**
   * Script que genera la vista de una colección en formato XML.
   *
   */
    $collection = get_current_record('collection'); // objeto 'collection'
    $collectionID = $collection->id; // identificador de la colección

    header('Content-Disposition: attachment; filename="Collection_'.$collection->id.'.xml"'); // formato del nombre del fichero [Collection_ID.cir]

    if(!isset($collectionID)) return 'ERROR: collection ID not set'; // se comprueba que existe la colección

      include_once(dirname(dirname(dirname(dirname(__FILE__)))).'/views/helpers/CENIEHExporter.php');
      $exporter = new CENIEHExporter();
      try{?><?= $exporter->exportCollectionFull($collectionID);?>
<?php } catch(Exception $e) {
          return $e->getMessage();
      } 
?>
