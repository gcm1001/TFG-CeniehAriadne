<?php

/* 
 * Tags Manager Plugin 
 * 
 * Allows Omeka users to remove all existing or searched tags.
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
        set_option('tagsmanager_sync', $_POST['sync']);
        set_option('tagsmanager_delbutton', $_POST['delbutton']);
    }
    
    function hookAdminTagsBrowse($tags) {    
        if (get_option('tagsmanager_delbutton') && is_allowed('Tags', 'delete')) {
            if ((isset($_GET['like']) || isset($_GET['type']))  && count($tags)) {
                echo "<a class='button red' style='margin-top:20px;' href='".url('tags-manager/del/all', $_GET)."'><input style='background-color:transparent;color:white;border:none;' type='button' value='Delete results' /></a>";            
            } else {
                echo "<a class='button red' style='margin-top:20px;' href='".url('tags-manager/del/all')."'><input style='background-color:transparent;color:white;border:none;' type='button' value='Delete all' /></a>";
            }
        }
    }
    
    function hookAfterSaveItem($args) {
        if (get_option('tagsmanager_sync')) {
            $item = $args['record'];
            $itemId = metadata($item, 'id');
            $subjects = metadata($item, array('Dublin Core', 'Subject'), array('all' => true));
            
            $db = get_db();
            
            $tagsIds = array();
            foreach ($subjects as $id => $subject) {
                $subject = str_replace("'", "''", $subject);
                $tagId = $db->query("SELECT id FROM `$db->Tags` WHERE name = '$subject'")->fetch();

                if (empty($tagId)) {
                    $db->query("INSERT INTO `$db->Tags` (name) VALUES('$subject')");
                    $tagId['id'] = $db->getAdapter()->lastInsertId();
                }
                $tagsIds[$subject] = $tagId['id'];
            }
            
            $db->query("DELETE FROM `$db->RecordsTags` WHERE record_id = ? AND record_type='Item'",$itemId);

            foreach ($tagsIds as $id) {
                $db->query("INSERT INTO `$db->RecordsTags` (record_id, record_type, tag_id) VALUES ($itemId, 'Item', $id)");
            }
        }
    }
    
}
    