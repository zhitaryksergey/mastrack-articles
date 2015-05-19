<?php

/**
* @copyright Copyright &copy; Gogodigital Srls
* @company Gogodigital Srls - Wide ICT Solutions 
* @website http://www.gogodigital.it
* @github https://github.com/cinghie/yii2-articles
* @license GNU GENERAL PUBLIC LICENSE VERSION 3
* @package yii2-articles
* @version 1.0
*/

namespace cinghie\articles\controllers;

use Yii;
use cinghie\articles\models\Items;
use cinghie\articles\models\ItemsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\imagine\Image;

/**
 * ItemsController implements the CRUD actions for Items model.
 */
class ItemsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
					'deleteImage' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Items models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Items model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Items model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Items();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			// Upload Image and Thumb if is not Null
			$imagePath   = Yii::getAlias('@webroot')."/".Yii::$app->controller->module->itemImagePath;
			$thumbPath   = Yii::getAlias('@webroot')."/".Yii::$app->controller->module->itemThumbPath;
			$imgNameType = Yii::$app->controller->module->imageNameType;
			$imgOptions  = Yii::$app->controller->module->thumbOptions;
			$imgName     = $model->title;
			
			$file = \yii\web\UploadedFile::getInstance($model, 'image');
			
			// If is set an image, upload it
			if ($file->name != "")
			{ 
				$filename = $this->uploadImage($file,$imagePath,$thumbPath,$imgName,$imgNameType,$imgOptions);
				$model->image = $filename;	
			}
			
			// If alias is not set, generate it
			if ($_POST['Items']['alias']=="") 
			{
				$model->alias = $this->generateAlias($model->title,"url");
			}
			
			// Save changes
			$model->save();	
				
			Yii::$app->session->setFlash('success', \Yii::t('articles.message', 'Item has been saved!'));
			
            return $this->redirect([
				'view', 'id' => $model->id
			]);
			
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Items model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ( $model->load(Yii::$app->request->post()) ) {
			
			// Set Modified as actual date 
			$model->modified = date("Y-m-d H:i:s");
			
			// Save changes
			$model->save();	
			
            return $this->redirect(['view', 'id' => $model->id]);
			
        } else {
			
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Items model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Items model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Items the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Items::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	// Upload Image in a select Folder
	protected function uploadImage($file,$imagePath,$thumbPath,$imgName,$imgNameType,$imgOptions)
	{
		$type = $file->type;
		$type = str_replace("image/","",$type);
		$size = $file->size;
		
		switch($imgNameType) 
		{
			case "original":
				$name = str_replace(" ","_",$file->name);
				break;
			
			case "casual":
				$name = uniqid(rand(), true).".".$type;
				break;
			
			default:
				$name = str_replace(" ","_",$imgName).".".$type;
				break;
		}
		
		// Save the file in the Image Folder
		$path = $imagePath.$name;
		$file->saveAs($path);
		
		// Save Image Thumbs
		Image::thumbnail($imagePath.$name, $imgOptions['small']['width'], $imgOptions['small']['height'])->save($thumbPath."small/".$name, ['quality' => $imgOptions['small']['quality']]);
		Image::thumbnail($imagePath.$name, $imgOptions['medium']['width'], $imgOptions['medium']['height'])->save($thumbPath."medium/".$name, ['quality' => $imgOptions['medium']['quality']]);
		Image::thumbnail($imagePath.$name, $imgOptions['large']['width'], $imgOptions['large']['height'])->save($thumbPath."large/".$name, ['quality' => $imgOptions['large']['quality']]);
		Image::thumbnail($imagePath.$name, $imgOptions['extra']['width'], $imgOptions['extra']['height'])->save($thumbPath."extra/".$name, ['quality' => $imgOptions['extra']['quality']]);			
		
		return $name;
	}
	
	// Generate URL or IMG alias
	protected function generateAlias($name,$type)
    {
        // remove any '-' from the string they will be used as concatonater
        $str = str_replace('-', ' ', $name);
        $str = str_replace('_', ' ', $name);
		
        // remove any duplicate whitespace, and ensure all characters are alphanumeric
		$str = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $str);

        // lowercase and trim
        $str = trim(strtolower($str));
		
        return $str;
    }
	
	// Generate JSON for Params
	protected function generateJsonParams($params)
	{
		return json_encode($params);
	}
}