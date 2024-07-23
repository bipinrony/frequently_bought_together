<?php

/**
 * Widget file
 *
 * @category   Plugin
 * @package    frequently_bought_together
 * @author     E-Commerce Zentrum
 */

use Plugin\frequently_bought_together\includes\FbtProductHelper;

$helper = FbtProductHelper::getInstance($oPlugin);
$pluginData = $helper->getPluginKeyValuePairs($oPlugin);
if ($pluginData['fbt_enable'] == 'Y') {
    $productIdentifier = $helper->getProductIdentifier($args_arr['oArtikel']);
    $productIds = [];
    $fbtProductIds = $helper->getBoughtTogetherProductWithId($productIdentifier);
    if ($fbtProductIds && is_array($fbtProductIds)) {
        $productIds = [$fbtProductIds[0]->bought_with];
        if (isset($fbtProductIds[1])) {
            $productIds[] = $fbtProductIds[1]->bought_with;
        } else if (!empty($pluginData['fbt_default_product_1'])) {
            $productIds[] = $pluginData['fbt_default_product_1'];
        }
    }

    if (empty($productIds) && $pluginData['fbt_show_only_if_products'] == 'N') {
        if (!empty($pluginData['fbt_default_product_1'])) {
            $productIds[] = $pluginData['fbt_default_product_1'];
        }

        if (!empty($pluginData['fbt_default_product_2'])) {
            $productIds[] = $pluginData['fbt_default_product_2'];
        }
    }

    $fbtProducts = $helper->fbtProductDetails($productIds, $productIdentifier);
    if (!empty($fbtProducts)) {
        $smarty->assign('fbt_article_id', $productIdentifier);
        $smarty->assign('fbtProducts', $fbtProducts);
        $smarty->assign('pluginData', $pluginData);
        $smarty->assign('fbt_jtl_token', \JTL\Session\Backend::get('jtl_token'));
        $smarty->assign('fbt_widget', $smarty->fetch(__DIR__ . '/template/fbt_widget.tpl'));
    } else {
        $smarty->assign('fbt_widget', '');
    }
} else {
    $smarty->assign('fbt_widget', '');
}