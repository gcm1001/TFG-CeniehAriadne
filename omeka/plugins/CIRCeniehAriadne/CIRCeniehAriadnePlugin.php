<?php

/**
 * CIR Export plugin.
 */
class CIRCeniehAriadnePlugin extends Omeka_Plugin_AbstractPlugin
{

    /**
     * @var array $_filters Filtros que utilizaremos en el plugin.
     */
    protected $_filters = array('action_contexts','response_contexts');

    /**
     * Defino los contextos relativos a la exportación, tanto para los ítems
     * (CIR) como para las colecciones (CIRcol | CIRzip).
     *
     *@param array $contexts Contextos sin actualizar
     *@return array $contexts Contextos actualizados
     *
     */
    public function filterResponseContexts($contexts) {

      $contexts['CIR'] = array('suffix' => 'cir',
				'headers' => array('Content-Type' => 'application/octet-stream'));

      $contexts['CIRmeta'] = array('suffix' => 'cirmeta',
          'headers' => array('Content-Type' => 'application/octet-stream'));
      
      $contexts['CIRfull'] = array('suffix' => 'cirfull',
				'headers' => array('Content-Type' => 'application/octet-stream'));

      $contexts['CIRfullzip'] = array('suffix' => 'cirfullzip',
				'headers' => array('Content-Type' => 'text/xml'));

      return $contexts;
    }

    /**
     * Añado el formato CIR a la lista de exportación de Omeka
     *
     *@param array $contexts Contextos sin actualizar
     *@param array $args Parámetros de omeka
     *@return array $contexts Contextos actualizados
     */
    public function filterActionContexts($contexts, $args) {

      if($args['controller'] instanceOf ItemsController) {
	       $contexts['show'][] = 'CIR';
      } else if($args['controller'] instanceOf CollectionsController) {
	       $contexts['show'][] = 'CIRfullzip';
         $contexts['show'][] = 'CIRfull';
         $contexts['show'][] = 'CIRmeta';
      }

      return $contexts;
    }

}
