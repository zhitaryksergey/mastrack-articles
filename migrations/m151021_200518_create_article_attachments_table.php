<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions 
 * @website http://www.gogodigital.it
 * @github https://github.com/mahmuds/mastrack-articles
 * @license GNU GENERAL PUBLIC LICENSE VERSION 3
 * @package mastrack-articles
 * @version 0.6.3
 */

use mahmuds\articles\migrations\Migration;
use yii\db\Schema;

class m151021_200518_create_article_attachments_table extends Migration
{

    public function init()
    {
        $this->db = 'ecommercedb';
        parent::init();
    }

    public function up()
    {
        $this->createTable('{{%article_attachments}}', [
            'id' => $this->primaryKey(),
            'itemid' => $this->integer(11)->notNull(),
            'filename' => $this->string(255)->notNull(),
            'title' => $this->string(255)->notNull(),
            'titleAttribute' => $this->text(),
            'hits' => $this->integer(11)->notNull()->defaultValue(0),
        ], $this->tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%article_attachments}}');
    }

}
