<?php declare(strict_types=1);
/**
 * helper class for doing stuff
 *
 * @category   Plugin
 * @package    frequently_bought_together
 * @author     E-Commerce Zentrum
 * @copyright  2021-2022
 */

namespace Plugin\frequently_bought_together;

use JTL\Events\Dispatcher;
use JTL\Plugin\Bootstrapper;
use JTL\Shop;
use Exception;
use JTL\Smarty\JTLSmarty;
use Plugin\frequently_bought_together\includes\FbtProductHelper;

/**
 * Class Bootstrap
 * @package Plugin\jtl_test
 */
class Bootstrap extends Bootstrapper
{
    protected const ADMIN_TAB_NAME_STATISTICS = 'Statistiken';

    private $helper;

    private const TEST_CRON_JOB = 'frequently_bought_together_cron';

    private const CONSENT_ITEM_ID = 'frequently_bought_together_consent';

    /**
     * @inheritdoc
     */
    public function boot(Dispatcher $dispatcher)
    {
        parent::boot($dispatcher);
        if (Shop::isFrontend() === false) {
            return;
        }
        $plugin = $this->getPlugin();
    }

    /**
     * @inheritdoc
     */
    public function installed()
    {
        parent::installed();
    }

    /**
     * @inheritdoc
     */
    public function updated($oldVersion, $newVersion)
    {
        parent::updated($oldVersion, $newVersion);
        \error_log('updated from ' . $oldVersion . ' to ' . $newVersion);
    }

    /**
     * @inheritdoc
     */
    public function uninstalled(bool $deleteData = true)
    {
        parent::uninstalled($deleteData);
       // $this->getDB()->delete('tcron', 'jobType', self::TEST_CRON_JOB);
    }


    /**
     * @param string $tabName
     * @param int $menuID
     * @param JTLSmarty $smarty
     * @return string
     */
    public function renderAdminMenuTab(string $tabName, int $menuID, JTLSmarty $smarty): string {
        if($tabName == self::ADMIN_TAB_NAME_STATISTICS) {

            try {
                $page_number = $_GET['page_number'] ?? 0;
                $helper = new FbtProductHelper($this->getPlugin());
                $bt_products = $helper->getBoughtTogetherProduct($page_number);

                $bought_product=array();
                foreach($bt_products as $key=>$product)
                {
                    $bought_product[$key]['original_product'] =  $helper->getProductName($product->original_SKU);
                    $bought_product[$key]['bought_with'] =  $helper->getProductName($product->bought_with);
                    $bought_product[$key]['total'] =  $product->times_bought_together;
                }

                $ajaxUrl = $this->getPlugin()->getPaths()->getShopURL() . 'admin/plugin.php?kPlugin=' . $this->getPlugin()->getID();
                $smarty->assign("ajaxUrl", $ajaxUrl);
                $smarty->assign("stepPlugin", "statistics");
                $smarty->assign("click_count", $helper->getClickCount());
                $smarty->assign("bought_product", $bought_product);

                if(isset($_GET['isStatisticsAjax']) && (int)$_GET['isStatisticsAjax'] === 1 && isset($_GET['page_number'])) {
                    $content = $smarty->fetch($this->getPlugin()->getPaths()->getAdminPath() . 'templates/snippets/statistics_detail.tpl');
                    echo json_encode([
                        'status' => 'success',
                        'data' => ['content' => $content, 'count' => count($bought_product)],
                        'messages' => ''
                    ]);
                    exit();
                } else {
                    return $smarty->fetch($this->getPlugin()->getPaths()->getAdminPath() . 'templates/statistics.tpl');
                }

            } catch (Exception $ex) {
                echo json_encode([
                    'status' => 'error',
                    'data' => '',
                    'messages' => [$ex->getMessage()]
                ]);
                exit();
            }
        }

        return parent::renderAdminMenuTab($tabName, $menuID, $smarty);
    }
}
