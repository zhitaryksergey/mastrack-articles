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

// Set Title and Breadcrumbs
$this->title = Yii::t('articles', 'Update Items') . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('articles', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Render Yii2-Articles Menu
echo Yii::$app->view->renderFile('@vendor/mahmuds/mastrack-articles/views/default/_menu.php');

?>
<div class="items-update">

    <?php if(Yii::$app->getModule('articles')->showTitles): ?>
        <div class="page-header">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    <?php endif ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
