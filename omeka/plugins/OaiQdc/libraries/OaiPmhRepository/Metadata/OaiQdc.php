<?php
/**
 * OAI Qualified DC for OAI-PMH metadata format
 *
 * @author John Kloor <kloor@bgsu.edu>
 * @copyright 2017 Bowling Green State University Libraries
 * @license MIT
 */

 /**
  * Class implmenting metadata output for oai_qdc metadata format.
  *
  * @package OaiQdc
  */
class OaiPmhRepository_Metadata_OaiQdc implements
    OaiPmhRepository_Metadata_FormatInterface
{
    /** OAI-PMH metadata prefix */
    const METADATA_PREFIX = 'oai_qdc';

    /** XML namespace for output format */
    const METADATA_NAMESPACE = 'http://worldcat.org/xmlschemas/qdc-1.0/';

    /** XML schema for output format */
    const METADATA_SCHEMA =
        'http://worldcat.org/xmlschemas/qdc/1.0/qdc-1.0.xsd';

    /** XML namespace for unqualified Dublin Core elements */
    const DC_NAMESPACE_URI = 'http://purl.org/dc/elements/1.1/';

    /** XML namespace for qualified Dublin Core terms */
    const DCTERMS_NAMESPACE_URI = 'http://purl.org/dc/terms/';

    /**
     * Appends both unqualified and qualified Dublin Core metadata.
     * @param object $item The item having the metadata.
     * @param [type] $metadataElement The XML metadata element to add to.
     */
    public function appendMetadata($item, $metadataElement)
    {
        // Create a node with the metadata namespace.
        $qualifieddc = $metadataElement->ownerDocument->createElementNS(
            self::METADATA_NAMESPACE,
            'oai_qdc:qualifieddc'
        );

        // Append the node to the metadata element.
        $metadataElement->appendChild($qualifieddc);

        // Add additional namespaces to the node.
        $qualifieddc->setAttribute(
            'xmlns:dcterms',
            self::DCTERMS_NAMESPACE_URI
        );

        $qualifieddc->setAttribute(
            'xmlns:dc',
            self::DC_NAMESPACE_URI
        );

        // Set the schema location for the node.
        $qualifieddc->declareSchemaLocation(
            self::METADATA_NAMESPACE,
            self::METADATA_SCHEMA
        );

        // The fifteen unqualified DC metadata elements in original order.
        $unqualified = array(
            'title', 'creator', 'subject', 'description', 'publisher',
            'contributor', 'date', 'type', 'format', 'identifier',
            'source', 'language', 'relation', 'coverage', 'rights'
        );

        // Work through each unqualified DC element.
        foreach ($unqualified as $element) {
            // Add the item type as a dc:type if the option is set.
            if ($element === 'type') {
                if (get_option('oaipmh_repository_expose_item_type')) {
                    if ($type = $item->getProperty('item_type_name')) {
                        $qualifieddc->appendNewElement('dc:type', $type);
                    }
                }
            }

            // Add the MIME-type of the files from the item to dc:format.
            if ($element === 'format') {
                $mimeTypes = array_unique(array_map(
                    function ($file) {
                        return $file->mime_type;
                    },
                    $item->getFiles()
                ));

                foreach ($mimeTypes as $mimeType) {
                    $qualifieddc->appendNewElement('dc:format', $mimeType);
                }
            }

            // Add any metadata specified for the item.
            $texts = $item->getElementTexts('Dublin Core', ucwords($element));

            foreach ($texts as $text) {
                $qualifieddc->appendNewElement('dc:' . $element, $text->text);
            }

            // Add the URL to the item as a dc:identifier.
            if ($element === 'identifier') {
                $qualifieddc->appendNewElement(
                    'dc:identifier',
                    record_url($item, 'show', true)
                );

                // Add the URL to the item's thumbnail as a dc:identifier.
                $file = $item->getFile();

                if ($file && $file->hasThumbnail()) {
                    $qualifieddc->appendNewElement(
                        'dc:identifier',
                        $file->getWebPath('thumbnail')
                    );
                }
            }
        }

        // Load the DC metadata elements specified by DublinCoreExtended.
        require PLUGIN_DIR . DIRECTORY_SEPARATOR . 'DublinCoreExtended' .
            DIRECTORY_SEPARATOR . 'elements.php';

        // Work through each qualified DC element.
        foreach ($elements as $element) {
            // Make sure a name is specified for the element.
            if (empty($element['name'])) {
                continue;
            }

            // Do no repeat unqualified elements.
            if (in_array($element['name'], $unqualified)) {
                continue;
            }

            // Add any metadata specified for the item.
            $texts = $item->getElementTexts('Dublin Core', $element['label']);

            foreach ($texts as $text) {
                $qualifieddc->appendNewElement(
                    'dcterms:' . $element['name'],
                    $text->text
                );
            }
        }
    }
}
