<?php

namespace Plugin\frequently_bought_together\includes;

/**
 * helper class for doing stuff
 *
 * @category   Plugin
 * @package    frequently_bought_together
 * @author     E-Commerce Zentrum
 * @copyright  2021-2022
 */

use JTL\Shop;

/**
 * FbtProductHelper
 */
class FbtProductHelper
{

    const LIMIT = 10;

    /**
     * @var null|fbtHelper
     */
    private static $_instance = null;

    /**
     * @var null|bool
     */
    private static $_isModern = null;

    /**
     * @var null|NiceDB
     */
    private $db = null;

    /**
     * @var null|Plugin
     */
    private $plugin = null;

    private $kSprache = 1;

    /**
     * order Object
     * @var mix
     */
    var $order = null;

    /**
     * constructor
     *
     * @param Plugin $oPlugin
     */
    public function __construct($oPlugin)
    {
        $this->plugin = $oPlugin;
        $this->db = Shop::Container()->getDB();
        $this->kSprache = (int)$_SESSION['kSprache'];
    }

    /**
     * singleton getter
     *
     * @param $oPlugin
     * @return fbtHelper
     */
    public static function getInstance($oPlugin)
    {
        return (self::$_instance === null) ? new self($oPlugin) : self::$_instance;
    }

    /**
     * @param $kArtkelId
     * @return null
     */
    public function getProductName($kArtkelId)
    {
        $query = "SELECT cName
                      FROM twarenkorbpos
                   WHERE kArtikel={$kArtkelId}";

        $data = $this->db->query($query, 2);
        if ($data && is_array($data)) {
            return $data[0]->cName;
        }
        return null;
    }

    /**
     * @param $id
     * @return array|\Illuminate\Support\Collection|object|PDOStatement|stdClass|null
     */
    public function getBoughtTogetherProductWithId($id)
    {
        $query = "SELECT c.original_SKU, c.bought_with, count(*) as times_bought_together
        FROM (
          SELECT a.kArtikel as original_SKU, b.kArtikel as bought_with
          FROM twarenkorbpos a
          INNER join twarenkorbpos b
          ON a.kWarenkorb = b.kWarenkorb AND a.kArtikel != b.kArtikel AND a.kArtikel>0 AND b.kArtikel>0)  c
          WHERE original_SKU = $id
        GROUP BY c.original_SKU, c.bought_with
        ORDER BY times_bought_together DESC
        LIMIT 2";
        $data = $this->db->query($query, 2);
        if ($data && is_array($data)) {

            return $data;
        }
        return null;
    }

    /**
     * @return array|\Illuminate\Support\Collection|object|PDOStatement|stdClass|null
     */
    public function getBoughtTogetherProduct($page = 0)
    {
        $query = "SELECT c.original_SKU, c.bought_with, count(*) as times_bought_together
        FROM (
          SELECT a.kArtikel as original_SKU, b.kArtikel as bought_with
          FROM twarenkorbpos a
          INNER join twarenkorbpos b
          ON a.kWarenkorb = b.kWarenkorb AND a.kArtikel != b.kArtikel AND a.kArtikel>0 AND b.kArtikel>0)  c
        GROUP BY c.original_SKU, c.bought_with ";

        $query .= ' LIMIT ' . self::LIMIT . ' offset ' . ($page * self::LIMIT);

        $data = $this->db->query($query, 2);
        if ($data && is_array($data)) {

            return $data;
        }
        return null;
    }

    /**
     * Fetches the ordered products
     *
     * @param type $order_id The order id
     *
     * @return array  The products data
     *
     * @access protected
     */
    public function fbtProducts($productIds)
    {
        if (is_array($productIds)) {
            $productIds = implode(',', $productIds);
        }

        $query = "SELECT t.kArtikel, t.kVaterArtikel, t.kEigenschaftKombi, IFNULL(ts.cName,t.cName) as cName, IFNULL(ts.cSeo,t.cSeo) as cSeo, fMindestbestellmenge
                  FROM tartikel t LEFT JOIN tartikelsprache ts ON (t.kArtikel = ts.kArtikel AND ts.kSprache = $this->kSprache) 
                  WHERE t.fLagerbestand > 0 AND t.kArtikel IN ($productIds)";

        $data = $this->db->query($query, 2);
        if ($data && is_array($data)) {
            return $data;
        }

        return [];
    }

