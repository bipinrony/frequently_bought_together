<?php
/**
 * backend tab to clear cache
 *
 * @category   Plugin
 * @package    frequently_bought_together
 * @author     E-Commerce Zentrum
 */

global $smarty, $oPlugin;

$error   = null;
$success = null;

$smarty->assign("stepPlugin", "clear_cache");

$smarty->display($oPlugin->getPaths()->getAdminPath() . 'templates/clear_cache.tpl');