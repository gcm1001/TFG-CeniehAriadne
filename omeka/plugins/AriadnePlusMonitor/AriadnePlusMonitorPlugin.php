<?php

/**
 * The AriadnePlus Monitor plugin.
 * Based on: CuratorMonitor https://github.com/Daniel-KM/Omeka-plugin-CuratorMonitor
 * 
 * @license GPLv3
 * @package AriadnePlusMonitor
 */
class AriadnePlusMonitorPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_elementSetName = 'Monitor';

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install',
        'uninstall',
        'uninstall_message',
        'define_acl',
        'define_routes',
        'config_form',
        'config',
        'admin_head',
        'admin_items_panel_fields',
        'admin_items_browse_simple_each',
        'admin_items_browse_detailed_each',
        'admin_items_show_sidebar',
        'admin_collections_panel_fields',
        'admin_collections_show_sidebar',
        'admin_collections_browse',
        'admin_collections_browse_each',
        'admin_items_batch_edit_form',
        'items_batch_edit_custom',
        'admin_element_sets_form',
        'admin_element_sets_form_each',
        'before_save_item',
        'after_save_element',
        'after_save_collection',
        'before_save_collection',
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'admin_navigation_main',
        'admin_items_form_tabs',
        'admin_collections_form_tabs',
    );

    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        'ariadneplus_monitor_display_remove' => false,
        'ariadneplus_monitor_elements_unique' => array(),
        'ariadneplus_monitor_elements_steppable' => array(),
        'ariadneplus_monitor_elements_default' => array(),
        'ariadneplus_monitor_admin_items_browse' => array(
            'search' => array(),
            'filter' => array(),
            'simple' => array(),
            'detailed' => array(),
        ),
        'ariadneplus_monitor_name' => '',
        'ariadneplus_monitor_email' => '',
    );

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        // Load elements to add.
        require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'elements.php';

        $elementSet = get_record('ElementSet', array('name' => $elementSetMetadata['name']));
        if ($elementSet) {
            throw new Omeka_Plugin_Exception(__('An element set by the name "%s" already exists. You must delete that element set before to install this plugin.',
                $elementSetMetadata['name']));
        }

        $elements = $this->_getElementsList();
        unset($elementSetMetadata['elements']);

        $elementSet = insert_element_set($elementSetMetadata, $elements);

        if (!$elementSet) {
            throw new Omeka_Plugin_Exception(__('Unable to build the element set "%s".', $elementSetMetadata['name']));
        }

        // Add terms for simple vocabs and the flags "unique" and "steppable".
        $es = $elementSet->getElements();
        foreach ($es as $e) {
            foreach ($elements as $key => $element) {
                if ($element['name'] == $e->name) {
                    // Set / unset the flag for unique.
                    if (empty($element['unique'])) {
                        unset($this->_options['ariadneplus_monitor_elements_unique'][$e->id]);
                    }
                    // Add the flag.
                    else {
                        $this->_options['ariadneplus_monitor_elements_unique'][$e->id] = true;
                    }

                    // Set / unset the flag for process.
                    if (empty($element['steppable'])) {
                        unset($this->_options['ariadneplus_monitor_elements_steppable'][$e->id]);
                    }
                    // Add the flag.
                    else {
                        $this->_options['ariadneplus_monitor_elements_steppable'][$e->id] = true;
                    }

                    // Set / unset the list of terms.
                    if (!empty($element['terms'])) {
                        $vocabTerm = new SimpleVocabTerm();
                        $vocabTerm->element_id = $e->id;
                        $vocabTerm->terms = implode(PHP_EOL, $element['terms']);
                        $vocabTerm->save();
                    }

                    // Set / unset the default term.
                    if (empty($element['default']) || !in_array($element['default'], $element['terms'])) {
                        unset($this->_options['ariadneplus_monitor_elements_default'][$e->id]);
                    }
                    // Add the default.
                    else {
                        $this->_options['ariadneplus_monitor_elements_default'][$e->id] = $element['default'];
                    }
                }
            }
        }

        $this->_options['ariadneplus_monitor_elements_unique'] = json_encode($this->_options['ariadneplus_monitor_elements_unique']);
        $this->_options['ariadneplus_monitor_elements_steppable'] = json_encode($this->_options['ariadneplus_monitor_elements_steppable']);
        $this->_options['ariadneplus_monitor_elements_default'] = json_encode($this->_options['ariadneplus_monitor_elements_default']);
        $this->_options['ariadneplus_monitor_admin_items_browse'] = json_encode($this->_options['ariadneplus_monitor_admin_items_browse']);
        $this->_installOptions();
        
        // JSON Element
        $hideSettings = json_decode(get_option('hide_elements_settings'), true);
        if(!isset($hideSettings['form']['Monitor'])){
            $hideSettings['form']['Monitor'] = array('Metadata Status' => '1','GettyAAT mapping' => '1');
        }
        set_option('hide_elements_settings', json_encode($hideSettings));
        
        $db = get_db();
        
        // Log entries
        $sql = "
                CREATE TABLE IF NOT EXISTS `{$db->AriadnePlusLogEntry}` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `record_type` enum('Item', 'Collection') NOT NULL,
                    `record_id` int(10) unsigned NOT NULL,
                    `part_of` int(10) unsigned NOT NULL DEFAULT 0,
                    `user_id` int(10) unsigned NOT NULL,
                    `operation` enum('assign', 'stage', 'refresh') NOT NULL,
                    `added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    INDEX `record_type_record_id` (`record_type`, `record_id`),
                    INDEX (`added`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $db->query($sql);
        
        // Associated table to log changes of each element.
        $sql = "
                CREATE TABLE IF NOT EXISTS `{$db->AriadnePlusLogMsgs}` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `entry_id` int(10) unsigned NOT NULL,
                    `msg` mediumtext COLLATE utf8_unicode_ci NOT NULL,
                    PRIMARY KEY (`id`),
                    INDEX (`entry_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $db->query($sql);
    }

    /**
     * Helper to add new element automatically.
     */
    protected function _addNewElements()
    {
        $db = $this->_db;

        // Load elements to add.
        require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'elements.php';

        // Prepare the elements.
        $elements = $this->_getElementsList();
        unset($elementSetMetadata['elements']);

        $elementSet = get_record('ElementSet', array('name' => $elementSetMetadata['name']));
        $es = $elementSet->getElements();

        // Add new elements, but they may have been created manually, so a
        // check is needed to avoid an error.
        $newElements = array();
        foreach ($elements as $key => $element) {
            $flag = false;
            foreach ($es as $e) {
                if ($element['name'] == $e->name) {
                    $flag = true;
                    break;
                }
            }
            // This element doesn't exist.
            if (!$flag) {
                $newElements[] = $element;
            }
        }

        // Add new elements if any.
        if (!empty($newElements)) {
            $elementSet->addElements($newElements);
            $elementSet->save();

            $uniques = json_decode(get_option('ariadneplus_monitor_elements_unique'), true) ?: array();
            $steppables = json_decode(get_option('ariadneplus_monitor_elements_steppable'), true) ?: array();
            $defaultTerms = json_decode(get_option('ariadneplus_monitor_elements_default'), true) ?: array();
            foreach ($newElements as $key => $element) {
                $e = $db->getTable('Element')
                    ->findByElementSetNameAndElementName($elementSetMetadata['name'], $element['name']);

                if (!empty($element['unique'])) {
                    $uniques[$e->id] = true;
                }
                if (!empty($element['steppable'])) {
                    $steppables[$e->id] = true;
                }
                if (!empty($element['terms'])) {
                    $vocabTerm = new SimpleVocabTerm();
                    $vocabTerm->element_id = $e->id;
                    $vocabTerm->terms = implode(PHP_EOL, $element['terms']);
                    $vocabTerm->save();
                }
                if (!empty($element['default'])) {
                    $defaultTerms[$e->id] = $element['default'];
                }
            }
            set_option('ariadneplus_monitor_elements_unique', json_encode($uniques));
            set_option('ariadneplus_monitor_elements_steppable', json_encode($steppables));
            set_option('ariadneplus_monitor_elements_default', json_encode($defaultTerms));
        }
    }

    /**
     * Helper to remove old elements automatically.
     *
     * @param array $elementsToRemove
     */
    protected function _removeOldElements($elementsToRemove = array())
    {
        $db = $this->_db;

        $uniques = json_decode(get_option('ariadneplus_monitor_elements_unique'), true) ?: array();
        $steppables = json_decode(get_option('ariadneplus_monitor_elements_steppable'), true) ?: array();
        $defaultTerms = json_decode(get_option('ariadneplus_monitor_elements_default'), true) ?: array();

        $elementTable = $db->getTable('Element');
        $vocabTable = $this->_db->getTable('SimpleVocabTerm');
        foreach ($elementsToRemove as $elementName) {
            $e = $elementTable->findByElementSetNameAndElementName($this->_elementSetName, $elementName);
            if ($e) {
                $vocabTerm = $vocabTable->findByElementId($e->id);
                if ($vocabTerm) {
                    $vocabTerm->delete();
                }

                unset($uniques[$e->id]);
                unset($steppables[$e->id]);
                unset($defaultTerms[$e->id]);

                $e->delete();
            }
        }
        set_option('ariadneplus_monitor_elements_unique', json_encode($uniques));
        set_option('ariadneplus_monitor_elements_steppable', json_encode($steppables));
        set_option('ariadneplus_monitor_elements_default', json_encode($defaultTerms));
    }

    /**
     * Update the list of terms of a list of elements.
     *
     * @param array $elementsToUpdate
     */
    protected function _updateVocab($elementsToUpdate)
    {
        $db = $this->_db;

        // Prepare the elements.
        $elements = $this->_getElementsList();

        $elementTable = $db->getTable('Element');
        $vocabTable = $this->_db->getTable('SimpleVocabTerm');
        foreach ($elementsToUpdate as $elementName) {
            $e = $elementTable->findByElementSetNameAndElementName($this->_elementSetName, $elementName);
            if ($e) {
                $vocabTerm = $vocabTable->findByElementId($e->id);
                if (!$vocabTerm) {
                    $vocabTerm = new SimpleVocabTerm();
                    $vocabTerm->element_id = $e->id;
                }
                foreach ($elements as $element) {
                    if ($element['name'] == $elementName) {
                        $vocabTerm->terms = implode(PHP_EOL, $element['terms']);
                        $vocabTerm->save();
                        break;
                    }
                }
            }
        }
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        $elementSet = $this->_db->getTable('ElementSet')
            ->findByName($this->_elementSetName);

        if (!empty($elementSet)) {
            $elements = $elementSet->getElements();
            foreach ($elements as $element) {
                $simpleVocabTerm = $this->_db->getTable('SimpleVocabTerm')
                    ->findByElementId($element->id);
                if ($simpleVocabTerm) {
                    $simpleVocabTerm->delete();
                }
                $element->delete();
            }
            $elementSet->delete();
        }
        $this->_uninstallOptions();
        
        $hideSettings = json_decode(get_option('hide_elements_settings'), true);
        if(isset($hideSettings['form']['Monitor'])){
            unset($hideSettings['form']['Monitor']);
        }
        set_option('hide_elements_settings', json_encode($hideSettings));
        
        $sql = "DROP TABLE IF EXISTS `{$this->_db->AriadnePlusLogEntry}`";
        $this->_db->query($sql);
        $sql = "DROP TABLE IF EXISTS `{$this->_db->AriadnePlusLogMsg}`";
        $this->_db->query($sql);
    }

    /**
     * Display the uninstall message.
     */
    public function hookUninstallMessage()
    {
        echo __('%sWarning%s: This will remove all the Monitor elements added by this plugin and permanently delete all element texts entered in those fields.%s', '<p><strong>', '</strong>', '</p>');
    }

    /**
     * Define the plugin's access control list.
     *
     * @param array $args Parameters supplied by the hook
     * @return void
     */
    public function hookDefineAcl($args)
    {
        $args['acl']->addResource('AriadnePlusMonitor_Index');
    }
    
    
    public function hookDefineRoutes($args)
    {
        if (!is_admin_theme()) {
            return;
        }
        $args['router']->addRoute(
            'ariadneplus_record_log',
            new Zend_Controller_Router_Route(
                ':type/ariadnepluslog/:id',
                array(
                    'module' => 'ariadne-plus-monitor',
                    'controller' => 'log',
                    'action' => 'log',
                ),
                array(
                    'type' =>'items|collections',
                    'id' => '\d+',
        )));
    }
    /**
     * Shows plugin configuration page.
     *
     * @return void
     */
    public function hookConfigForm($args)
    {
        // The option is set one time only.
        set_option('AriadnePlus_monitor_display_remove', false);

        $settings = json_decode(get_option('ariadneplus_monitor_admin_items_browse'), true) ?: $this->_options['ariadneplus_monitor_admin_items_browse'];

        $table = $this->_db->getTable('Element');
        $select = $table->getSelect()
            ->order('elements.element_set_id')
            ->order('ISNULL(elements.order)')
            ->order('elements.order');
        $elements = $table->fetchObjects($select);

        $view = $args['view'];
        echo $view->partial(
            'plugins/ariadne-plus-monitor-config-form.php',
            array(
                'settings' => $settings,
                'elements' => $elements,
        ));
    }

    /**
     * Processes the configuration form.
     *
     * @return void
     */
    public function hookConfig($args)
    {
        $post = $args['post'];

        foreach ($this->_options as $optionKey => $optionValue) {
            if (isset($post[$optionKey])) {
                set_option($optionKey, $post[$optionKey]);
            }
        }

        $settings = array(
            'search' => isset($post['search']) ? $post['search'] : array(),
            'filter' => isset($post['filter']) ? $post['filter'] : array(),
            'simple' => isset($post['simple']) ? $post['simple'] : array(),
            'detailed' => isset($post['detailed']) ? $post['detailed'] : array(),
        );
        set_option('ariadneplus_monitor_admin_items_browse', json_encode($settings));
        set_option('ariadneplus_monitor_name', $_POST['ariadneplus_monitor_name']);
        set_option('ariadneplus_monitor_email', $_POST['ariadneplus_monitor_email']);
    }

    /**
     * Hook for admin head.
     *
     * @return void
     */
    public function hookAdminHead()
    {
        queue_css_file('ariadneplusmonitor'); 
        $requestParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $module = isset($requestParams['module']) ? $requestParams['module'] : 'default';
        if($module=='ariadne-plus-monitor'){
            queue_js_file('ariadneplusmonitor');
        }
    }

    /**
     * Add the specified fields in the specified part.
     */
    public function hookAdminItemsBrowseSimpleEach($args)
    {
        $this->_adminItemsBrowseDisplay($args, 'simple');
    }

    public function hookAdminItemsBrowseDetailedEach($args)
    {
        $this->_adminItemsBrowseDisplay($args, 'detailed');
    }

    protected function _adminItemsBrowseDisplay($args, $location)
    {
        $currentElements = json_decode(get_option('ariadneplus_monitor_admin_items_browse'), true) ?: array();
        if (empty($currentElements[$location])) {
            return;
        }

        $view = $args['view'];
        $item = $args['item'];
        foreach ($currentElements[$location] as $elementSetName => $displayElements) {
            $html = '';
            foreach ($displayElements as $elementName => $value) {
                $elementTexts = $item->getElementTexts($elementSetName, $elementName);
                if ($elementTexts) {
                    $elementText = reset($elementTexts);
                    $text = $elementText->html ? strip_formatting($elementText->text) : $elementText->text;
                    $html .= '<div><span class="ariadne-plus-monitor-items-element">' . $elementName . '</span>: <span>' . snippet_by_word_count($text, 12) . '</span></div>';
                }
            }
            if ($html) {
                echo '<div class="ariadne-plus-monitor-items-browse"><span>' . $elementSetName . '</span>';
                echo $html;
                echo '</div>';
            }
        }
    }

    /**
     * Add a partial batch edit form.
     *
     * @return void
     */
    public function hookAdminItemsBatchEditForm($args)
    {
        $view = $args['view'];
        $statusTermsElements = $view->monitor()->getStatusElements(null, null, true);
        $statusNoTermElements = $view->monitor()->getStatusElements(null, null, false);
        if ($statusTermsElements || $statusNoTermElements) {
            echo $view->partial(
                'forms/ariadne-plus-monitor-batch-edit.php',
                array(
                    'statusTermsElements' => $statusTermsElements,
                    'statusNoTermElements' => $statusNoTermElements,
            ));
        }
    }

    /**
     * Process the partial batch edit form.
     *
     * @return void
     */
    public function hookItemsBatchEditCustom($args)
    {
        $item = $args['item'];
        $statusTerms = array_filter($args['custom']['ariadneplusmonitor']['statusterms'], function ($v) { return strlen($v) > 0; });
        if (!empty($statusTerms)) {
            $statusTermsElements = get_view()->monitor()->getStatusElements(null, null, true);
            foreach ($statusTerms as $elementId => $termId) {
                $elementId = (integer) substr($elementId, 8);
                $item->deleteElementTextsByElementId(array($elementId));
                if ($termId !== 'remove') {
                    $item->addTextForElement(
                        $statusTermsElements[$elementId]['element'],
                        $statusTermsElements[$elementId]['terms'][$termId],
                        false);
                }  
            }
            $item->save();
        }
    }

    /**
     * Hook to manage element set.
     *
     * @param array @args
     * @return void
     */
    public function hookAdminElementSetsForm($args)
    {
        $elementSet = $args['element_set'];
        if ($elementSet->name != $this->_elementSetName) {
            return;
        }

        $view = $args['view'];

        // The option is set one time only.
        set_option('ariadneplus_monitor_display_remove', false);

        // TODO Manage order and multiple new elements dynamically.
        // Re-use the "add button" from the item-types page.

        $statusElements = $view->monitor()->getStatusElements();

        // Add a new element.
        $options = array();
        $elementTempId = '' . time();
        $elementName = '';
        $elementDescription = '';
        $elementOrder = count($statusElements) + 1;
        $elementComment = '';
        $elementUnique = false;
        $elementSteppable = false;
        $elementTerms = '';
        $elementDefault = '';

        $stem = 'new-elements' . "[$elementTempId]";
        $elementNameName = $stem . '[name]';
        $elementDescriptionName = $stem . '[description]';
        $elementOrderName = $stem . '[order]';
        $elementCommentName = $stem . '[comment]';
        $elementUniqueName = $stem . '[unique]';
        $elementSteppableName = $stem . '[steppable]';
        $elementTermsName = $stem . '[terms]';
        $elementDefaultName = $stem . '[default]';

        $options = array(
            'element_name_name' => $elementNameName,
            'element_name_value' => $elementName,
            'element_description_name' => $elementDescriptionName,
            'element_description_value' => $elementDescription,
            'element_order_name' => $elementOrderName,
            'element_order_value' => $elementOrder,
            'element_comment_name' => $elementCommentName,
            'element_comment_value' => $elementComment,
            'element_unique_name' => $elementUniqueName,
            'element_unique_value' => $elementUnique,
            'element_steppable_name' => $elementSteppableName,
            'element_steppable_value' => $elementSteppable,
            'element_terms_name' => $elementTermsName,
            'element_terms_value' => $elementTerms,
            'element_default_name' => $elementDefaultName,
            'element_default_value' => $elementDefault,
       );

        echo common('add-new-element', $options);
    }

    /**
     * Hook to manage element set.
     *
     * @param array $args
     * @return void
     */
    public function hookAdminElementSetsFormEach($args)
    {
        $elementSet = $args['element_set'];
        if ($elementSet->name != $this->_elementSetName) {
            return;
        }

        $element = $args['element'];
        $view = $args['view'];

        $statusElement = $view->monitor()->getStatusElement($element->id);

        $html = '';

        // Add unique.
        $html .= $view->formLabel('elements[' . $element->id. '][unique]', __('Unrepeatable'));
        $html .= $view->formCheckbox('elements[' . $element->id. '][unique]',
            true, array('checked' => (boolean) $statusElement['unique']));

        // Add process.
        $html .= $view->formLabel('elements[' . $element->id. '][steppable]', __('Steps of a workflow'));
        $html .= $view->formCheckbox('elements[' . $element->id. '][steppable]',
            true, array('checked' => (boolean) $statusElement['steppable']));

        // Add vocabulary terms.
        $html .= $view->formLabel('elements[' . $element->id. '][terms]', __('Terms'));
        $html .= $view->formTextarea('elements[' . $element->id. '][terms]',
            implode(PHP_EOL, $statusElement['terms']),
            array('placeholder' => __('Ordered list of concise terms, one by line'), 'rows' => '5', 'cols' => '10'));

        // Add the select for the vocabulary term.
        $html .= $view->formLabel('elements[' . $element->id. '][default]', __('Default term'));
        $html .= $view->formText('elements[' . $element->id. '][default]',
            $statusElement['default'],
            array('placeholder' => __('The default term to use for new items, or let empty')));

        // Add the remove checkbox only if requested in the config page.
        if (get_option('ariadneplus_monitor_display_remove')) {
            $html .= $view->formLabel('elements[' . $element->id. '][remove]', __('Remove this element'));
            $totalElementTexts = $this->_db->getTable('ElementText')->count(array('element_id' => $element->id));
            $html .= '<p class="explanation">' . __('Warning: All existing data (%d) for this field will be removed without further notice.', $totalElementTexts) . '</p>';
            $html .= $view->formCheckbox('elements[' . $element->id. '][remove]',
                true, array('checked' => false));
        }

        echo $html;
    }

    /**
     * Hook used after save element.
     *
     * @param array $args
     */
    public function hookAfterSaveElement($args)
    {
        // Don't use this hook during install or upgrade, because elements are
        // not standard records and the view is unavailable.
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request->getControllerName() == 'plugins') {
            return;
        }

        $record = $args['record'];

        // There is no post in this view.
        $view = get_view();

        $statusElements = $view->monitor()->getStatusElements();
        if (!isset($statusElements[$record->id])) {
            return;
        }

        // Update of an existing element.
        if (empty($args['insert'])) {
            // Get post values.
            $elements = $request->getParam('elements');

            // The "add new Element" is not a standard hook.
            $statusElementSet = $view->monitor()->getElementSet();
            $statusElementNames = $view->monitor()->getStatusElementNamesById();
            $newElements = $request->getParam('new-elements');
            $key = 0;
            foreach ($newElements as $newElement) {
                $newElement['name'] = trim($newElement['name']);
                // Check if this a unique element in the set.
                if ($newElement['name'] && !in_array($newElement['name'], $statusElementNames)) {
                    // Check if it is not a duplicate or just created.
                    $element = get_record('Element', array(
                        'name' => $newElement['name'],
                        'element_set_id' => $statusElementSet->id,
                    ));
                    if ($element) {
                        continue;
                    }

                    $order = ++$key + count($statusElements);
                    $element = $this->_createElement($newElement, $order);

                    // Update current elements to avoid issues when multiple
                    // elements are inserted.
                    $view->monitor()->resetCache();
                    $statusElements = $view->monitor()->getStatusElements();
                    $statusElementNames = $view->monitor()->getStatusElementNamesById();
                }
            }

            // Process update.
            $postElement = $elements[$record->id];

            // Check remove.
            if (!empty($postElement['remove'])) {
                $this->_setUnique($record, false);
                $this->_setSteppable($record, false);
                $this->_setTerms($record, '');
                $this->_setDefault($record, '');
                $msg = __('The element "%s" (%d) of the set "%s" is going to be removed.',
                    $record->name, $record->id, $record->set_name);
                _log('[AriadnePlusMonitor] ' . $msg, Zend_Log::WARN);
                // TODO History Log this type of remove.
                $record->delete();
                return;
            }

            // Set / unset unique.
            $this->_setUnique($record, $postElement['unique']);

            // Set / unset process.
            $this->_setSteppable($record, $postElement['steppable']);

            // Set / unset terms.
            $this->_setTerms($record, $postElement['terms']);

            // Set / unset default term.
            $this->_setDefault($record, $postElement['default']);
        }

        // The hook for the creation of a new element is not fired by Omeka.
    }

    /**
     * Add the AriadnePlus Monitor link to the admin main navigation.
     *
     * @param array $nav Navigation array.
     * @return array $filteredNav Filtered navigation array.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('AriadnePlus Monitor'),
            'uri' => url('ariadne-plus-monitor'),
            'resource' => 'AriadnePlusMonitor_Index',
            'privilege' => 'index',
        );
        return $nav;
    }

    /**
     * Modify the Monitor tab in the admin > items > edit page.
     *
     * @todo Use the controller (see SimpleVocab). Currently, use a hack and
     * a regex is fine because the admin theme can't be really changed.
     *
     * @return array of tabs
     */
    public function filterAdminItemsFormTabs($tabs, $args)
    {
        $record = $args['item'];
        if(empty(metadata($record,array('Monitor', 'Metadata Status')))){
            unset($tabs[$this->_elementSetName]);
            return $tabs;
        }
        $view = get_view();
        $tab = $tabs[$this->_elementSetName];

        // This hack uses a simple preg_replace.
        $patterns = array();
        $replacements = array();

        // Indicate the last change for each element of the Monitor element set.
        $listElements = $view->monitor()->getStatusElementNamesById();
        $lastChanges = $this->_db->getTable('HistoryLogChange')
            ->getLastChanges($record, array_keys($listElements), true);

        // The "input" button has been replaced by a button between Omeka 2.4.1
        // and Omeka 2.5.
        if (version_compare(OMEKA_VERSION, '2.5', '<')) {
            $inputString = '(<input type="submit" name="add_element_(%s)" .*? class="add-element">)';
        }
        // From Omeka 2.5.
        else {
            $inputString = '(<button name="add_element_(%s)" .*?<\/button>)';
        }

        // Add a message only for created/updated elements.
        foreach ($lastChanges as $change) {
            $pattern = sprintf($inputString, $change->element_id);
            $patterns[] = '~' . $pattern . '~';
            $replacement = '$1<p class="last-change">';
            switch ($change->type) {
                case HistoryLogChange::TYPE_CREATE:
                    $replacement .= __('Set by %s on %s', $change->displayUser(), $change->displayAdded());
                    break;
                case HistoryLogChange::TYPE_UPDATE:
                    $replacement .= __('Updated by %s on %s', $change->displayUser(), $change->displayAdded());
                    break;
                case HistoryLogChange::TYPE_DELETE:
                    $replacement .= __('Removed by %s on %s', $change->displayUser(), $change->displayAdded());
                    break;
                case HistoryLogChange::TYPE_NONE:
                default:
                    $replacement .= __('Logged by %s on %s', $change->displayUser(), $change->displayAdded());
                    break;
            }
            $replacement .= '</p>';
            $replacements[] = $replacement;
        }

        // Remove all buttons "Add element" and "Remove element" for non
        // repeatable elements.
        $listUnique = $view->monitor()->getStatusElementNamesById(true);
        $pattern =
            // This first part of pattern removes all listed buttons "Add element".
            sprintf($inputString, implode('|', array_keys($listUnique)))
            // The second part allows to keep the dropdown.
            . '(.*?)'
            // The last part removes all listed buttons "Remove element".
            . '(' . '<div class="controls"><input type="submit" name="" value="' . __('Remove') . '" class="remove-element red button"><\/div>' . ')';
        // The pattern is multiline.
        $patterns[] = '~' . $pattern . '~s';
        $replacements[] = '$3';

        // If this is a new record, set the default values.
        if (empty($record->id)) {
            $defaults = json_decode(get_option('ariadneplus_monitor_elements_default'), true) ?: array();
            foreach ($defaults as $elementId => $default) {
                if ($default) {
                    $pattern = sprintf('<select name="Elements\[%s\].*<option value="%s"', $elementId, $default);
                    // Multiline and ungreedy.
                    $patterns[] = '~(' . $pattern . ')~sU';
                    $replacements[] = '$1 selected="selected"';
                }
            }
        }
        // Update the tab.
        $tab = preg_replace($patterns, $replacements, $tab);
        $tabs[$this->_elementSetName] = $tab.$view->partial('file/gettyAATitem.php', array(
            'item' => $record
        ));
        return $tabs;
    }
    
    public function filterAdminCollectionsFormTabs($tabs, $args)
    {
        $record = $args['collection'];
        if(empty(metadata($record,array('Monitor', 'Metadata Status')))){
            unset($tabs[$this->_elementSetName]);
            return $tabs;
        }
        
        $tab = $tabs[$this->_elementSetName];
        $view = get_view();
        
        // This hack uses a simple preg_replace.
        $patterns = array();
        $replacements = array();
        
        // Indicate the last change for each element of the Monitor element set.
        $listElements = $view->monitor()->getStatusElementNamesById();
        $lastChanges = $this->_db->getTable('HistoryLogChange')
        ->getLastChanges($record, array_keys($listElements), true);
        
        // The "input" button has been replaced by a button between Omeka 2.4.1
        // and Omeka 2.5.
        if (version_compare(OMEKA_VERSION, '2.5', '<')) {
            $inputString = '(<input type="submit" name="add_element_(%s)" .*? class="add-element">)';
        }
        // From Omeka 2.5.
        else {
            $inputString = '(<button name="add_element_(%s)" .*?<\/button>)';
        }
        
        // Add a message only for created/updated elements.
        foreach ($lastChanges as $change) {
            $pattern = sprintf($inputString, $change->element_id);
            $patterns[] = '~' . $pattern . '~';
            $replacement = '$1<p class="last-change">';
            switch ($change->type) {
                case HistoryLogChange::TYPE_CREATE:
                    $replacement .= __('Set by %s on %s', $change->displayUser(), $change->displayAdded());
                    break;
                case HistoryLogChange::TYPE_UPDATE:
                    $replacement .= __('Updated by %s on %s', $change->displayUser(), $change->displayAdded());
                    break;
                case HistoryLogChange::TYPE_DELETE:
                    $replacement .= __('Removed by %s on %s', $change->displayUser(), $change->displayAdded());
                    break;
                case HistoryLogChange::TYPE_NONE:
                default:
                    $replacement .= __('Logged by %s on %s', $change->displayUser(), $change->displayAdded());
                    break;
            }
            $replacement .= '</p>';
            $replacements[] = $replacement;
        }
        
        // Remove all buttons "Add element" and "Remove element" for non
        // repeatable elements.
        $listUnique = $view->monitor()->getStatusElementNamesById(true);
        $pattern =
        // This first part of pattern removes all listed buttons "Add element".
        sprintf($inputString, implode('|', array_keys($listUnique)))
        // The second part allows to keep the dropdown.
        . '(.*?)'
            // The last part removes all listed buttons "Remove element".
        . '(' . '<div class="controls"><input type="submit" name="" value="' . __('Remove') . '" class="remove-element red button"><\/div>' . ')';
        // The pattern is multiline.
        $patterns[] = '~' . $pattern . '~s';
        $replacements[] = '$3';
        
        // If this is a new record, set the default values.
        if (empty($record->id)) {
            $defaults = json_decode(get_option('ariadneplus_monitor_elements_default'), true) ?: array();
            foreach ($defaults as $elementId => $default) {
                if ($default) {
                    $pattern = sprintf('<select name="Elements\[%s\].*<option value="%s"', $elementId, $default);
                    // Multiline and ungreedy.
                    $patterns[] = '~(' . $pattern . ')~sU';
                    $replacements[] = '$1 selected="selected"';
                }
            }
        }
        $gettyaatElementId = '';
        foreach($listElements as $elementId => $element){
            if($element == 'GettyAAT mapping'){
                $gettyaatElementId = $elementId;
            }
        }
        
        $db = get_db();
        $sql = "
        SELECT COUNT(f.id)
        FROM $db->CollectionFile f
        WHERE f.collection_id = ?";
        $has_files = (int) $db->fetchOne($sql, array((int) $record->id));
        
        $files = $db->getTable('CollectionFile')->findByCollection($record->id);
        
        // Update the tab.
        $tab = preg_replace($patterns, $replacements, $tab);
        $tabs[$this->_elementSetName] = $tab.$view->partial('file/gettyAATcollection.php', array(
            'collection' => $record,
            'has_files' => (bool) $has_files,
            'files' => $files,
        ));
        
        return $tabs;
    }
  
    /**
     * Set / unset a value in the list of unique fields.
     *
     * @param Record|integer $record
     * @param boolean $isUnique
     * @return void
     */
    protected function _setUnique($record, $isUnique)
    {
        $this->_setOptionInList($record, (boolean) $isUnique, 'ariadneplus_monitor_elements_unique');
    }

    /**
     * Set / unset a value in the list of steppable fields.
     *
     * @param Record|integer $record
     * @param boolean $isSteppable
     * @return void
     */
    protected function _setSteppable($record, $isSteppable)
    {
        $this->_setOptionInList($record, (boolean) $isSteppable, 'ariadneplus_monitor_elements_steppable');
    }

    /**
     * Set / unset a list of terms for an element.
     *
     * @param Record|integer $record
     * @param array|string $terms
     * @return void
     */
    protected function _setTerms($record, $terms)
    {
        $recordId = (integer) (is_object($record) ? $record->id : $record);
        if (is_string($terms)) {
            $terms = explode(PHP_EOL, trim($terms));
        }

        $terms = array_map('trim', $terms);
        $terms = array_filter($terms, function($value) { return strlen($value) > 0; });
        $terms = array_unique($terms);
        $statusElement = get_view()->monitor()->getStatusElement($recordId);

        // Check if an update is needed.
        if ($statusElement['terms'] === $terms) {
            return;
        }

        $vocabTerm = $statusElement['vocab'];
        // Remove terms.
        if (empty($terms)) {
            if (!empty($vocabTerm)) {
                $vocabTerm->delete();
            }
        }

        // Update or create a simple vocab term.
        else {
            if (empty($vocabTerm)) {
                $vocabTerm = new SimpleVocabTerm();
                $vocabTerm->element_id = $recordId;
            }
            $vocabTerm->terms = implode(PHP_EOL, $terms);
            $vocabTerm->save();
        }
    }

    /**
     * Set / unset the default term for an element.
     *
     * If it is not in the list of terms, it is removed.
     *
     * @param Record|integer $record
     * @param string $default term
     * @return void
     */
    protected function _setDefault($record, $default)
    {
        $newDefault = trim($default);
        // Check if the element is set in the list of terms.
        $recordId = (integer) (is_object($record) ? $record->id : $record);
        $statusElement = get_view()->monitor()->getStatusElement($recordId);
        if (!in_array($newDefault, $statusElement['terms'])) {
            $newDefault = '';
        }

        $this->_setOptionInList($record, $newDefault, 'ariadneplus_monitor_elements_default');
    }

    /**
     * Set / unset a value in an option list.
     *
     * @param Record|integer $record
     * @param var $value
     * @param string $optionList
     * @return void
     */
    protected function _setOptionInList($record, $value, $optionList)
    {
        $recordId = (integer) (is_object($record) ? $record->id : $record);
        $list = json_decode(get_option($optionList), true);
        // Set the value as key.
        if ($value) {
            $list[$recordId] = $value;
        }
        // Remove the flag.
        else {
            unset($list[$recordId]);
        }
        set_option($optionList, json_encode($list));
    }

    /**
     * Create a new element with specific monitor parameters.
     *
     * The element name should be checked and not exist.
     *
     * @param array $element
     * @param integer $order
     * @return Element
     */
    protected function _createElement($element, $order = 0)
    {
        $statusElementSet = get_record('ElementSet', array('name' => $this->_elementSetName));

        $newElement = new Element;
        $newElement->name = $element['name'];
        $newElement->element_set_id = $statusElementSet->id;
        if ($order) {
            $newElement->order = $order;
        }
        $newElement->description = $element['description'];
        $newElement->comment = $element['comment'];
        $newElement->save();

        $view = get_view();
        $view->monitor()->resetCache();
        $this->_setUnique($newElement, $element['unique']);
        $this->_setSteppable($newElement, $element['steppable']);
        $this->_setTerms($newElement, $element['terms']);
        $view->monitor()->resetCache();
        $this->_setDefault($newElement, $element['default']);

        $msg = __('The element "%s" (%d) has been added to the set "%s".',
            $newElement->name, $newElement->id, $statusElementSet->name);
        $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $flash->addMessage($msg, 'success');
        _log('[AriadnePlusMonitor] ' . $msg, Zend_Log::NOTICE);

        return $newElement;
    }

    /**
     * Prepare the elements from the list.
     *
     * @return array
     */
    private function _getElementsList()
    {
        // Load elements to add.
        require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'elements.php';
        foreach ($elementSetMetadata['elements'] as &$element) {
            $element['name'] = $element['label'];
        }
        return $elementSetMetadata['elements'];
    }
    
    /**
     * Updates the monitor elements of the items associated with the collection.
     * 
     * @param type $args
     */
    public function hookBeforeSaveCollection($args)
    {
        $collection = $args['record'];
        
        if (!empty($_FILES['collectionfile'])) {
            $jsonfiles = $this->_db->getTable('CollectionFile')->findByCollection($collection->id);
            $file = array_pop($jsonfiles);
            $url = metadata($file, 'uri');
            if(!empty($url)){
                $url = '<a href="'.$url.'"> JSON file </a>';
                $elementTexts['Monitor']['GettyAAT mapping'][] = array(
                 'text' => $url,
                 'html' => true,
                );
                $gettyElement = $this->_db->getTable('Element')->findByElementSetNameAndElementName('Monitor', 'GettyAAT mapping');
                $collection->deleteElementTextsByElementId(array($gettyElement->id));
                $collection->addTextForElement($gettyElement, $url,true);
            }
        }
    }
    
    /**
     * Updates the monitor elements of the items associated with the collection.
     * 
     * @param type $args
     */
    public function hookAfterSaveCollection($args)
    {
        $collection = $args['record'];
        $db = get_db();
        $items = get_records('Item',array('collection'=> $collection->id));
        $elementTexts = [];
        // METADATA STATUS
        $status = metadata($collection, array('Monitor','Metadata Status'));
        if(!$status){
            $status = '';
        }
        $elementTexts['Monitor']['Metadata Status'][] = array(
            'text' => $status,
            'html' => false,
        );
        // MAP ID
        $mapId = metadata($collection, array('Monitor','ID of your metadata transformation'));
        if(!$mapId){
            $mapId = '';
        }
        $elementTexts['Monitor']['ID of your metadata transformation'][] = array(
            'text' => $mapId,
            'html' => false,
        );
        // PERIODO URL
        $periodo = metadata($collection, array('Monitor','URL of your PeriodO collection'));
        if(!$periodo){
            $periodo = '';
        }
        $elementTexts['Monitor']['URL of your PeriodO collection'][] = array(
             'text' => $periodo,
             'html' => true,
        );
        //GETTY URL
        $getty = metadata($collection, array('Monitor', 'GettyAAT mapping'));
        if(!$getty){
            $periodo = '';
        }
        $elementTexts['Monitor']['GettyAAT mapping'][] = array(
         'text' => $getty,
         'html' => true,
        );
        
        // UPDATE CONFIG 
        $metadata = array(
            Builder_Item::OVERWRITE_ELEMENT_TEXTS => true,
        );
        
        // UPDATE ASSOCIATED ITEMS
        if (!empty($items) && !empty($elementTexts)) {
            foreach ($items as $item) {
                $item = update_item($item, $metadata, $elementTexts);
                release_object($item);
            }
        }
    }
    
    /**
     * Adds JSON input.
     * 
     * @param type $args
     */
    public function hookBeforeSaveItem($args){
        try {
            $item = $args['record'];
            $files = array();
            if (!empty($_FILES['itemfile'])) {
                $files = insert_files_for_item($item, 'Upload', 'itemfile', array('ignoreNoFile' => true));
                $gettyElement = $this->_db->getTable('Element')->findByElementSetNameAndElementName('Monitor', 'GettyAAT mapping');
                $item->deleteElementTextsByElementId(array($gettyElement->id));
                foreach($files as $file){
                    $item->addTextForElement($gettyElement, '<a href="'.metadata($file,'uri').'" >JSON file</a>',true);
                }
            } 
        } catch (Omeka_File_Ingest_InvalidException $e) {
            $this->addError('File Upload', $e->getMessage());
        }
    }
    /**
     * Only one file per Collection
     * @param type $args
     */
    public function hookAfterSaveItem($args){
        $item = $args['record'];
        if (!empty($_FILES['itemfile'])) {
            $jsonfiles = $this->_db->getTable('File')->findByItem($item->id);
            if(count($jsonfiles) > 1){
                array_pop($jsonfiles);
                foreach($jsonfiles as $jsonfile){
                    $jsonfile->delete();
                }
            }
        }
    }

    
    /**
     * Adds status badge to the collections browse view.
     * 
     * @param type $args
     */
    public function hookAdminCollectionsBrowseEach($args){
        $collection = $args['collection'];
        $this->printStatus($collection);
    }
    
    /**
     * Updates collection state.
     * 
     * @param type $args
     */
    public function hookAdminCollectionsBrowse($args){
        
        if(isset($_GET["proposed"])){
            $collection = get_record_by_id('Collection',$_GET["proposed"]);
            
            if($collection){
                $metadata = array(
                    Builder_Item::OVERWRITE_ELEMENT_TEXTS => true,
                );
                $elementTexts['Monitor']['Metadata Status'][] = array(
                    'text' => 'Proposed',
                    'html' => false,
                );
                $collection = update_collection($collection, $metadata, $elementTexts);
                release_object($collection);
                header("Refresh:0; url=".url('collections'));
            }
        }
      
    }
    
    /**
     * Adds status badge to the collections show view.
     * 
     * @param type $args
     */
    public function hookAdminCollectionsShowSidebar($args){
        $collection = $args['collection'];
        echo '<div class="panel">';
        echo "<h4> Ariadne+ Status</h4> </br>";
        $this->printStatus($collection);
        echo '</div>';
    }
    
    /**
     * Adds status badge to the items show view.
     * 
     * @param type $args
     */
    public function hookAdminItemsShowSidebar($args){
        $item = $args['item'];
        echo '<div class="panel">';
        echo "<h4> Ariadne+ Status</h4> </br>";
        $this->printStatus($item);
        echo '</div>';
    }

    /**
     * Generates HTML code for the status badges.
     * 
     * @param type $record
     */
    private function printStatus($record){
        $state = metadata($record,array('Monitor','Metadata Status'));
        $type = is_a($record, 'Collection') ? 'collection' : 'item';
        if($state) {
            echo '<div class="badge">';
            echo '<a href='.url('ariadne-plus-monitor', array('record_type'=> ucfirst($type) ,$type => $record->id )).'>';
            echo '<div class="name"><span>A+ Status</span></div>';
            echo '<div class="status '.trim(strtolower($state)).'"> <span>'.$state.'</span> </div>';
            echo '</a></div>';
        } else {
            echo '<div class="badge">';
            printf('<a href="%s">',
                html_escape(url($type.'s', array('proposed' => $record->id ))));
            echo '<div class="name"><span>No Status</span></div>';
            echo '<div class="status noprogress"><span>Add to publish process</span></div>';
            echo '</a></div> <div></div>';
            
        }
    }
    
    public function hookAdminItemsPanelFields($args){
        $item = $args['record'];
        $state = metadata($item, array('Monitor', 'Metadata Status'));
        if($state != null && $state != ''){
            $blocks = array();
            
            if($state != 'Incomplete'){
              array_push($blocks,'dublin-core', 'item-type-metadata', 'map', 'files', 'tags');
            }
            if($state == 'Enriched'){
              array_push($blocks,'monitor');
            }
            $this->printScriptsToDisableMetadata($blocks);
        }
    }
    
    public function hookAdminCollectionsPanelFields($args)
    {
        $collection = $args['record'];
        $state = metadata($collection, array('Monitor', 'Metadata Status'));
        if($state != null && $state != ''){
            $blocks = array();
            
            if($state != 'Incomplete'){
              array_push($blocks,'dublin-core','files');
            }
            if($state == 'Enriched'){
              array_push($blocks,'monitor');
            }
  
            $this->printScriptsToDisableMetadata($blocks);
        }
    }
    
    private function printScriptsToDisableMetadata($sections)
    {
        echo __('<script type="text/javascript" charset="utf-8">'); 
        echo __("jQuery('#collection-id').attr('disabled',true);
                 jQuery('#public').attr('disabled',true);
                 jQuery('#featured').attr('disabled',true);");
        foreach($sections as $section){
            if($section == 'files') echo __("jQuery('#".$section."-metadata .add-new').remove();
                                             jQuery('#".$section."-metadata .drawer-contents').remove();
                                             jQuery('#".$section."-metadata .edit').remove();
                                             jQuery('#".$section."-metadata .delete').remove(); ");
            if($section == 'map') echo __("jQuery('#".$section."-metadata .leaflet-control-container').remove(); ");
            if($section == 'tags') echo __("jQuery('#".$section."-metadata .remove-tag').remove();");  

            echo __("jQuery('#".$section."-metadata textarea').attr('disabled',true);
                     jQuery('#".$section."-metadata checkbox').attr('disabled',true);
                     jQuery('#".$section."-metadata button').attr('disabled',true);
                     jQuery('#".$section."-metadata input').attr('disabled',true);
                     jQuery('#".$section."-metadata select').attr('disabled',true);"); 
        }
        echo __('</script>');
    }
    
    
}