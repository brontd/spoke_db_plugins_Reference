<?php
/**
 * Reference
 *
 * Allows to serve an alphabetized and a hierarchical page of links to searches
 * for all item types and elements of all items of an Omeka instance.
 *
 * @copyright William Mayo 2011
 * @copyright Copyright Daniel Berthereau, 2014-2018
 * @copyright Copyright Daniele Binaghi, 2020
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Reference
 */

/**
 * The Reference plugin.
 * @package Omeka\Plugins\Reference
 */
class ReferencePlugin extends Omeka_Plugin_AbstractPlugin
{
    const REFERENCE_PATH_LIST = 'references';
    const REFERENCE_PATH_TREE = 'subjects/tree';

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'initialize',
        'install',
        'uninstall',
        'config_form',
        'config',
        'define_routes',
        'public_head',
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'public_navigation_main',
        'public_navigation_items',
    );

    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        // 49 is the element id of Dublin Core Subject, forced during install.
        'reference_slugs' => array(
            'subject' => array(
                'id' => 49,
                'type' => 'Element',
                'label' => 'Subject',
                'active' => true,
            ),
        ),
        'reference_list_skiplinks' => true,
        'reference_list_alphabet' => '',
        'reference_list_headings' => true,
        'reference_link_to_single' => true,
        'reference_hide_empty' => false,
        'reference_show_count' => true,
        'reference_link_columns' => 1,
        'reference_tree_enabled' => false,
        'reference_tree_expanded' => true,
        'reference_tree_hierarchy' => '',
        'reference_query_type' => 'is exactly',
    );

    /**
     * Add the translations.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'languages');
        add_shortcode('reference', array($this, 'shortcodeReference'));
        add_shortcode('subjects', array($this, 'shortcodeSubjects'));
    }

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        // Keep old parameters.
        if (plugin_is_active('SubjectBrowse')) {
            $this->_options['reference_slugs']['subject']['active'] = get_option('subject_browse_list_enabled');
            $this->_options['reference_list_skiplinks'] = get_option('subject_browse_list_skiplinks');
            $this->_options['reference_list_headings'] = get_option('subject_browse_list_headings');
            $this->_options['reference_tree_enabled'] = get_option('subject_browse_tree_enabled');
            $this->_options['reference_tree_expanded'] = get_option('subject_browse_tree_expanded');
            $this->_options['reference_tree_hierarchy'] = get_option('subject_browse_tree_hierarchy');
        }

        $this->_options['reference_slugs'] = json_encode($this->_options['reference_slugs']);

        $this->_installOptions();

        $this->_updateSlugs();
    }

    /**
     * Helper to create or update the default slugs with existing or new item
     * types and elements.
     *
     * @return string
     */
    protected function _updateSlugs()
    {
        $slugs = json_decode(get_option('reference_slugs'), true) ?: $this->_options['reference_slugs'];

        // Remove deleted records.
        foreach ($slugs as $slug => $slugData) {
            $record = $this->_db->getTable($slugData['type'])->find($slugData['id']);
            if (empty($record)) {
                unset($slugs[$slug]);
            }
        }

        // Data are in an array ordered by slug to be retrieved quickly, but it
        // implies to rebuild the list of ids by slug here.
        $idsBySlug = array();
        foreach ($slugs as $slug => $slugData) {
            $idsBySlug[$slug] = $slugData['id'];
        }

        // Initialize the empty slugs for item types and elements.
        $itemTypes = $this->_db->getTable('ItemType')->findPairsForSelectForm();

        $table = $this->_db->getTable('Element');
        $select = $table->getSelect()
            ->order('elements.element_set_id')
            ->order('ISNULL(elements.order)')
            ->order('elements.order');
        $elements = $table->fetchObjects($select);

        $elementNamesById = array();
        foreach ($elements as $key => $element) {
            $elementSet = $element->getElementSet();
            // By default, no set name for Dublin Core.
            $slug = $elementSet->name == 'Dublin Core'
                ? $element->name
                : $elementSet->name . ' : ' . $element->name;
            $elementNamesById[$element->id] = $slug;
        }

        foreach (array(
                'ItemType' => $itemTypes,
                'Element' => $elementNamesById,
            ) as $type => $records) {
            foreach ($records as $id => $name) {
                $slugsFromId = array_keys($idsBySlug, $id);
                $slug = '';
                foreach ($slugsFromId as $slugFromId) {
                    if ($slugs[$slugFromId]['type'] == $type) {
                        $slug = $slugFromId;
                        break;
                    }
                }
                if (empty($slug)) {
                    $newSlug = strtolower($name);
                    $newSlug = str_replace(array(' ', ':', '/'), '-', $newSlug);
                    $newSlug = preg_replace('/\-+/', '-', $newSlug);
                    if (isset($idsBySlug[$newSlug])) {
                        $newSlug = str_replace('_', '-', Inflector::underscore($type)) . '-' . $newSlug;
                        $newSlug = preg_replace('/\-+/', '-', $newSlug);
                        if (isset($idsBySlug[$newSlug])) {
                            $newSlug .= '-' . substr(md5(rand()), 0, 7);
                        }
                    }
                    // Keep only alphanumeric characters, underscores, dashes.
                    $slugs[$newSlug]['id'] = $id;
                    $slugs[$newSlug]['type'] = $type;
                    $slugs[$newSlug]['label'] = $name;
                    $slugs[$newSlug]['active'] = false;
                    $idsBySlug[$newSlug] = $id;
                }
            }
        }

        // Order slugs by Item Types, then by Elements.
        $orderedSlugs = array();
        foreach (array(
                'ItemType' => $itemTypes,
                'Element' => $elementNamesById,
            ) as $type => $list) {
            foreach ($list as $id => $name) {
                foreach ($slugs as $slug => $slugData) {
                    if ($slugData['id'] == $id && $slugData['type'] == $type) {
                        $orderedSlugs[$slug] = $slugData;
                        unset($slugs[$slug]);
                        break;
                    }
                }
            }
        }

        set_option('reference_slugs', json_encode($orderedSlugs));
        return $orderedSlugs;
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        delete_option('reference_list_enabled');
        $this->_uninstallOptions();
    }

    /**
     * Shows plugin configuration page.
     */
    public function hookConfigForm($args)
    {
        $tables = array();
        foreach (array('ItemType', 'Element') as $type) {
            $tables[$type] = get_db()->getTable($type);
        }

        $view = $args['view'];
        echo $view->partial(
            'plugins/reference-config-form.php',
            array(
                'slugs' => $this->_updateSlugs(),
                'tables' => $tables,
        ));
    }

    /**
     * Processes the configuration form.
     */
    public function hookConfig($args)
    {
        $post = $args['post'];

        // Check the list of slugs.
        $slugs = array();
        $totalSlugs = 0;
        foreach ($post['slugs'] as $type => $slugsById) {
            $slugs = array_merge($slugs, $slugsById);
            $totalSlugs += count($slugsById);
        }
        $slugs = array_filter($slugs);
        if (count($slugs) != $totalSlugs) {
            $msg = __('Some slugs are empty.');
            $msg .= ' ' . __('Changes were reverted.');
            throw new Omeka_Validate_Exception($msg);
        }
        $slugs = array_unique($slugs);
        if (count($slugs) != $totalSlugs) {
            $msg = __('Some slugs are not unique.');
            $msg .= ' ' . __('Changes were reverted.');
            throw new Omeka_Validate_Exception($msg);
        }

        // Rebuild the slugs data.
        $slugsData = array();
        $types = array();
        foreach ($post['slugs'] as $type => $slugsById) {
            foreach ($slugsById as $id => $slug) {
                $slugsData[$slug] = array();
                $slugsData[$slug]['id'] = $id;
                $slugsData[$slug]['type'] = $type;
                $slugsData[$slug]['label'] = $post['labels'][$type][$id];
                $slugsData[$slug]['active'] = $post['actives'][$type][$id];
                if ($slugsData[$slug]['active']) $types[$type] = true;
            }
        }

        unset($post['slugs']);
        unset($post['labels']);
        unset($post['actives']);
        $post['reference_slugs'] = $slugsData;

        foreach ($this->_options as $optionKey => $optionValue) {
            if (in_array($optionKey, array('reference_slugs'))) {
                $post[$optionKey] = json_encode($post[$optionKey]) ?: json_encode($optionValue);
            }
            if (isset($post[$optionKey])) {
                set_option($optionKey, $post[$optionKey]);
            }
        }
        
        $this->updatePublicNavigationMain($types);
    }

    /**
     * Defines public routes
     */
    public function hookDefineRoutes($args)
    {
        $args['router']->addConfig(new Zend_Config_Ini(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'routes.ini', 
            'routes'
        ));
    }

    public function hookPublicHead($args)
    {
        queue_css_file('reference');
    }

    /**
     * Filter for public main navigation.
     *
     * @return nav
     */
    public function filterPublicNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('References'),
            'uri' => url(array(), 'reference_base'),
        );

        if (get_option('reference_tree_enabled')) {
            $nav[] = array(
                'label' => __('Subjects Tree'),
                'uri' => url(array(), 'reference_tree'),
            );
        }
        return $nav;
    }

    /**
     * Filter for public navigation items.
     *
     * @return nav
     */
    public function filterPublicNavigationItems($nav)
    {
        $types = $this->getReferenceTypes();
        if (isset($types['Element']) && $types['Element'] && count($types) == 1) {
            $label = __('Browse Metadata');
        } elseif (isset($types['ItemType']) && $types['ItemType'] && count($types) == 1) {
            $label = __('Browse Item Types');
        } else {
            $label = __('Browse References');
        }

        $nav['Browse References'] = array(
            'label' => $label,
            'uri' => url(array(), 'reference_base'),
        );

        if (get_option('reference_tree_enabled')) {
            $nav['Hierarchy of Subjects'] = array(
                'label' => __('Hierarchy of Subjects'),
                'uri' => url(array(), 'reference_tree'),
            );
        }

        return $nav;
    }

    /**
     * Shortcode for adding list of references.
     *
     * @param array $args
     * @param Omeka_View $view
     * @return string
     */
    public function shortcodeReference($args, $view)
    {
        $args['view'] = $view;
        if (empty($args['slug'])) {
            return;
        }
        $references = $view->reference()->getList($args['slug']);
        foreach ($args as &$arg) {
            // Set true values.
            if ($arg == 'true') {
                $arg = true;
            }
            // Set false values.
            elseif ($arg == 'false') {
                $arg = false;
            }
        }
        return $view->reference()->displayList($references, $args);
    }

    /**
     * Shortcode for adding tree of subjects.
     *
     * @param array $args
     * @param Omeka_View $view
     * @return string
     */
    public function shortcodeSubjects($args, $view)
    {
        $args['view'] = $view;
        $subjects = $view->reference()->getTree();
        foreach ($args as &$arg) {
            // Set true values.
            if ($arg == 'true') {
                $arg = true;
            }
            // Set false values.
            elseif ($arg == 'false') {
                $arg = false;
            }
        }
        return $view->reference()->displayTree($subjects, $args);
    }
    
    /**
     * Get the list of active reference types (Item Types, Metadata) 
     *
     * @return array or null
     */
    public function getReferenceTypes()
    {
        $slugs = json_decode(get_option('reference_slugs'), true) ?: array();
        if (!empty($slugs)) {
            // Remove disabled slugs.
            foreach ($slugs as $slug => $slugData) {
                if (empty($slugData['active'])) {
                    unset($slugs[$slug]);
                }
            }
        }

        if (!empty($slugs)) {
            $types = array();
            foreach ($slugs as $slug => $slugData) {
                $types[$slugData['type']] = true;
            }
            return $types;
        } else {
            return null;
        }
    }

    public function updatePublicNavigationMain($types)
    {
        if (count($types) == 1 && isset($types['Element']) && $types['Element']) {
            $label = 'Metadata';
        } elseif (count($types) == 1 && isset($types['ItemType']) && $types['ItemType']) {
            $label = 'Item Types';
        } else {
            $label = 'References';
        }

        $navItems = json_decode(get_option('public_navigation_main'), true) ?: array();
        foreach ($navItems AS $key=>$navItem) {
            if ($navItem['uid'] == '/' . self::REFERENCE_PATH_LIST) {
                if ($navItem['label'] != $label) {
                    $navItems[$key]['label'] = $label;
                    $optionValue = json_encode($navItems);
                    set_option('public_navigation_main', $optionValue);
                    return true;
                }
            }
        }
    }
}
