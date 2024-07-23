<?php
/**
 * backend tab to clear cache
 *
 * @category   plugin
 * @package    frequently_bought_together
 * @author     E-Commerce Zentrum
 */


global $smarty, $oPlugin;

$error   = null;
$success = null;

$smarty->assign("stepPlugin", "support");

$smarty->display($oPlugin->getPaths()->getAdminPath() . 'templates/support.tpl');