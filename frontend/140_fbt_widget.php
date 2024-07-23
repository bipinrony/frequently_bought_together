<?php

/**
 * Widget file
 *
 * @category   Plugin
 * @package    frequently_bought_together
 * @author     E-Commerce Zentrum
 */

use JTL\Shop;
use Plugin\frequently_bought_together\includes\FbtProductHelper;

$helper = FbtProductHelper::getInstance($oPlugin);
$pluginData = $helper->getPluginKeyValuePairs($oPlugin);

if (Shop::getPageType() == PAGE_ARTIKEL && $pluginData['fbt_enable'] == 'Y' && $pluginData['fbt_show_widget_automatically'] == 'Y') {
    // pq('.tab-navigation')->before($smarty->getTemplateVars('fbt_widget'));
    pq('#tabAccordion')->before($smarty->getTemplateVars('fbt_widget'));
}
