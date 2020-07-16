<?php 

// Determine which template to use based on the item type

$type = $item->getItemType();
$type = isset($type['name']) ? $type['name'] : 'none';
switch($type){
/*
	case 'Dataset':
	include('show-template-dataset.php');
	break;
*/
	
	default:
	include('show-template-default.php');
	break;
	
}
