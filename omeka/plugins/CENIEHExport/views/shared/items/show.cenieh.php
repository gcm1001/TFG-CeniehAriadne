<?php
    /**
     * Script que genera la vista de un ítem en formato XML.
     */

     
    $item = get_current_record('item'); // objeto 'item'
    $itemID = $item->id; // identificador del ítem

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="Item_'.$itemID.'.xml"'); // formato del nombre del fichero [Item_ID.cir]

    if(!isset($itemID)) return 'ERROR: item ID not set'; // se comprueba que existe el ítem

      include_once(dirname(dirname(dirname(dirname(__FILE__)))).'/views/helpers/CENIEHExporter.php');
      $exporter = new CENIEHExporter();
      try{?><?= $exporter->exportItem($itemID);?>
<?php } catch(Exception $e) {
          return $e->getMessage();
      }
?>