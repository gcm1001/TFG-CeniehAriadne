<?php
/**
 * Omeka
 *  > Adapted by Gonzalo Cuesta.
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package CollectionFiles\File\MimeType\Detect\Strategy
 */
interface CollectionFiles_File_MimeType_Detect_StrategyInterface
{
    public function detect($file);
}
