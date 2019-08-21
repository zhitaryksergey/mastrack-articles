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

use yii\helpers\Html;
use mahmuds\articles\assets\ArticlesAsset;

// Load Articles Assets
ArticlesAsset::register($this);
$asset = $this->assetBundles['mahmuds\articles\assets\ArticlesAsset'];

// Set Title and Breadcrumbs
$this->title = Html::encode($model->name);
$this->params['breadcrumbs'][] = $this->title;

/* Render MetaData */
$this->render('@vendor/mahmuds/mastrack-articles/views/default/_meta_data.php',[ 'model' => $model,]);
$this->render('@vendor/mahmuds/mastrack-articles/views/default/_meta_facebook.php',[ 'model' => $model,]);
$this->render('@vendor/mahmuds/mastrack-articles/views/default/_meta_twitter.php',[ 'model' => $model,]);

// Decode Params
$params = json_decode($model->params);

// Render Theme
$themePath = 'themes/'.$model->theme;
echo($this->render($themePath,['model'=>$model,'params'=>$params]));

?>
