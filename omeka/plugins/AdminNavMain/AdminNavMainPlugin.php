<?php
/**
 * PLUGIN AdminNavMain
 *
 */
class AdminNavMainPlugin extends Omeka_Plugin_AbstractPlugin {
    
    protected $localnav = array();
    
    protected $_hooks = array(
        'initialize',
        'config', 'config_form',
        'install', 'uninstall',
        'admin_head'
    );
    
    public function setUp(){
        parent::setUp();
        add_filter('admin_navigation_main', array($this, 'filterAdminNavigationMain'), 1000);
    }
    
    public function hookInstall() {
        set_option('admin_nav_main_sections', json_encode($this->_sections()));
    }
    
    private function _sections($arg = ''){
        if(empty($arg)){
            $sections = array(
                'Data-Manager' => array(),
                'Import-Tools' => array(),
                'Export-Tools' => array(),
                'Edit-Tools' => array(),
                'Others' => array()
            );
        } else {
            $sections = $arg;
        }
        return $sections;
    }
    
    public function hookUninstall() {
        delete_option('admin_nav_main_sections');
    }
    
    public function hookConfigForm() {        
        $sections = $this->_sections;
        
        $nav = $this->localnav;
        
        include 'config-form.php';
    }
    
    public function hookConfig($args) {
        $post = $args['post'];
        $sections = $this->_sections;
        foreach(array_keys($sections) as $section) {
            $sections[$section] = isset($post[$section]) ? $post[$section] : array();
        }
        set_option('admin_nav_main_sections', json_encode($sections));
    }
    
    public function hookInitialize() {
        $this->_sections = json_decode(get_option('admin_nav_main_sections'), true);
    }
    
    public function filterAdminNavigationMain($nav) {
        $db = get_db();
        foreach($nav as $id => $entry){
            $arr = array('id' => $id,'uri' => $entry['uri']);
            if(array_key_exists('resource', $entry)) $arr['resource'] = $entry['resource'];
            if(array_key_exists('privilege', $entry)) $arr['privilege'] = $entry['privilege'];
            ($this->localnav)[$entry['label']] = $arr;
        }
        $sections = $this->_sections;

        foreach($sections as $section => $entries) {
            $pages = array();
            if(count($entries)){
                foreach(array_keys($entries) as $entry){
                    if(!isset(($this->localnav)[$entry])){
                        if(isset(($this->_sections)[$section][$entry])){
                            unset(($this->_sections)[$section][$entry]);
                            set_option('admin_nav_main_sections', json_encode($this->_sections));
                        }
                        continue;
                    }
                    $arr = array('label' => __($entry), 'uri' => ($this->localnav)[$entry]['uri']);
                    if(array_key_exists('resource', ($this->localnav)[$entry]))
                        $arr['resource'] = ($this->localnav)[$entry]['resource'];
                    if(array_key_exists('privilege', ($this->localnav)[$entry]))
                        $arr['privilege'] = ($this->localnav)[$entry]['privilege'];
                    $pages[] = $arr;
                    unset($nav[($this->localnav)[$entry]['id']]);
                }
                $nav[] = array(
                    'label' => __(str_replace('-', ' ', $section)),
                    'uri' => url('#/'.$section),
                    'class' => 'dropdown',
                    'privilege' => in_array($section, array("Import-Tools", "Edit-Tools")) ? 'index' : '',
                    'pages' => $pages,
                );
            }
        }
        return $nav;
    }

    public function hookAdminHead($args) {
        $this->_head();
    }
    
    private function _head() {
        queue_js_url('jquery.tabledit.js');
        queue_css_file('ddm');
    }
    
    
}
?>
