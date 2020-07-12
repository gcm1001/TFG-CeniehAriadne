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
class CollectionFiles_File_MimeType_Detect_Strategy_FileCommand implements CollectionFiles_File_MimeType_Detect_StrategyInterface
{
    public function detect($file)
    {
        $disabled = explode(', ', ini_get('disable_functions'));
        if (in_array('shell_exec', $disabled)) {
            // shell_exec is disabled.
            return false;
        }
        $fileArg = escapeshellarg($file);
        $command = "file -ib $fileArg";
        return trim(shell_exec($command));
    }
}
