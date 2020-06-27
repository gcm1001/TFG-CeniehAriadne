<?php
  /**
   * Script que genera la vista de una colección en formato XML.
   *
   */

    include_once(dirname(dirname(dirname(dirname(__FILE__)))).'/helpers/CENIEHExporter.php'); // se importa la clase CENIEHExporter

    $collection = get_current_record('collection'); // objeto 'collection'
    $collectionID = $collection->id; // identificador de la colección

    header('Content-Disposition: attachment; filename="Collection_'.$collection->id.'.xml"'); // formato del nombre del fichero [Collection_ID.cir]

    $CENIEHExporter = new CENIEHExporter(); // inicializamos un objeto de tipo CENIEHExporter

    if(!isset($collectionID)) return 'ERROR: collection ID not set'; // se comprueba que existe la colección

      try{?><?=$CENIEHExporter->exportCollectionFull($collectionID);?>
<?php } catch(Exception $e) {
          return $e->getMessage();
      } 
?>
