<?php
/**
 * OAI Qualified DC for OAI-PMH Repository plugin
 *
 * @author John Kloor <kloor@bgsu.edu>
 * @copyright 2017 Bowling Green State University Libraries
 * @license MIT
 */

/**
 * OAI Qualified DC for OAI-PMH Repository plugin: Plugin Class
 *
 * @package OaiQdc
 */
class OaiQdcPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_filters = array('oai_pmh_repository_metadata_formats');

    public function filterOaiPmhRepositoryMetadataFormats($formats)
    {
        $formats['oai_qdc'] = array(
            'class' =>
                OaiPmhRepository_Metadata_OaiQdc::class,
            'namespace' =>
                OaiPmhRepository_Metadata_OaiQdc::METADATA_NAMESPACE,
            'schema' =>
                OaiPmhRepository_Metadata_OaiQdc::METADATA_NAMESPACE
        );

        return $formats;
    }
}
