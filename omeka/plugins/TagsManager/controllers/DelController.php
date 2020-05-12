<?php

class TagsManager_DelController extends Omeka_Controller_AbstractActionController {

    public function allAction() {
        $search = false;
        $db = get_db();
        $tagTable = $db->getTable('Tag');
        $params = $this->getAllParams();
        if (isset($params['like']) && isset($params['type'])) {
            $r1 = $db->query("SELECT id FROM `$db->Tags` WHERE name LIKE CONCAT('%', ?, '%')",$params['like'])->fetchAll();
            $r2 = $db->query("SELECT tag_id FROM `$db->RecordsTags` WHERE record_type=?",$params['type'])->fetchAll();
            $result = array_intersect($r1,$r2);
            $search = true;
        } else if (isset($params['like']) || isset($params['type'])){
            if (isset($params['like'])){
                $result = $db->query("SELECT id FROM `$db->Tags` WHERE name LIKE CONCAT('%', ?, '%')",$params['like'])->fetchAll();
            }
            if (isset($params['type'])){
                $result = $db->query("SELECT tag_id FROM `$db->RecordsTags` WHERE record_type=?",$params['type'])->fetchAll();
            }
            $search = true;
        } else {
            $result = $tagTable->findAll();
        }
        $this->view->result = $result;
        $this->view->search = $search;
    }
  
}
