<?php
$title = urlencode(option('site_title'));
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
        $database->query("DELETE FROM `$db->Tags`");
    }
}

header('Location: /admin/tags');
return;