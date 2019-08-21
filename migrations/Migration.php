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

namespace mahmuds\articles\migrations;

use Yii;

class Migration extends \yii\db\Migration
{

    /**
     * @var string
     */
    protected $tableOptions;
	
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
		switch (Yii::$app->db->driverName) 
		{
            case 'mysql':
                $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
                break;
            case 'pgsql':
                $this->tableOptions = null;
                break;
            default:
                throw new \RuntimeException('Your database is not supported!');
        }
    }

}