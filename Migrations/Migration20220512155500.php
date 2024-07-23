<?php declare(strict_types=1);
/**
 * @category   Plugin
 * @package    frequently_bought_together
 * @author     E-Commerce Zentrum
 * @copyright  2021-2022
 */
namespace Plugin\frequently_bought_together\Migrations;

use JTL\Plugin\Migration;
use JTL\Update\IMigration;

class Migration20220512155500 extends Migration implements IMigration
{
    public function up()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS `fbt_statistics` (
                      `id` int(10) NOT NULL AUTO_INCREMENT,
                      `article_id` int(10) NOT NULL,
                      `click_count` int(10) NOT NULL,
                      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                      PRIMARY KEY (`id`),
                      CONSTRAINT article_id_unique UNIQUE (article_id)
                    ) ENGINE=InnoDB COLLATE utf8_unicode_ci");
    }

    public function down()
    {
        $this->execute("DROP TABLE IF EXISTS `fbt_statistics`");
    }
}