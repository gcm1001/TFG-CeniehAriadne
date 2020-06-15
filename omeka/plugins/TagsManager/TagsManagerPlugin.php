<?php

/* 
 * Tags Manager Plugin 
 * 
 * Allows Omeka users to remove all tags at once.
 * It also gives you the option to synchronize Omeka tags with dc:subject entries.
 *
 */

class TagsManagerPlugin extends Omeka_Plugin_AbstractPlugin {
    
    protected $_hooks = array(
        'install',
        'uninstall',
        'config_form',
        'config',
        'after_save_item',
        'admin_tags_browse'
    );
    
    public function hookInstall() {
        set_option('tagsmanager_sync', true);
        set_option('tagsmanager_delbutton', true);
    }
    
    public function hookUninstall() {
        delete_option('tagsmanager_sync');
        delete_option('tagsmanager_delbutton');
    }

    public function hookConfigForm($args) {
        include 'config_form.php';
    }
    
    public function hookConfig($args) {
        $post = $args['post'];
        set_option('tagsmanager_sync', $post['sync']);
        set_option('tagsmanager_delbutton', $post['delbutton']);
    }
    
    function hookAdminTagsBrowse($args) { 
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        if (get_option('tagsmanager_delbutton') && is_allowed('Tags', 'delete')) {
            if (isset($params['like']) || isset($params['type'])) {
                $this->_p_html("<a class='button red' id='del-tags' style='margin-top:20px;' href='".html_escape(url('tags-manager/del/all', $params))."'><input style='background-color:transparent;color:white;border:none;' type='button' value='Delete results' /></a>");      
                return;
            } 
            $this->_p_html("<a class='button red' id='del-tags' style='margin-top:20px;' href='".html_escape(url('tags-manager/del/all'))."'><input style='background-color:transparent;color:white;border:none;' type='button' value='Delete all' /></a>"); 
            $this->_p_html("<script> jQuery('#del-tags').click(function(){ return confirm('Are you sure you want to delete all tags?'); }); </script>");
        }
    }
    
    function hookAfterSaveItem($args) {
        if (get_option('tagsmanager_sync')) {
            $item = $args['record'];
            $itemId = metadata($item, 'id');
            $subjects = array_unique(metadata($item, array('Dublin Core', 'Subject'), array('all' => true)));
            $database = get_db();
            
            $tagsIds = array();
            foreach ($subjects as $id => $subject) {
                $subject = str_replace("'", "''", $subject);
                $tagId = $database->query("SELECT id FROM `$database->Tags` WHERE name = '$subject'")->fetch();

                if (empty($tagId)) {
                    $database->query("INSERT INTO `$database->Tags` (name) VALUES('$subject')");
                    $tagId['id'] = $database->getAdapter()->lastInsertId();
                }
                $tagsIds[$subject] = $tagId['id'];
            }
            $database->query("DELETE FROM `$database->RecordsTags` WHERE record_id = ? AND record_type='Item'",$itemId);

            foreach (array_unique($tagsIds) as $id) {
                $database->query("INSERT INTO `$database->RecordsTags` (record_id, record_type, tag_id) VALUES ($itemId, 'Item', $id)");
            }
        }
    }
    
    private function _p_html($html){ ?>
      <?= $html ?> <?php
    }
}
    