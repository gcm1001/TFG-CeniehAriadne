<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Record\Api
 */
class Api_BoxLocation extends Omeka_Record_Api_AbstractRecordAdapter
{
    /**
     * Get the REST representation of a box location.
     *
     * @param Location $record
     * @return array
     */
    public function getRepresentation(Omeka_Record_AbstractRecord $record)
    {
        $representation = array(
            'id' => $record->id,
            'url' => $this->getResourceUrl("/geolocations/{$record->id}"),
            'latitude' => $record->latitude,
            'longitude' => $record->longitude,
            'width' => $record->width,
            'height' => $record->height,
            'zoom_level' => $record->zoom_level,
            'address' => $record->address,
            'map_type' => $record->map_type,
            'item' => array(
                'id' => $record->item_id,
                'url' => $this->getResourceUrl("/items/{$record->item_id}"),
                'resource' => 'items',
            ),
        );
        return $representation;
    }

    /**
     * Set POST data to a box location.
     *
     * @param Location $record
     * @param mixed $data
     */
    public function setPostData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->item->id)) {
            $record->item_id = $data->item->id;
        }
        if (isset($data->latitude)) {
            $record->latitude = $data->latitude;
        }
        if (isset($data->longitude)) {
            $record->longitude = $data->longitude;
        }
        if (isset($data->width)) {
            $record->width = $data->width;
        }
        if (isset($data->height)) {
            $record->height = $data->height;
        }
        if (isset($data->zoom_level)) {
            $record->zoom_level = $data->zoom_level;
        }
        if (isset($data->map_type)) {
            $record->map_type = $data->map_type;
        } else {
            $record->map_type = '';
        }
        if (isset($data->address)) {
            $record->address = $data->address;
        } else {
            $record->address = '';
        }
    }

    /**
     * Set PUT data to a box location.
     *
     * @param Location $record
     * @param mixed $data
     */
    public function setPutData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->latitude)) {
            $record->latitude = $data->latitude;
        }
        if (isset($data->longitude)) {
            $record->longitude = $data->longitude;
        }
        if (isset($data->width)) {
            $record->width = $data->width;
        }
        if (isset($data->height)) {
            $record->height = $data->height;
        }
        if (isset($data->zoom_level)) {
            $record->zoom_level = $data->zoom_level;
        }
        if (isset($data->map_type)) {
            $record->map_type = $data->map_type;
        } else {
            $record->map_type = '';
        }
        if (isset($data->address)) {
            $record->address = $data->address;
        } else {
            $record->address = '';
        }
    }
}
