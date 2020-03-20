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
            'box_latA' => $record->box_latA,
            'box_lonA' => $record->box_lonA,
            'box_latB' => $record->box_latB,
            'box_lonB' => $record->box_lonB,
            'box_latC' => $record->box_latC,
            'box_lonC' => $record->box_lonC,
            'box_latD' => $record->box_latD,
            'box_lonD' => $record->box_lonD,
            'box_zoom' => $record->box_zoom,
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
        if (isset($data->box_latA)) {
            $record->box_latA = $data->box_latA;
        }
        if (isset($data->box_lonA)) {
            $record->box_lonA = $data->box_lonA;
        }
        if (isset($data->box_latB)) {
            $record->box_latB = $data->box_latB;
        }
        if (isset($data->box_lonB)) {
            $record->box_lonB = $data->box_lonB;
        }
        if (isset($data->box_latC)) {
            $record->box_latC = $data->box_latC;
        }
        if (isset($data->box_lonC)) {
            $record->box_lonC = $data->box_lonC;
        }
        if (isset($data->box_latD)) {
            $record->box_latD = $data->box_latD;
        }
        if (isset($data->box_lonD)) {
            $record->box_lonD = $data->box_lonD;
        }
        if (isset($data->box_zoom)) {
            $record->box_zoom = $data->box_zoom;
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
        if (isset($data->box_latA)) {
            $record->box_latA = $data->box_latA;
        }
        if (isset($data->box_lonA)) {
            $record->box_lonA = $data->box_lonA;
        }
        if (isset($data->box_latB)) {
            $record->box_latB = $data->box_latB;
        }
        if (isset($data->box_lonB)) {
            $record->box_lonB = $data->box_lonB;
        }
        if (isset($data->box_latC)) {
            $record->box_latC = $data->box_latC;
        }
        if (isset($data->box_lonC)) {
            $record->box_lonC = $data->box_lonC;
        }
        if (isset($data->box_latD)) {
            $record->box_latD = $data->box_latD;
        }
        if (isset($data->box_lonD)) {
            $record->box_lonD = $data->box_lonD;
        }
        if (isset($data->box_zoom)) {
            $record->box_zoom = $data->box_zoom;
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
