<?php
/**
 * 'Helper Class' que realiza el proceso de exportación tanto de las
 * colecciones como de los ítems.
 *
 */
 Class CirExporter {

    /**
     * Devuelve el texto con formato xml asociado a un ítem.
     *
     *@param int $itemID EL identificador del ítem a exportar
     *@return string $xml El contenido del fichero xml
     */
    public function exportItem($itemID) {
        ob_start();
        $this->_generateCIR($itemID,false);
        return ob_get_clean();
    }

    /**
     * Genera el contenido del fichero xml asociado a una colección.
     *
     *@param int $collectionID Identificador de la colección a exportar
     *@return void
     */
    public function exportCollection($collectionID) {
        $collection = get_record_by_id("collection",$collectionID);
        $items = get_records('Item',array('collection'=>$collectionID),999);

        $this->_generateCirHeader($collectionID,"Collection");
        $this->_generateCirBody($collectionID,"Collection");

        foreach($items as $item) {
            $this->_generateCirBody($item->id,"Item");
        }

        $this->_generateCirFooter($collectionID);
    }

    /**
     * Exporta la colección al completo en un único fichero comprimido (.zip)
     * donde se almacenan todos los ítems que pertenecen a esa colección.
     *
     *@param int $collectionID Identificador de la colección a exportar
     *@return void
     */
    public function exportCollectionZip($collectionID) {
        /* Librería que nos permite generar un fichero comprimido (zip) */
        include_once(dirname(dirname(__FILE__)).'/libraries/zipstream-php-0.2.2/zipstream.php');

        $collection = get_record_by_id("collection",$collectionID);
        $items = get_records('Item',array('collection'=>$collectionID),999); // ítems de la colección

        error_reporting(0); // se desactiva toda notificación de error

        $zip = new ZipStream('Collection_'.$collection->id.'.zip');

        foreach($items as $item) { // añadimos el xml generado para cada item al zip
            ob_start();
            $this->_generateCIR($item->id,false);
            $zip->add_file("Item_".$item->id.".cir.xml", ob_get_clean() );
        }

        $zip->finish();
    }


    private function _generateCirHeader($itemID,$recordType="Item") {
        echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        //--------------------
        // HEADER
        //--------------------
        echo '<Collection ';
        echo 'xmlns:dc="http://purl.org/dc/elements/1.1/" ';
        echo 'xmlns:dcterms="http://purl.org/dc/terms/" ';
        echo 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        echo 'xmlns:cir="http://www.cir.cenieh.es" ';
        echo ">\n";

    }

    private function _generateCirBody($itemID,$recordType) {

        $item = get_record_by_id($recordType,$itemID);
        $files = ($recordType=="Item") ? $item->getFiles() : array();

        //--------------------
        //METADATA
        //--------------------
        echo ($recordType == "Collection") ? "\n\t<collection>\n" : "\t\t\t<record>\n";

        $elementArray = $item->getAllElements();

        foreach($elementArray as $elementSetName => $elements) {
            ob_start();
            $flag = false;

            $eSSlug=$this->_getElementSetSlug($elementSetName);

            echo ($recordType == "Collection") ? "\t\t<metadata>\n" : "\t\t\t\t<metadata>\n";

            if($eSSlug!=="") $eSSlug .= ":";

            $unqualified = array(
                'title', 'creator', 'subject', 'description', 'publisher',
                'contributor', 'date', 'type', 'format', 'identifier',
                'source', 'language', 'relation', 'coverage', 'rights'
            );

            foreach($elements as $element) {
                $eSlug = $this->_getElementSlug($element->name,$elementSetName);
                $elementTexts =  $item->getElementTexts($elementSetName,$element->name);

        	      if(empty($elementTexts)) continue;

                $flag = true;

        	      foreach($elementTexts as $elementText) {
                    $preElement = ($recordType == "Collection") ? "\t\t\t<" : "\t\t\t\t\t<";
                    echo (in_array($eSlug, $unqualified)) ? ($preElement.$eSSlug.$eSlug.">") : ($preElement."dcterms:".$eSlug.">");
          		      echo htmlspecialchars($elementText->text);
                    echo (in_array($eSlug, $unqualified)) ? "</".$eSSlug.$eSlug.">\n" : "</"."dcterms:".$eSlug.">\n";
        	      }
      	    }

      	    echo ($recordType == "Collection") ? "\t\t</metadata>\n" : "\t\t\t\t</metadata>\n";

            if($flag) {
      	       ob_end_flush();
      	    } else {
      	       ob_end_clean();
            }
        }

        echo ($recordType == "Collection") ? "\t\t<records>\n" : "\t\t\t</record>\n";
    }


    private function _generateCirFooter($itemID) {
        echo "\t\t</records>";
        echo "\n\t</collection>\n";
        echo "</Collection>\n";
    }

    /**
     * Genera e imprime el contenido del fichero xml asociado a un ítem.
     *
     *@param int $itemID Identificador del ítem
     */
    private function _generateCir($itemID) {
        if(!is_numeric($itemID)) {
            echo "ERROR: Invalid item ID";
            die();
        }

        $item = get_record_by_id("item",$itemID);

        if(is_null($item)||empty($item)) {
            echo "ERROR: Invalid item ID";
            die();
        }

        $titles = $item->getElementTexts('Dublin Core','Title');
        $title = $titles[0];
        $title = htmlspecialchars($title);
        $type = $item->getItemType();

        $unqualified = array(
            'title', 'creator', 'subject', 'description', 'publisher',
            'contributor', 'date', 'type', 'format', 'identifier',
            'source', 'language', 'relation', 'coverage', 'rights' );

        echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        //--------------------
        // HEADER
        //--------------------
        echo '<Record ';
        echo 'xmlns:dc="http://purl.org/dc/elements/1.1/" ';
        echo 'xmlns:dcterms="http://purl.org/dc/terms/" ';
        echo 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
        echo 'xmlns:cir="http://www.cir.cenieh.es" ';
        echo ">\n";
        //--------------------
        //METADATA
        //--------------------
        $elementArray = $item->getAllElements();

        foreach($elementArray as $elementSetName => $elements) {
            ob_start();
            $flag = false;

            $eSSlug=$this->_getElementSetSlug($elementSetName);

            echo "\t<metadata>\n";

            if($eSSlug!=="") $eSSlug .= ":";

            foreach($elements as $element) {
                $eSlug = $this->_getElementSlug($element->name,$elementSetName);

                $elementTexts =  $item->getElementTexts($elementSetName,$element->name);

                if(empty($elementTexts)) continue;
                $flag = true;

                foreach($elementTexts as $elementText) {
                    echo (in_array($eSlug, $unqualified)) ? ("\t\t\t<".$eSSlug.$eSlug.">") : ("\t\t\t<"."dcterms:".$eSlug.">");
                    echo htmlspecialchars($elementText->text);
                    echo (in_array($eSlug, $unqualified)) ? "</".$eSSlug.$eSlug.">\n" : "</"."dcterms:".$eSlug.">\n";
        	      }
            }

            if($flag){
                ob_end_flush();
            } else {
                ob_end_clean();
            }
        }

        echo "\t</metadata>\n";
        echo "</Record>\n";
    }

    /**
     *Devuelve el slug para un conjunto de datos determinado
     *
     *@param string $elementSetName Nombre del conjunto de datos
     *@return string El identificador del conjunto de datos o "unknow" si
     * es desconocido (no está instalado en Omeka)
     */
    private function _getElementSetSlug($elementSetName) {
        switch($elementSetName) {
            case 'Dublin Core':
                return 'dc';
            default:
                $elementSetName = str_replace(' ', '', $elementSetName);
                return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $elementSetName));
        }
    }


    /**
     * Devuelve el 'slug' asociado a un elemento del conjunto de metadatos
     * en función del nombre.
     *
     *@param string $elementName Nombre del elemento
     *@return string Nombre del conjunto al que pertenece
     */
    private function _getElementSlug($elementName,$elementSetName='') {
        $dces = new DublinCoreExtendedPlugin;
        foreach ($dces->getElements() as $elementDces) { 
            if ($elementName == $elementDces['label']) return $elementDces['name'];
        }
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $elementName));
    }



    /**
     *Determine whether a given metadata element set is
     *recognized or not based on its slug
     *
     *@param string $eSSlug Identificador del conjunto de datos
     *@return bool True si el conjunto de elementos es deconocido, False si no
     */
    private function _is_type_other($eSSlug) {
        if($eSSlug==="unknown"){
            return true;
        } else {
            return false;
        }
    }


 }

?>
