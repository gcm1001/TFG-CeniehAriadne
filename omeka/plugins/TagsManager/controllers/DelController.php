<?php

class TagsManager_DelController extends Omeka_Controller_AbstractActionController {

    public function allAction() {        
        $params = $this->getAllParams();
        $this->view->result = $this->_getResult($params);
        $this->view->search = (isset($params['like']) || isset($params['type']));
        
    }
    
    private function _getResult($params){
        $database = get_db();
        $tagTable = $database->getTable('Tag');
        if (isset($params['like']) && isset($params['type'])) {
            $query1 = $database->query("SELECT id FROM `$database->Tags` WHERE name LIKE CONCAT('%', ?, '%')",$params['like'])->fetchAll();
            $query2 = $database->query("SELECT tag_id FROM `$database->RecordsTags` WHERE record_type=?",$params['type'])->fetchAll();
            $result = array_intersect($query1,$query2);
            return $result;
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
