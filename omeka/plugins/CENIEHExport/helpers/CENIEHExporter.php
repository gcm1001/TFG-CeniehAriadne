<?php
/**
 * CENIEH Export Plugin helper class that generates the content of the xml document..
 */
 Class CENIEHExporter {

    /**
     * Returns CENIEH xml for a given single Omeka item
     *
     *@param int $itemID ID of the Omeka item
     *@return string $xml Contents of the CENIEH file
     */
    public function exportItem($itemID) {
        ob_start();
        $this->_generateCIR($itemID,false);
        return ob_get_clean();
    }

    /**
     * Export a single collection with CENIEH xml.
     *
     *@param int $collectionID Identificador de la colección a exportar
     *@return void
     */
    public function exportCollectionMeta($collectionID) {        
        $this->_generateCeniehHeader();
        $this->_generateCeniehBody($collectionID,"Collection",false);
        $this->_generateCeniehFooter(false);
    }
    
    /**
     * Export an entire collection as a zip file filled with CENIEH xml 
     *
     *@param int $collectionID ID of the omeka collection to export
     *@return void
     */
    public function exportCollectionFull($collectionID) {
        $items = get_records('Item',array('collection'=>$collectionID),999);

        $this->_generateCeniehHeader();
        $this->_generateCeniehBody($collectionID,"Collection");

        foreach($items as $item) {
            $this->_generateCeniehBody($item->id,"Item");
        }

        $this->_generateCeniehFooter();
    }

    /**
     * Export an entire collection as a zip file filled with CENIEH xml 
     * files for each item.
     * 
     *@param int $collectionID ID of the omeka collection to export
     *@return void
     */
    public function exportCollectionZip($collectionID) {
        include_once(dirname(dirname(__FILE__)).'/libraries/zipstream-php-0.2.2/zipstream.php');

        $collection = get_record_by_id("collection",$collectionID);
        $items = get_records('Item',array('collection'=>$collectionID),999); // ítems de la colección

        error_reporting(0); 

        $zip = new ZipStream('Collection_'.$collection->id.'.zip');

        foreach($items as $item) { 
            ob_start();
            $this->_generateCIR($item->id,false);
            $zip->add_file("Item_".$item->id.".cir.xml", ob_get_clean() );
        }

        $zip->finish();
    }


    private function _generateCeniehHeader() {
        $this->_p_html('<?xml version="1.0" encoding="UTF-8"?>'."\n");
        //--------------------
        // HEADER
        //--------------------
        $this->_p_html('<Collections ');
        $this->_p_html('xmlns:dc="http://purl.org/dc/elements/1.1/" ');
        $this->_p_html('xmlns:dcterms="http://purl.org/dc/terms/" ');
        $this->_p_html('xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ');
        $this->_p_html('xmlns:cenieh="http://www.cenieh.es" ');
        $this->_p_html( ">\n");

    }
  
    private function _generateCeniehBody($recordID,$recordType, $full = True) {
        $record = get_record_by_id($recordType,$recordID);
        //--------------------
        //METADATA
        //--------------------
        $this->_p_html( ($recordType == "Collection") ? "\n\t<collection>\n" : "\t\t\t<record>\n");

        $elementArray = $record->getAllElements();

        foreach($elementArray as $elementSetName => $elements) {
            if($elementSetName == 'Monitor') continue;
            ob_start();
            
            $flag = false;

            $eSSlug=$this->_getElementSetSlug($elementSetName);

            $this->_p_html( ($recordType == "Collection") ? "\t\t<metadata>\n" : "\t\t\t\t<metadata>\n");

            if($eSSlug!=="") $eSSlug .= ":";

            $unqualified = array(
                'title', 'creator', 'subject', 'description', 'publisher',
                'contributor', 'date', 'type', 'format', 'identifier',
                'source', 'language', 'relation', 'coverage', 'rights'
            );

            foreach($elements as $element) {
                $eSlug = $this->_getElementSlug($element->name,$elementSetName);
                $elementTexts =  $record->getElementTexts($elementSetName,$element->name);

        	if(empty($elementTexts)) continue;

                $flag = true;
        	foreach($elementTexts as $elementText) {
                    $eSlugPlus = false;
                    $preElement = ($recordType == "Collection") ? "\t\t\t<" : "\t\t\t\t\t<";
                    if ($eSlug == "language") $eSlugPlus = $eSlug.' xsi:type="dcterms:ISO639-1"';
                    if ($eSlug == "spatial"){
                        if (preg_match('/;/', $elementText->text)) {
                            if(count(explode(';',$elementText->text)) == 2){
                                $eSlugPlus = $eSlug.' xsi:type="dcterms:BOX"';
                            }
                        } else{
                            $eSlugPlus = $eSlug.' xsi:type="dcterms:POINT"';
                        }
                    }
                    $this->_p_html( (in_array($eSlug, $unqualified)) ? ($preElement.$eSSlug.(($eSlugPlus) ? $eSlugPlus : $eSlug).">") : ($preElement."dcterms:".( ($eSlugPlus) ? $eSlugPlus : $eSlug).">"));
          		      $this->_p_html( htmlspecialchars($elementText->text));
                    $this->_p_html( (in_array($eSlug, $unqualified)) ? "</".$eSSlug.$eSlug.">\n" : "</"."dcterms:".$eSlug.">\n");
        	}
      	    }
      	    $this->_p_html( ($recordType == "Collection") ? "\t\t</metadata>\n" : "\t\t\t\t</metadata>\n");
            $this->_free_buffer($flag);
        }
        if($full) {
          $this->_p_html( ($recordType == "Collection") ? "\t\t<records>\n" : "\t\t\t</record>\n");
        }
    }

    private function _generateCeniehFooter($full = True) {
        if($full) $this->_p_html( "\t\t</records>");
        $this->_p_html( "\n\t</collection>\n");
        $this->_p_html( "</Collection>\n");
    }

    private function _generateCenieh($itemID) {
        if(!is_numeric($itemID)) {
            $this->_p_html( "ERROR: Invalid item ID");
            return;
        }

        $item = get_record_by_id("item",$itemID);

        if($item === null || empty($item)) {
            $this->_p_html( "ERROR: Invalid item ID");
            return;
        }

        $unqualified = array(
            'title', 'creator', 'subject', 'description', 'publisher',
            'contributor', 'date', 'type', 'format', 'identifier',
            'source', 'language', 'relation', 'coverage', 'rights' );

        $this->_p_html('<?xml version="1.0" encoding="UTF-8"?>'."\n");
        //--------------------
        // HEADER
        //--------------------
        $this->_p_html( '<Record ');
        $this->_p_html( 'xmlns:dc="http://purl.org/dc/elements/1.1/" ');
        $this->_p_html( 'xmlns:dcterms="http://purl.org/dc/terms/" ');
        $this->_p_html( 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ');
        $this->_p_html( 'xmlns:cenieh="http://www.cenieh.es" ');
        $this->_p_html( ">\n");
        //--------------------
        //METADATA
        //--------------------
        $elementArray = $item->getAllElements();

        foreach($elementArray as $elementSetName => $elements) {
            ob_start();
            $flag = false;

            $eSSlug=$this->_getElementSetSlug($elementSetName);

            $this->_p_html( "\t<metadata>\n");

            if($eSSlug!=="") $eSSlug .= ":";

            foreach($elements as $element) {
                $eSlug = $this->_getElementSlug($element->name,$elementSetName);

                $elementTexts =  $item->getElementTexts($elementSetName,$element->name);

                if(empty($elementTexts)) continue;
                $flag = true;

                foreach($elementTexts as $elementText) {
                    $eSlugPlus = false;
                    if ($eSlug == "language") $eSlugPlus = $eSlug.' xsi:type="dcterms:ISO639-1"';
                    if ($eSlug == "spatial"){
                        if (preg_match('/;/', $elementText->text)) {
                            if(count(explode(';',$elementText->text)) == 2){
                                $eSlugPlus = $eSlug.' xsi:type="dcterms:BOX"';
                            }
                        } else{
                            $eSlugPlus = $eSlug.' xsi:type="dcterms:POINT"';
                        }
                    }
                    $this->_p_html((in_array($eSlug, $unqualified)) ? ("\t\t\t<".$eSSlug.(($eSlugPlus) ? $eSlugPlus : $eSlug).">") : ("\t\t\t<"."dcterms:".( ($eSlugPlus) ? $eSlugPlus : $eSlug).">"));
                    $this->_p_html(htmlspecialchars($elementText->text));
                    $this->_p_html((in_array($eSlug, $unqualified)) ? "</".$eSSlug.$eSlug.">\n" : "</"."dcterms:".$eSlug.">\n");
        	}
            }

            $this->_free_buffer($flag);
        }

        $this->_p_html("\t</metadata>\n");
        $this->_p_html("</Record>\n");
    }

    private function _free_buffer($flag = True){
        if($flag) {
      	    return ob_end_flush();
      	}
        return ob_end_clean();
    }
    
    /**
     * Retrieve the slug for a given metadata element set name.
     *
     * @param string $elementSetName Name of the metadata element set
     * @return string Slug
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
     * Retrieve the slug for a given metadata element name.
     *
     *@param string $elementName Name of the metadata element
     *@return string Slug
     */
    private function _getElementSlug($elementName) {
        $dces = new DublinCoreExtendedPlugin;
        foreach ($dces->getElements() as $elementDces) {
            if ($elementName == $elementDces['label']) {
              return $elementDces['name'];
            }
        }
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $elementName));
    }

    /**
     * Prints HTML code.
     * 
     * @param type $html HTML code to print
     */
    private function _p_html($html){?><?=$html;?><?php }
 }


