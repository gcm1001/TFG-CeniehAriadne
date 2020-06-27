<?php
    /**
     * Script que genera la vista de un ítem en formato XML.
     */

    include_once(dirname(dirname(dirname(dirname(__FILE__)))).'/helpers/CENIEHExporter.php'); //se importa la clase CENIEHExporter

    $item = get_current_record('item'); // objeto 'item'
    $itemID = $item->id; // identificador del ítem

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="Item_'.$itemID.'.xml"'); // formato del nombre del fichero [Item_ID.cir]

    $CENIEHExporter = new CENIEHExporter(); // inicializamos un objeto de tipo CENIEHExporter

    if(!isset($itemID)) return 'ERROR: item ID not set'; // se comprueba que existe el ítem

      try{ ?>
<?= $CENIEHExporter->exportItem($itemID); ?>
<?php } catch (Exception $e) {
        $this->flashMessenger->addMessage($e->getMessage(),'error');
      }
?>