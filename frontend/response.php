<?php
/**
 * @category   Plugin
 * @package    frequently_bought_together
 * @author     E-Commerce Zentrum
 */
use Plugin\frequently_bought_together\includes\FbtProductHelper;

$helper = FbtProductHelper::getInstance($oPlugin);
$pluginData = $helper->getPluginKeyValuePairs($oPlugin);

if ($pluginData['fbt_enable'] == 'Y') {
    if(empty($_POST['fbt_article_id'])){
        echo json_encode(['status' => 202, 'message' => 'Product ID is missing :)']);
    }
    if($helper->saveClickCount($_POST['fbt_article_id']))
        echo json_encode(['status' => 200, 'message' => 'Record updated successfully. :)']);
    else {
        echo json_encode(['status' => 500, 'message' => 'Something went wrong.']);
    }
} else{
    echo json_encode(['status' => 200, 'message' => 'Plugin is not enabled!']);
}
exit();