<?php
$title = option('site_title');
$title = urlencode($title);
$canDelete = is_allowed('Tags', 'delete');

if($canDelete){
    $db = get_db();
    if ($search) {
        foreach ( $result as $tagId ) {
            $db->query("DELETE FROM `$db->RecordsTags` WHERE tag_id=?",$tagId);
            $db->query("DELETE FROM `$db->Tags` WHERE id=?",$tagId);
        }    
    } else {
        $db->query("DELETE FROM `$db->RecordsTags`");
        $db->query("DELETE FROM `$db->Tags`");
    }
}

header('Location: /admin/tags');
exit;
