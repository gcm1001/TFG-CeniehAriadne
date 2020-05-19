<?php

/**
 * Box Location
 * @package: Omeka
 */
class BoxLocation extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public $item_id;
    public $latitude;
    public $longitude;
    public $width;
    public $height;
    public $zoom_level;
    public $address;
    public $map_type;


    /*
     * Executes before the record is saved.
     */
    protected function beforeSave($args)
    {
        if ($this->map_type === null) {
            $this->map_type = '';
        }
        if ($this->address === null) {
            $this->address = '';
        }
    }

    /**
     * Validate this location before saving.
     */
    protected function _validate()
    {
        if (empty($this->item_id)) {
            $this->addError('item_id', __('Box location requires an item ID.'));
        }
        // An item must exist.
        if (!$this->getTable('Item')->exists($this->item_id)) {
            $this->addError('item_id', __('Box location requires a valid item ID.'));
        }
        // An item can only have one location. This assumes that updating an
        // existing location will never modify the item ID.
        if (empty($this->latitude)) {
            $this->addError('latitude', __('Box ocation requires a latitude.'));
        }
        if (empty($this->longitude)) {
            $this->addError('longitude', __('Box location requires a longitude.'));
        }
        if (empty($this->width)) {
            $this->addError('width', __('Box location requires a width.'));
        }
        if (empty($this->height)) {
            $this->addError('height', __('Box location requires a height.'));
        }
        if (empty($this->zoom_level)) {
            $this->addError('zoom_level', __('Box location requires zoom.'));
        }
    }

    /**
     * Identify Location records as relating to the Locations ACL resource.
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'BoxLocations';
    }
}