    /**
     * @param $productIds
     * @param $productIdentifier
     * @return array
     */
    public function fbtProductDetails($productIds, $productIdentifier)
    {
        $productsArray = [];
        $singleProduct = $this->fbtProducts($productIdentifier);
        $fbtProducts = $this->fbtProducts($productIds);
        $products = [];
        if (!isset($singleProduct[0]) || empty($productIds)) {
            return $productsArray;
        }

        $products[] = $singleProduct[0];
        if (isset($fbtProducts[0]))
            $products[] = $fbtProducts[0];
        if (isset($fbtProducts[1]))
            $products[] = $fbtProducts[1];

        foreach ($products as $key => $product) {
            $productDetail['cSeo'] = $product->cSeo;
            $cSeo = strtolower($product->cSeo);
            $imageUrl = Shop::getURL() . '/' . PFAD_MEDIA_IMAGE . "product/{$product->kArtikel}/md/{$cSeo}.jpg";
            $product->image_url = $imageUrl;
            $price = new \JTL\Catalog\Product\Preise(\JTL\Session\Frontend::getCustomerGroup()->getID(), $product->kArtikel);
            $product->localizedPrices = $price->localizePreise()->cVKLocalized;
            $product->alterVKLocalized = $price->localizePreise()->alterVKLocalized;
            if (isset($product->alterVKLocalized[0])) {
                $product->alterVKLocalized = $product->alterVKLocalized[0];
            }
            $product->teigenschaftkombiwert = $this->getCombinations($product->kEigenschaftKombi);
            $productsArray[] = $product;
        }
        return $productsArray;
    }

    public function getCombinations($kEigenschaftKombi)
    {
        if ($kEigenschaftKombi > 0) {
            $query = "SELECT * FROM `teigenschaftkombiwert` where kEigenschaftKombi = {$kEigenschaftKombi}";
            $data = $this->db->query($query, 2);
            if ($data && is_array($data)) {
                return $data;
            }
        }

        return [];
    }

    /**
     * Gets product identifier.
     *
     * @param array  $product
     * @param string $configuredIdentifier
     *
     * @return int|string
     */
    public function getProductIdentifier($product, $configuredIdentifier = 'kArtikel')
    {
        if ((!is_null($product->kVariKindArtikel))) {
            return $product->kVariKindArtikel;
        } else if (!empty($product->Variationen)) {
            return $product->Variationen[0]->Werte[0]->oVariationsKombi->kArtikel;
        }

        return $product->kArtikel;
    }

    /**
     * @param $oPlugin
     * @return array
     */
    public function getPluginKeyValuePairs($oPlugin): array
    {
        $pluginOptions = [];
        foreach ($oPlugin->getConfig()->getOptions()->toArray() as $pluginConfig) {
            $value = (array)$pluginConfig;
            $pluginOptions[$value['valueID']] = $value['value'];
        }
        return $pluginOptions;
    }

    /**
     * @param $oPlugin
     * @return array
     */
    public function saveClickCount($fbt_article_id): bool
    {
        $query = "INSERT INTO fbt_statistics (article_id, click_count) VALUES({$fbt_article_id}, 1) ON DUPLICATE KEY UPDATE click_count = click_count + 1;";
        $response = $this->db->query($query, 7);
        if ($response && $response > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $oPlugin
     * @return array
     */
    public function getClickCount(): int
    {
        $query = "SELECT SUM(click_count) as total_click_count FROM fbt_statistics";
        $data = $this->db->query($query, 2);
        if (isset($data[0]) && $data[0]->total_click_count > 0) {
            return $data[0]->total_click_count;
        }

        return 0;
    }
}