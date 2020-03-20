<?php

/**
 * Box Location
 * @package: Omeka
 */
class BoxLocation extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public $item_id;
    public $box_latA;
    public $box_lonA;
    public $box_latB;
    public $box_lonB;
    public $box_latC;
    public $box_lonC;
    public $box_latD;
    public $box_lonD;
    public $box_zoom;
    public $address;
    public $map_type;


    /*
     * Executes before the record is saved.
     */
    protected function beforeSave($args)
    {
        if (is_null($this->map_type)) {
            $this->map_type = '';
        }
        if (is_null($this->address)) {
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
        if (empty($this->box_latA)) {
            $this->addError('box_latA', __('[POINT A] Box ocation requires a latitude.'));
        }
        if (empty($this->box_lonA)) {
            $this->addError('box_lonA', __('[POINT A] Box location requires a longitude.'));
        }
        if (empty($this->box_latB)) {
            $this->addError('box_latB', __('[POINT B] Box ocation requires a latitude.'));
        }
        if (empty($this->box_lonB)) {
            $this->addError('box_lonB', __('[POINT B] Box location requires a longitude.'));
        }
        if (empty($this->box_latC)) {
            $this->addError('box_latC', __('[POINT C] Box ocation requires a latitude.'));
        }
        if (empty($this->box_lonC)) {
            $this->addError('box_lonC', __('[POINT C] Box location requires a longitude.'));
        }
        if (empty($this->box_latD)) {
            $this->addError('box_latD', __('[POINT D] Box ocation requires a latitude.'));
        }
        if (empty($this->box_lonD)) {
            $this->addError('box_lonD', __('[POINT D] Box location requires a longitude.'));
        }
        if (empty($this->box_zoom)) {
            $this->addError('box_zoom', __('Box location requires zoom.'));
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
