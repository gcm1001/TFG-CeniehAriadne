<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Add buttons to filter by date (year) and status fields of AriadnePlus Monitor.
 *
 * @package Omeka\View\Helper
 */
class AriadnePlusMonitor_View_Helper_ItemQuickFilters extends Zend_View_Helper_Abstract
{
    /**
     * Add buttons to filter by date (year) and status fields of AriadnePlus Monitor.
     *
     * @todo Move these in Simple Vocab?
     *
     * @param array $params Optional array of key-value pairs to use instead of
     *  reading the current params from the request.
     * @return string HTML output
     */
    public function itemQuickFilters(array $params = null)
    {
        $html = '';

        $current = json_decode(get_option('ariadneplus_monitor_admin_items_browse'), true) ?: array();
        $statusTermsElements = $this->view->monitor()->getStatusElements(null, null, true);
        $statusNoTermElements = $this->view->monitor()->getStatusElements(null, null, false);

        if (!empty($current['search']['Monitor'])) {
            $terms = array_intersect_key($statusTermsElements, $current['search']['Monitor']);
            $noTerms = array_intersect_key($statusNoTermElements, $current['search']['Monitor']);
            if (!empty($terms) || !empty($noTerms)) {
                $html .= $this->view->partial(
                    'items/ariadne-plus-monitor-quick-search.php',
                    array(
                        'statusTermsElements' => $terms,
                        'statusNoTermElements' => $noTerms,
                ));
            }
        }

        if (!empty($current['filter']['Monitor'])) {
            $terms = array_intersect_key($statusTermsElements, $current['filter']['Monitor']);
            $noTerms = array_intersect_key($statusNoTermElements, $current['filter']['Monitor']);
            if (!empty($terms) || !empty($noTerms)) {
                $html .= $this->view->partial(
                    'items/ariadne-plus-monitor-quick-filters.php',
                    array(
                        'statusTermsElements' => $terms,
                        'statusNoTermElements' => $noTerms,
                ));
            }
        }

        if ($html) {
            $html .= '<script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery("#ariadne-plus-monitor-quick-search-div").hide();
                    jQuery("#ariadne-plus-monitor-quick-search").click(function () {
                        jQuery("#ariadne-plus-monitor-quick-search-div").toggle(400);
                    });
                    jQuery("#ariadne-plus-monitor-quick-filters-div").hide();
                    jQuery("#ariadne-plus-monitor-quick-filters").click(function () {
                        jQuery("#ariadne-plus-monitor-quick-filters-div").toggle(400);
                    });
                });
            </script>';
        }

        return $html;
    }
}
