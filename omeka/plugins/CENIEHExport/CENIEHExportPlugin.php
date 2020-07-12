<?php

/**
 * CENIEH Export plugin.
 */
class CENIEHExportPlugin extends Omeka_Plugin_AbstractPlugin
{

    /**
     * @var array $_filters Filters for the plugin.
     */
    protected $_filters = array('action_contexts','response_contexts');

    /**
     * Define the CENIEH context and set browser headers 
     * to output an XML file with a .xml extension
     *
     *@param array $contexts Unfiltered contexts
     *@return array $contexts Filtered contexts
     *
     */
    public function filterResponseContexts($contexts) {

      $contexts['CENIEH'] = array('suffix' => 'cenieh',
				'headers' => array('Content-Type' => 'application/octet-stream'));

      $contexts['CENIEHmeta'] = array('suffix' => 'cenieh-meta',
          'headers' => array('Content-Type' => 'application/octet-stream'));
      
      $contexts['CENIEHfull'] = array('suffix' => 'cenieh-full',
				'headers' => array('Content-Type' => 'application/octet-stream'));

      $contexts['CENIEHfullzip'] = array('suffix' => 'cenieh-full-zip',
				'headers' => array('Content-Type' => 'text/xml'));

      return $contexts;
    }

    /**
     * Add CENIEH format to Omeka item output list
     *
     *@param array $contexts Unfiltered action contexts
     *@param array $args Parameters 
     *@return array $contexts Filtered action contexts
     */
    public function filterActionContexts($contexts, $args) {

      if($args['controller'] instanceOf ItemsController) {
	       $contexts['show'][] = 'CENIEH';
      } else if($args['controller'] instanceOf CollectionsController) {
	 $contexts['show'][] = 'CENIEHfullzip';
         $contexts['show'][] = 'CENIEHfull';
         $contexts['show'][] = 'CENIEHmeta';
      }
      return $contexts;
    }

}
