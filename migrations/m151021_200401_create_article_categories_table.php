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

class m151021_200401_create_article_categories_table extends Migration
{

    public function init()
    {
        $this->db = 'ecommercedb';
        parent::init();
    }

    public function up()
    {
        $this->createTable('{{%article_categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'alias' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'parentid' => $this->integer(11)->notNull()->defaultValue(0),
            'state' => $this->boolean()->notNull()->defaultValue(0),
            'access' => $this->string(64)->notNull(),
            'language' => $this->char(7)->notNull(),
            'theme' => $this->string(12)->notNull()->defaultValue('blog'),
            'ordering' => $this->integer(11)->notNull()->defaultValue(0),
            'image' => $this->text(),
            'image_caption' => $this->string(255),
            'image_credits' => $this->string(255),
            'params' => $this->text(),
            'metadesc' => $this->text(),
            'metakey' => $this->text(),
            'robots' => $this->string(20),
            'author' => $this->string(50),
            'copyright' => $this->string(50),
        ], $this->tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%article_categories}}');
    }
    
    public function safeUp()
    {
	    $now = new \yii\db\Expression('now()');

        $this->batchInsert(
            '{{%article_categories}}',
             ["id", "name", "alias", "description", "parent_id", "state", "access", "language", "theme", "ordering", "image", "image_caption", "image_credits", "params", "metadesc", "metakey", "robots", "author", "copyright"],
             [
                 [
                     'id' => '1',
                     'name' => 'News',
                     'alias' => 'news',
                     'description' => '',
                     'parent_id' => null,
                     'state' => '1',
                     'access' => 'public',
                     'language' => 'all',
                     'theme' => 'blog',
                     'ordering' => '1',
                     'image' => '',
                     'image_caption' => '',
                     'image_credits' => '',
                     'params' => '{"categoriesImageWidth":"small","categoriesIntroText":"No","categoriesFullText":"No","categoriesCreatedData":"No","categoriesModifiedData":"No","categoriesUser":"No","categoriesHits":"No","categoriesDebug":"No","categoryImageWidth":"medium","categoryIntroText":"Yes","categoryFullText":"No","categoryCreatedData":"Yes","categoryModifiedData":"No","categoryUser":"Yes","categoryHits":"Yes","categoryDebug":"No","itemImageWidth":"small","itemIntroText":"No","itemFullText":"No","itemCreatedData":"No","itemModifiedData":"No","itemUser":"No","itemHits":"No","itemDebug":"No"}',
                     'metadesc' => '',
                     'metakey' => '',
                     'robots' => 'index, follow',
                     'author' => '',
                     'copyright' => '',
                 ]
             ]
        );
    }
    
    public function safeDown()
    {
        $this->truncateTable('{{%article_categories}}');
    }

}
