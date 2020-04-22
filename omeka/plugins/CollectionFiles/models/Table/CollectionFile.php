<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

class Table_CollectionFile extends Omeka_Db_Table
{
    protected $_target = 'CollectionFile';

    public function applySearchFilters($select, $params)
    {
        $boolean = new Omeka_Filter_Boolean;

        foreach ($params as $paramName => $paramValue) {
            if ($paramValue === null || (is_string($paramValue) && trim($paramValue) == '')) {
                continue;
            }

            switch ($paramName) {
                case 'collection':
                case 'collection_id':
                    $select->where('collection_files.collection_id = ?', $paramValue);
                    break;

                case 'order':
                    if ($paramValue == 'null') {
                        $select->where('collection_files.order IS NULL');
                    } else {
                        $select->where('collection_files.order = ?', $paramValue);
                    }
                    break;

                case 'original_filename':
                    $select->where('collection_files.original_filename = ?', $paramValue);
                    break;

                case 'size_greater_then':
                    $select->where('collection_files.size > ?', $paramValue);
                    break;

                case 'has_derivative_image':
                    $this->filterByHasDerivativeImage($select, $boolean->filter($paramValue));
                    break;

                case 'mime_type':
                    $select->where('collection_files.mime_type = ?', $paramValue);
                    break;

                case 'added_since':
                    $this->filterBySince($select, $paramValue, 'added');
                    break;

                case 'modified_since':
                    $this->filterBySince($select, $paramValue, 'modified');
                    break;
            }
        }
    }

    public function filterByHasDerivativeImage($select, $hasDerivative)
    {
        if ($hasDerivative) {
            $select->where('collection_files.has_derivative_image = 1');
        } else {
            $select->where('collection_files.has_derivative_image = 0');
        }
    }

    /**
     * All files should only be retrieved if they join properly on the items
     * table.
     *
     * @return Omeka_Db_Select
     */
    public function getSelect()
    {
        $select = parent::getSelect();
        $db = $this->getDb();
        $select->joinInner(array('collections' => $db->Collection), 'collections.id = collection_files.collection_id', array());

        $permissions = new Omeka_Db_Select_PublicPermissions('Collections');
        $permissions->apply($select, 'collections');

        $select->group('collection_files.id');
        return $select;
    }

    /**
     * Retrieve a random file with an image associated with an item.
     *
     * @param int $itemId
     * @return File
     */
    public function getRandomFileWithImage($collectionId)
    {
        $select = $this->getSelect()
                       ->where('collection_files.collection_id = ? AND collection_files.has_derivative_image = 1')
                       ->order('RAND()')
                       ->limit(1);

        return $this->fetchObject($select, array($collectionId));
    }

    /**
     * Retrieve files associated with an item.
     *
     * @param int $itemId
     * @param array $fileIds Optional If given, this will only retrieve files
     * with these specific IDs.
     * @param string $sort The manner by which to order the files. For example:
     *  'id': file id, 'filename' = alphabetical by filename. The default is
     *  'order', following the user's specified order.
     * @return array
     */
    public function findByCollection($collectionId, $fileIds = array(), $sort = 'order')
    {
        $select = $this->getSelect();
        $select->where('collection_files.collection_id = ?');
        if ($fileIds) {
            $select->where('collection_files.id IN (?)', $fileIds);
        }

        $this->_orderFilesBy($select, $sort);

        return $this->fetchObjects($select, array($collectionId));
    }

    /**
     * Get a single file associated with an item, by index.
     *
     * @param int $itemId
     * @param int $index
     * @param string $sort The manner by which to order the files. For example:
     *  'id': file id, 'filename' = alphabetical by filename. The default is
     *  'order', following the user's specified order.
     * @return File|null
     */
    public function findOneByCollection($collectionId, $index = 0, $sort = 'order')
    {
        $select = $this->getSelect();
        $select->where('collection_files.collection_id = ?');
        $this->_orderFilesBy($select, $sort);
        $select->limit(1, $index);

        return $this->fetchObject($select, array($collectionId));
    }

    /**
     * Retrieve files for an item that has derivative images.
     *
     * @param int $itemId The ID of the item to get images for.
     * @param int|null $index Optional If given, this specifies the file to
     * retrieve for an item, based upon the ordering of its files.
     * @param string $sort The manner by which to order the files. For example:
     *  'id': file id, 'filename': alphabetical by filename. The default is
     *  'order', following the user's specified order.
     *
     * @return File|array
     */
    public function findWithImages($collectionId, $index = null, $sort = 'order')
    {
        $select = $this->getSelect()
                       ->where('collection_files.collection_id = ? AND collection_files.has_derivative_image = 1');

        $this->_orderFilesBy($select, $sort);

        if ($index === null) {
            return $this->fetchObjects($select, array($collectionId));
        } else {
            $select->limit(1, $index);
            return $this->fetchObject($select, array($collectionId));
        }
    }

    /**
     * Orders select results for files.
     *
     * @param Omeka_Db_Select The select object for finding files
     * @param string $sort The manner in which to order the files by.
     * For example:
     * 'id' = file id
     * 'filename' = alphabetical by filename
     */
    private function _orderFilesBy($select, $sort)
    {
        // order the files
        switch ($sort) {
            case 'order':
                $select->order('ISNULL(collection_files.order)')->order('collection_files.order');
            break;

            case 'filename':
                $select->order('collection_files.original_filename ASC');
            break;

            case 'id':
            default:
                $select->order('collection_files.id ASC');
            break;
        }
    }
}

