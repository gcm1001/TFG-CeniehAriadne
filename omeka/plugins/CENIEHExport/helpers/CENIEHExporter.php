<?php
/**
 * CENIEH Export Plugin helper class that generates the content of the xml document..
 */
 Class CENIEHExporter {
   
   private $_unqualified = array(
                'title', 'creator', 'subject', 'description', 'publisher',
                'contributor', 'date', 'type', 'format', 'identifier',
                'source', 'language', 'relation', 'coverage', 'rights'
            );

    /**
     * Returns CENIEH xml for a given single Omeka item
     *
     *@param int $itemID ID of the Omeka item
     *@return string $xml Contents of the CENIEH file
     */
    public function exportItem($itemID) {
        ob_start();
        $this->_generateCeniehItem($itemID,false);
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
        $this->_p_html('<Collection ');
        $this->_p_html( 'xmlns:cenieh="https://cenieh.es" ');
        $this->_p_html( 'xmlns:dc="http://purl.org/dc/elements/1.1/" ');
        $this->_p_html( 'xmlns:dcterms="http://purl.org/dc/terms/" ');
        $this->_p_html( 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"');
        $this->_p_html( ">\n");

    }
  
    private function _generateCeniehBody($recordID,$recordType) {
        $record = get_record_by_id($recordType,$recordID);
        //--------------------
        //METADATA
        //--------------------
        $this->_p_html(($recordType == "Collection") ? "\t<records>\n\t\t<record type=\"collection\">\n" : "\t\t<record type=\"item\">\n");
        $elementArray = $record->getAllElements();
        $this->_p_html("\t\t\t<metadata>\n");
        foreach($elementArray as $elementSetName => $elements) {
            if($elementSetName == 'Monitor') continue;
            ob_start();
            $flag = false;
            $eSSlug=$this->_getElementSetSlug($elementSetName);
            if($eSSlug!=="") $eSSlug .= ":";
            foreach($elements as $element) {
                $eSlug = $this->_getElementSlug($element->name,$elementSetName);
                $elementTexts =  $record->getElementTexts($elementSetName,$element->name);
        	if(empty($elementTexts)) continue;
                $flag = true;
        	foreach($elementTexts as $elementText) {
                    $this->_generateElement(array('eSlug' => $eSlug, 'eSSlug' => $eSSlug, 'text' => $elementText), true);
        	}
      	    }
            $this->_free_buffer($flag);
        }
        $this->_p_html("\t\t\t</metadata>\n");
        $this->_p_html("\t\t</record>\n");
    }

    private function _generateCeniehFooter() {
        $this->_p_html("\t</records>\n");
        $this->_p_html("</Collection>\n");
    }

    private function _generateCeniehItem($itemID) {
        if(!is_numeric($itemID)) {
            $this->_p_html( "ERROR: Invalid item ID");
            return;
        }

        $item = get_record_by_id("item",$itemID);

        if($item === null || empty($item)) {
            $this->_p_html( "ERROR: Invalid item ID");
            return;
        }

        $this->_p_html('<?xml version="1.0" encoding="UTF-8"?>'."\n");
        //--------------------
        // HEADER
        //--------------------
        $this->_p_html( '<Item ');
        $this->_p_html( 'xmlns:cenieh="https://cenieh.es" ');
        $this->_p_html( 'xmlns:dc="http://purl.org/dc/elements/1.1/" ');
        $this->_p_html( 'xmlns:dcterms="http://purl.org/dc/terms/" ');
        $this->_p_html( 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"');
        $this->_p_html( ">\n");
        //--------------------
        //METADATA
        //--------------------
        $elementArray = $item->getAllElements();

        $this->_p_html( "\t<metadata>\n");
        foreach($elementArray as $elementSetName => $elements) {
            if($elementSetName == 'Monitor') continue;
            ob_start();
            $flag = false;

            $eSSlug=$this->_getElementSetSlug($elementSetName);
            
            if($eSSlug!=="") $eSSlug .= ":";

            foreach($elements as $element) {
                $eSlug = $this->_getElementSlug($element->name,$elementSetName);
                $elementTexts =  $item->getElementTexts($elementSetName,$element->name);
                if(empty($elementTexts)) continue;
                $flag = true;
                foreach($elementTexts as $elementText) {
                    $this->_generateElement(array('eSlug' => $eSlug, 'eSSlug' => $eSSlug, 'text' => $elementText), false);
        	}
            }
            $this->_free_buffer($flag);
        }
        $this->_p_html("\t</metadata>\n");
        $this->_p_html("</Item>\n");
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
     * Prints an element.
     *
     * @param type $args Element Data
     */
    private function _generateElement($args, $colExport){
        $eSlug = $args['eSlug'];
        $eSSlug = $args['eSSlug'];
        $text = $args['text'];
        $tabs = $colExport ? "\t\t\t\t" : "\t\t";
        $content = $text->text;
        $eSlugInfo = '';
        $addtabs = '';
        if ($eSlug == "language"){
          $eSlugInfo = $this->_languageISO($text); 
        } else if ($eSlug == "spatial"){
          $eSlugInfo = $this->_spatialFormat($text);
          $addtabs = !empty($eSlugInfo) ? $tabs : '';
          $content = $this->_generateSpatialContent($eSlugInfo, $content, $tabs);
        }
        $this->_p_html( (in_array($eSlug, $this->_unqualified)) ? $tabs."<".$eSSlug.$eSlug.$eSlugInfo.">" : $tabs."<"."dcterms:".$eSlug.$eSlugInfo.">");
        $this->_p_html(empty($eSlugInfo) ? htmlspecialchars($content) : $content);
        $this->_p_html( (in_array($eSlug, $this->_unqualified)) ? $addtabs."</".$eSSlug.$eSlug.">\n" : $addtabs."</"."dcterms:".$eSlug.">\n");
    }
    
    /**
     * Identifies the standard used in the "Language" field.
     * 
     * @param type $elementText "Language" Element text
     * @return string xsi:type
     */
    private function _languageISO($elementText){
      $text = $elementText->text;
      switch(strlen($text)){
        case 2:
          return ' xsi:type="dcterms:ISO639-1"';
        case 3:
          return ' xsi:type="dcterms:ISO639-2"';
        default:
          return '';
      }
    }

    /**
     * Identifies the location type used in the "Spatial Coverage" field.
     * 
     * @param type $elementText "Spatial Coverage" Element text
     * @return string xsi:type
     */
    private function _spatialFormat($elementText){
        $content = $elementText->text;
        $nCoords = count(explode(',',$content));
        if ($nCoords == 4) {
            list($westlim,$southlim,$northlim,$eastlim) = explode(',',$content); 
            if(is_numeric($westlim) && is_numeric($southlim) && 
                    is_numeric($northlim) && is_numeric($eastlim)){
                return ' xsi:type="dcterms:BOX"';
            }
        } else if ($nCoords == 2) {
            list($lat,$lon) = explode(',',$content);
            if(is_numeric($lat) && is_numeric($lon)){
                return ' xsi:type="dcterms:POINT"';
            }
        }
        return '';
    }
    
    /**
     * Returns the content of the "Spatial Coverage" field in CENIEH XML format.
     * 
     * @param type $format xsi:type
     * @param type $content Element text
     * @param type $tabs Tabs
     * @return string New content
     */
    private function _generateSpatialContent($format, $content, $tabs){
      if(strpos($format, 'type="dcterms:BOX"')) {
          list($westlim,$southlim,$northlim,$eastlim) = explode(',',$content); 
          $content = "\n";
          $content .= $tabs."\t<northlimit>".trim($northlim)."</northlimit>\n";
          $content .= $tabs."\t<eastlimit>".trim($eastlim)."</eastlimit>\n";
          $content .= $tabs."\t<southlimit>".trim($southlim)."</southlimit>\n";
          $content .= $tabs."\t<westlimit>".trim($westlim)."</westlimit>\n";
      } else if (strpos($format, 'type="dcterms:POINT"')) {
          list($lat,$lon) = explode(',',$content);
          $content = "\n";
          $content .= $tabs."\t<latitude>".trim($lat)."</latitude>\n";
          $content .= $tabs."\t<longitude>".trim($lon)."</longitude>\n";
      }
      return $content;
    }
    
    /**
     * Prints HTML code.
     * 
     * @param type $html HTML code to print
     */
    private function _p_html($html){?><?= $html;?><?php }
    
}


