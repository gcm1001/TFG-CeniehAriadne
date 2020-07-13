<?php
/**
 * Omeka
 *  > Adapted by Gonzalo Cuesta.
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */
class CollectionFiles_IndexController extends Omeka_Controller_AbstractActionController
{
    protected $_autoCsrfProtection = true;
    
    public $contexts = array(
        'show' => array('omeka-xml')
    );
    
    public function init()
    {
        $this->_helper->db->setDefaultModelName('CollectionFile');
        $this->_redirector = $this->_helper->getHelper('Redirector');
    }
    
    public function indexAction()
    {
        throw new Omeka_Controller_Exception_404;
    }
    
    public function browseAction()
    {
        throw new Omeka_Controller_Exception_404;
    }
    
    public function addAction()
    {
        throw new Omeka_Controller_Exception_404;
    }
    
    public function editAction()
    {
        $elementSets = $this->_getFileElementSets();
        $this->view->assign(compact('elementSets'));
        parent::editAction();
    }
    
    protected function _getFileElementSets()
    {
        // Get element sets assigned to "All" and "File" record types.
        $elementSets = $this->_helper->db->getTable('ElementSet')->findByRecordType('File');
        
        // Remove legacy file element sets that will most likely be phased out
        // in later versions.
        $legacyElemSetNam = array('Omeka Image File', 'Omeka Video File');
        foreach ($elementSets as $key => $elementSet) {
            if (in_array($elementSet->name, $legacyElemSetNam)) {
                unset($elementSets[$key]);
            }
        }
        
        return $elementSets;
    }
    
    protected function _getDeleteConfirmMessage($record)
    {
        return __('This will delete the file (#%d) and its associated metadata.',$record->id);
    }
    
    protected function _redirectAfterDelete($record)
    {
        // Redirect back to the collection show page for this file
        $this->_helper->flashMessenger(__('The file was successfully deleted.'), 'success');
        $this->_redirector->gotoUrl(('collections/show/id/'.$record->collection_id));
    }
}