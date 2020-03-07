<?php
/**
 * CsvImportPlusPlugin class - represents the Csv Import plugin
 *
 * @copyright Copyright 2008-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package CsvImportPlus
 */

defined('CSV_IMPORT_PLUS_DIRECTORY') or define('CSV_IMPORT_PLUS_DIRECTORY', dirname(__FILE__));

/**
 * Csv Import plugin.
 */
class CsvImportPlusPlugin extends Omeka_Plugin_AbstractPlugin
{
    const MEMORY_LIMIT_OPTION_NAME = 'csv_import_plus_memory_limit';
    const PHP_PATH_OPTION_NAME = 'csv_import_plus_php_path';

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'initialize',
        'install',
        'upgrade',
        'uninstall',
        'config_form',
        'config',
        'admin_head',
        'define_acl',
        'admin_items_batch_edit_form',
        'items_batch_edit_custom',
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array('admin_navigation_main');

    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        // With some combinations of Apache/FPM/Varnish, the self constant
        // can't be used as key for properties.
        'csv_import_plus_memory_limit' => '',
        'csv_import_plus_php_path' => '',
        'csv_import_plus_identifier_field' => CsvImportPlus_ColumnMap_IdentifierField::DEFAULT_IDENTIFIER_FIELD,
        'csv_import_plus_column_delimiter' => CsvImportPlus_RowIterator::DEFAULT_COLUMN_DELIMITER,
        'csv_import_plus_enclosure' => CsvImportPlus_RowIterator::DEFAULT_ENCLOSURE,
        'csv_import_plus_element_delimiter' => CsvImportPlus_ColumnMap_Element::DEFAULT_ELEMENT_DELIMITER,
        'csv_import_plus_tag_delimiter' => CsvImportPlus_ColumnMap_Tag::DEFAULT_TAG_DELIMITER,
        'csv_import_plus_file_delimiter' => CsvImportPlus_ColumnMap_File::DEFAULT_FILE_DELIMITER,
        // Option used during the first step only.
        'csv_import_plus_html_elements' => false,
        'csv_import_plus_extra_data' => 'manual',
        // With roles, in particular if Guest User is installed.
        'csv_import_plus_allow_roles' => 'a:1:{i:0;s:5:"super";}',
        'csv_import_plus_slow_process' => 0,
        'csv_import_plus_repeat_amazon_s3' => 100,
    );

    /**
     * Add the translations.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');

        // Get the backend settings from the security.ini file.
        // This simplifies tests too (use of local paths instead of urls).
        // TODO Probably a better location to set this.
        if (!Zend_Registry::isRegistered('csv_import_plus')) {
            $iniFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'security.ini';
            $settings = new Zend_Config_Ini($iniFile, 'csv-import-plus');
            Zend_Registry::set('csv_import_plus', $settings);
        }
    }

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        $db = $this->_db;

        // Create csv imports plus table.
        // Note: CsvImportPlus_Import and CsvImportPlus_ImportedRecord are standard Zend
        // records, but not Omeka ones fully.
        $db->query("CREATE TABLE IF NOT EXISTS `{$db->CsvImportPlus_Import}` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `format` varchar(255) collate utf8_unicode_ci NOT NULL,
            `delimiter` varchar(1) collate utf8_unicode_ci NOT NULL,
            `enclosure` varchar(1) collate utf8_unicode_ci NOT NULL,
            `status` varchar(255) collate utf8_unicode_ci,
            `row_count` int(10) unsigned NOT NULL,
            `skipped_row_count` int(10) unsigned NOT NULL,
            `skipped_record_count` int(10) unsigned NOT NULL,
            `updated_record_count` int(10) unsigned NOT NULL,
            `file_position` bigint unsigned NOT NULL,
            `original_filename` text collate utf8_unicode_ci NOT NULL,
            `file_path` text collate utf8_unicode_ci NOT NULL,
            `serialized_default_values` text collate utf8_unicode_ci NOT NULL,
            `serialized_column_maps` text collate utf8_unicode_ci NOT NULL,
            `owner_id` int unsigned NOT NULL,
            `added` timestamp NOT NULL default '2000-01-01 00:00:00',
            PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        // Create csv imported records table.
        $db->query("CREATE TABLE IF NOT EXISTS `{$db->CsvImportPlus_ImportedRecord}` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `import_id` int(10) unsigned NOT NULL,
            `record_type` varchar(50) collate utf8_unicode_ci NOT NULL,
            `record_id` int(10) unsigned NOT NULL,
            `identifier` varchar(255) collate utf8_unicode_ci NOT NULL,
            PRIMARY KEY  (`id`),
            KEY (`import_id`),
            KEY `record_type_record_id` (`record_type`, `record_id`),
            KEY (`identifier`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $db->query("
            CREATE TABLE IF NOT EXISTS `{$db->CsvImportPlus_Log}` (
                `id` int(10) unsigned NOT NULL auto_increment,
                `import_id` int(10) unsigned NOT NULL,
                `priority` tinyint unsigned NOT NULL,
                `created` timestamp DEFAULT CURRENT_TIMESTAMP,
                `message` text NOT NULL,
                `params` text DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY (`import_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");

        $this->_installOptions();
    }

    /**
     * Upgrade the plugin.
     */
    public function hookUpgrade($args)
    {
        $oldVersion = $args['old_version'];
        $newVersion = $args['new_version'];
        $db = $this->_db;

        if (version_compare($oldVersion, '2.3', '<')) {
            $message = __('There is no upgrade path from the old fork of CSV Import to CSV Import+, because they can be installed simultaneously.');
            throw new Omeka_Plugin_Exception($message);
        }
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        $db = $this->_db;

        // Drop the tables.
        $sql = "DROP TABLE IF EXISTS `{$db->CsvImportPlus_Import}`";
        $db->query($sql);
        $sql = "DROP TABLE IF EXISTS `{$db->CsvImportPlus_ImportedRecord}`";
        $db->query($sql);
        $sql = "DROP TABLE IF EXISTS `{$db->CsvImportPlus_Log}`";
        $db->query($sql);

        $this->_uninstallOptions();
    }

    /**
     * Shows plugin configuration page.
     */
    public function hookConfigForm($args)
    {
        $view = get_view();
        echo $view->partial(
            'plugins/csv-import-plus-config-form.php'
        );
    }

    /**
     * Saves plugin configuration page.
     *
     * @param array Options set in the config form.
     */
    public function hookConfig($args)
    {
        $post = $args['post'];
        foreach ($this->_options as $optionKey => $optionValue) {
            if (in_array($optionKey, array(
                    'csv_import_plus_allow_roles',
                ))) {
               $post[$optionKey] = serialize($post[$optionKey]) ?: serialize(array());
            }
            if (isset($post[$optionKey])) {
                set_option($optionKey, $post[$optionKey]);
            }
        }
    }

    /**
     * Defines the plugin's access control list.
     *
     * @param array $args
     */
    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $resource = 'CsvImportPlus_Index';

        // TODO This is currently needed for tests for an undetermined reason.
        if (!$acl->has($resource)) {
            $acl->addResource($resource);
        }
        // Hack to disable CRUD actions.
        $acl->deny(null, $resource, array('show', 'add', 'edit', 'delete'));
        $acl->deny(null, $resource);

        $roles = $acl->getRoles();

        // Check that all the roles exist, in case a plugin-added role has
        // been removed (e.g. GuestUser).
        $allowRoles = unserialize(get_option('csv_import_plus_allow_roles')) ?: array();
        $allowRoles = array_intersect($roles, $allowRoles);
        if ($allowRoles) {
            $acl->allow($allowRoles, $resource);
        }

        $denyRoles = array_diff($roles, $allowRoles);
        if ($denyRoles) {
            $acl->deny($denyRoles, $resource);
        }
  }

    /**
     * Configure admin theme header.
     *
     * @param array $args
     */
    public function hookAdminHead($args)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request->getModuleName() == 'csv-import-plus') {
            queue_css_file('csv-import-plus');
            queue_js_file('csv-import-plus');
        }
    }

    /**
     * Add the CSV Import+ link to the admin main navigation.
     *
     * @param array Navigation array.
     * @return array Filtered navigation array.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('CSV Import+'),
            'uri' => url('csv-import-plus'),
            'resource' => 'CsvImportPlus_Index',
            'privilege' => 'index',
        );
        return $nav;
    }

    /**
     * Add a partial batch edit form.
     *
     * @return void
     */
    public function hookAdminItemsBatchEditForm($args)
    {
        $view = get_view();
        echo $view->partial(
            'forms/csv-import-plus-batch-edit.php'
        );
    }

    /**
     * Process the partial batch edit form.
     *
     * @return void
     */
    public function hookItemsBatchEditCustom($args)
    {
        $item = $args['item'];
        $orderByFilename = $args['custom']['csvimportplus']['orderByFilename'];
        $mixImages = $args['custom']['csvimportplus']['mixImages'];

        if ($orderByFilename) {
            $this->_sortFiles($item, (boolean) $mixImages);
        }
    }

    /**
     * Sort all files of an item by name and eventually sort images first.
     *
     * @param Item $item
     * @param boolean $mixImages
     * @return void
     */
    protected function _sortFiles($item, $mixImages = false)
    {
        if ($item->fileCount() < 2) {
            return;
        }

        $list = $item->Files;
        // Make a sort by name before sort by type.
        usort($list, function($fileA, $fileB) {
            return strcmp($fileA->original_filename, $fileB->original_filename);
        });
        // The sort by type doesn't remix all filenames.
        if (!$mixImages) {
            $images = array();
            $nonImages = array();
            foreach ($list as $file) {
                // Image.
                if (strpos($file->mime_type, 'image/') === 0) {
                    $images[] = $file;
                }
                // Non image.
                else {
                    $nonImages[] = $file;
                }
            }
            $list = array_merge($images, $nonImages);
        }

        // To avoid issues with unique index when updating (order should be
        // unique for each file of an item), all orders are reset to null before
        // true process.
        $db = $this->_db;
        $bind = array(
            $item->id,
        );
        $sql = "
            UPDATE `$db->File` files
            SET files.order = NULL
            WHERE files.item_id = ?
        ";
        $db->query($sql, $bind);

        // To avoid multiple updates, a single query is used.
        foreach ($list as &$file) {
            $file = $file->id;
        }
        // The array is made unique, because a file can be repeated.
        $list = implode(',', array_unique($list));
        $sql = "
            UPDATE `$db->File` files
            SET files.order = FIND_IN_SET(files.id, '$list')
            WHERE files.id in ($list)
        ";
        $db->query($sql);
    }
}
