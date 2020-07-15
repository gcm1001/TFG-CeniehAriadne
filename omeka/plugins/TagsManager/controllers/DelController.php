<?php

/**
 * Controller for delete page.
 *
 * @package CuratorMonitor
 */
class TagsManager_DelController extends Omeka_Controller_AbstractActionController {

    /**
     *  Delete all action.
     */
    public function allAction() {        
        $params = $this->getAllParams();
        $result = $this->_getResult($params);
        $search = (isset($params['like']) || isset($params['type']));
        $canDelete = is_allowed('Tags', 'delete');
        if($canDelete){
            $database = get_db();
            if ($search) {
                foreach ( $result as $tagId ) {
                    $database->query("DELETE FROM `$database->RecordsTags` WHERE tag_id=?",$tagId);
                    $database->query("DELETE FROM `$database->Tags` WHERE id=?",$tagId);
                }    
            } else {
                $database->query("DELETE FROM `$database->RecordsTags`");
                $database->query("DELETE FROM `$database->Tags`");
            }
            $this->_helper->flashMessenger(__('Tags deleted.'), 'success');
            $this->redirect('tags');
        }
        $this->_helper->flashMessenger(__('You dont have permission to remove tags.'), 'error');
        $this->redirect('tags');
    }
    
    /**
     * Returns the searched tags.
     * 
     * @param type $params
     * @return type Tags
     */
    private function _getResult($params){
        $database = get_db();
        $tagTable = $database->getTable('Tag');
        if (isset($params['like']) && isset($params['type'])) {
            $query1 = $database->query("SELECT id FROM `$database->Tags` WHERE name LIKE CONCAT('%', ?, '%')",$params['like'])->fetchAll();
            $query2 = $database->query("SELECT tag_id FROM `$database->RecordsTags` WHERE record_type=?",$params['type'])->fetchAll();
            foreach($query1 as $id){
              $resultA[] = $id['id'];
            }
            foreach($query2 as $id){
              $resultB[] = $id['tag_id'];
            }
            return array_intersect($resultA, $resultB);
        } else if (isset($params['like']) || isset($params['type'])){
            if (isset($params['like'])){
                $result = $database->query("SELECT id FROM `$database->Tags` WHERE name LIKE CONCAT('%', ?, '%')",$params['like'])->fetchAll();
            }
            if (isset($params['type'])){
                $result = $database->query("SELECT tag_id FROM `$database->RecordsTags` WHERE record_type=?",$params['type'])->fetchAll();
            }
            return $result;
        } 
        return $result = $tagTable->findAll();
        
    }
  
}
