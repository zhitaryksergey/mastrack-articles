<?php

/**
* @copyright Copyright &copy; Gogodigital Srls
* @company Gogodigital Srls - Wide ICT Solutions 
* @website http://www.gogodigital.it
* @github https://github.com/MahmudS/mastrack-articles
* @license GNU GENERAL PUBLIC LICENSE VERSION 3
* @package mastrack-articles
* @version 0.6.3
*/

namespace MahmudS\articles\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\helpers\Url;

class DefaultController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['index'], 'roles' => ['@']],
                ],
                'denyCallback' => function () {
                    throw new \Exception('You are not allowed to access this page');
                }
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->redirect(Url::to('/articles/items/index'));
        //return $this->render('index');
    }

}
