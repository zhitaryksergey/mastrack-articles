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

use yii\bootstrap\Nav;

?>

<?= Nav::widget([
	'options' => [
		'class' => 'nav-tabs',
		'style' => 'margin-bottom: 15px',
	],
	'items' => [
		/*[
			'label'   => Yii::t('articles', 'Dashboard'),
			'url'     => ['/articles/default/index'],
		],*/
		[
			'label'   => Yii::t('articles', 'Articles'),
			'url'     => ['/articles/items/index'],
		],
		[
			'label'   => Yii::t('articles', 'Categories'),
			'url'     => ['/articles/categories/index'],
		]/*,
		[
			'label'   => Yii::t('articles', 'Attachments'),
			'url'     => ['/articles/attachments/index'],
		],*/
	],
]) ?>
