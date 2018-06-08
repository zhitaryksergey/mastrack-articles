<?php

/**
* @copyright Copyright &copy; Gogodigital Srls
* @company Gogodigital Srls - Wide ICT Solutions 
* @website http://www.gogodigital.it
* @github https://github.com/cinghie/yii2-articles
* @license GNU GENERAL PUBLIC LICENSE VERSION 3
* @package yii2-articles
* @version 0.6.5
*/

namespace cinghie\articles\controllers;

use Yii;
use cinghie\articles\models\Attachments;
use cinghie\articles\models\Items;
use cinghie\articles\models\ItemsSearch;
use cinghie\articles\models\Tags;
use cinghie\articles\models\Tagsassign;
use cinghie\articles\models\Translations;
use Imagine\Exception\RuntimeException;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ItemsController implements the CRUD actions for Items model.
 */
class ItemsController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'matchCallback' => function () {
                            return ( Yii::$app->user->can('articles-index-all-items') || Yii::$app->user->can('articles-index-his-items') );
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['articles-create-items'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'matchCallback' => function () {
                            $model = $this->findModel(Yii::$app->request->get('id'));
                            return ( Yii::$app->user->can('articles-update-all-items') || ( Yii::$app->user->can('articles-update-his-items') && $model->isCurrentUserCreator() ) );
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['changestate','activemultiple','deactivemultiple'],
                        'matchCallback' => function () {
                            $model = $this->findModel(Yii::$app->request->get('id'));
                            return ( Yii::$app->user->can('articles-publish-all-items') || ( Yii::$app->user->can('articles-publish-his-items') && $model->isCurrentUserCreator() ) );
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete','deleteimage','deletemultiple'],
                        'matchCallback' => function () {
                            $model = $this->findModel(Yii::$app->request->get('id'));
                            return ( Yii::$app->user->can('articles-delete-all-items') || ( Yii::$app->user->can('articles-delete-his-items') && $model->isCurrentUserCreator() ) );
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'matchCallback' => function () {
                            $model = $this->findModel(Yii::$app->request->get('id'));
                            return ( Yii::$app->user->can('articles-view-items') || $model->access === 'public' );
                        }
                    ],
                ],
                'denyCallback' => function () {
                    throw new \RuntimeException('You are not allowed to access this page');
                }
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'activemultiple' => ['post'],
                    'deactivemultiple' => ['post'],
                    'changestate' => ['post'],
                    'delete' => ['post'],
					'deleteimage' => ['post'],
                    'deletemultiple' => ['post'],
                ],
            ],
        ];
    }

	/**
	 * Lists all Items models
	 *
	 * @return mixed
	 * @throws InvalidParamException
	 * @throws ForbiddenHttpException
	 */
    public function actionIndex()
    {
        $searchModel  = new ItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

	/**
	 * Displays a single Items model
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 * @throws InvalidParamException
	 * @throws NotFoundHttpException
	 */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

	/**
	 * Creates a new Items model
	 *
	 * @return mixed
	 * @throws Exception
	 * @throws InvalidParamException
	 * @throws RuntimeException
	 */
    public function actionCreate()
    {
        $model = new Items();
        $post  = Yii::$app->request->post();

        if ( $model->load($post) )
        {
            // Set modified as actual date
	        $model->modified = date('Y-m-d H:i:s');

	        // If alias is not set, generate it
	        $model->setAlias($post['Items'],'title');

            // Upload Image and Thumb if is not Null
            $imagePath   = Yii::getAlias(Yii::$app->controller->module->itemImagePath);
            $thumbPath   = Yii::getAlias(Yii::$app->controller->module->itemThumbPath);
            $imgNameType = Yii::$app->controller->module->imageNameType;
            $imgOptions  = Yii::$app->controller->module->thumbOptions;
            $imgName     = $model->title;
            $fileField   = 'image';

            // Create UploadFile Instance
            $image = $model->uploadFile($imgName,$imgNameType,$imagePath,$fileField);

            if ($model->save())
            {
            	// Set Attachments
	            $model->attachments = UploadedFile::getInstances($model, 'attachments');

	            if(count($model->attachments))
	            {
		            $attachmentFolder = Yii::getAlias(Yii::$app->controller->module->attachPath);

		            foreach ($model->attachments as $key => $attachment)
		            {
			            $attachmentName = $attachment->baseName;
			            $attachmentExt  = $attachment->extension;
			            $attachmentSize = $attachment->size;
			            $attachmentPath = $attachmentFolder. $attachmentName . '.' . $attachmentExt;

			            if($attachment->saveAs($attachmentPath))
			            {
				            $attach = new Attachments();
				            $attach->item_id = $model->id;
				            $attach->title = $attachmentName;
				            $attach->filename = $attachmentName . '.' . $attachmentExt;
				            $attach->extension = $attachment->extension;
				            $attach->mimetype = $attachment->type;
				            $attach->size = $attachmentSize;
				            $attach->save();
			            }
		            }
	            }

	            // Set Tags
	            $tags = !empty($post['Items']['tags']) ? $post['Items']['tags'] : [];

	            if(count($tags))
	            {
		            foreach ($tags as $tag)
		            {
			            $tag_id = isset(Tags::find()->select(['id'])->where([ 'name' => $tag])->one()->id) ? Tags::find()->select(['id'])->where([ 'name' => $tag])->one()->id : 0;

			            // Tags
			            if(!$tag_id) {
				            $newTag = new Tags();
				            $newTag->name = $tag;
				            $newTag->alias = $model->generateAlias($tag);
				            $newTag->state = 1;
				            $newTag->save();

				            $tag_id = $newTag->id;
			            }

			            $tagsAassign = new Tagsassign();
			            $tagsAassign->item_id = $model->id;
			            $tagsAassign->tag_id = $tag_id;
			            $tagsAassign->save();
		            }
	            }

                // Upload only if valid uploaded file instance found
                if ($image !== false) {
                    // save thumbs to thumbPaths
                    $model->createThumbImages($image,$imagePath,$imgOptions,$thumbPath);
                }

	            // Set Translations
	            foreach(Yii::$app->controller->module->languages as $langTag)
	            {
		            $lang = substr($langTag,0,2);

		            $titleName = 'title_'.$lang;
		            $aliasName = 'alias_'.$lang;
		            $introText = 'introText_'.$lang;
		            $fullText  = 'fullText_'.$lang;

		            $translation = $model->getTranslationsObject($lang);

		            if($post[$titleName] && $translation == null)
		            {
			            // Clone Model
			            $model_lang = new Items();
			            $attributes = $model->attributes;

			            foreach($attributes as  $attribute => $val)
			            {
				            if($attribute !== 'id') {
					            $model_lang->{$attribute} = $val;
				            }
			            }

			            // Set Translations values
			            $model_lang->title = $post[$titleName];
			            $model_lang->alias = $model_lang->generateAlias($post[$titleName]);
			            $model_lang->language = $lang;
			            $model_lang->introtext = $post[$introText];
			            $model_lang->fulltext = $post[$fullText];
			            $model_lang->save();

			            // Set Translation Table
			            $translation = new Translations();
			            $translation->item_id = $model->id;
			            $translation->translation_id = $model_lang->id;
			            $translation->lang = $lang;
			            $translation->lang_tag = $langTag;
			            $translation->save();
		            }
	            }

                // Set Success Message
                Yii::$app->session->setFlash('success', Yii::t('articles', 'Item has been created!'));

                return $this->redirect(['index']);
            }

	        // Set Error Message
	        Yii::$app->session->setFlash('error', Yii::t('articles', 'Item could not be saved!'));

	        return $this->render('create', ['model' => $model]);
        }

	    return $this->render('create', ['model' => $model]);
    }

	/**
	 * Updates an existing Items model
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 * @throws Exception
	 * @throws InvalidParamException
	 * @throws NotFoundHttpException
	 * @throws RuntimeException
	 */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();

        if ( $model->load($post) )
        {
            // Set modified as actual date
            $model->modified = date( 'Y-m-d H:i:s' );

            // Set modified_by User
            $model->modified_by = Yii::$app->user->identity->id;

	        // If alias is not set, generate it
	        $model->setAlias($post['Items'],'title');

            // Upload Image and Thumb if is not Null
            $imagePath   = Yii::getAlias(Yii::$app->controller->module->itemImagePath);
            $thumbPath   = Yii::getAlias(Yii::$app->controller->module->itemThumbPath);
            $imgNameType = Yii::$app->controller->module->imageNameType;
            $imgOptions  = Yii::$app->controller->module->thumbOptions;
            $imgName     = $model->title;
            $fileField   = 'image';

            // Create UploadFile Instance
            $image = $model->uploadFile($imgName, $imgNameType, $imagePath, $fileField);

            // If image is false delete from db
            if($model->image === false && $image === false) {
                unset($model->image);
            }

            if ($model->save())
            {
	            // Set Attachments
	            $model->attachments = UploadedFile::getInstances($model, 'attachments');

	            if(count($model->attachments))
	            {
		            $attachmentFolder = Yii::getAlias(Yii::$app->controller->module->attachPath);

		            foreach ($model->attachments as $key => $attachment)
		            {
			            $attachmentName = $attachment->baseName;
			            $attachmentExt  = $attachment->extension;
			            $attachmentSize = $attachment->size;
			            $attachmentPath = $attachmentFolder. $attachmentName . '.' . $attachmentExt;

			            if($attachment->saveAs($attachmentPath))
			            {
				            $attach = new Attachments();
				            $attach->item_id = $model->id;
				            $attach->title = $attachmentName;
				            $attach->filename = $attachmentName . '.' . $attachmentExt;
				            $attach->extension = $attachment->extension;
				            $attach->mimetype = $attachment->type;
				            $attach->size = $attachmentSize;
				            $attach->save();
			            }
		            }
	            }

                // Set Tags
	            $tags = !empty($post['Items']['tags']) ? $post['Items']['tags'] : [];

	            if(count($tags))
	            {
		            // Tagsassign
		            Tagsassign::deleteAll(['item_id'=>$model->id]);

                    foreach ($tags as $tag)
                    {
	                    $tag_id = isset(Tags::find()->select(['id'])->where([ 'name' => $tag])->one()->id) ? Tags::find()->select(['id'])->where([ 'name' => $tag])->one()->id : 0;

                    	// Tags
	                    if(!$tag_id) {
							$newTag = new Tags();
		                    $newTag->name = $tag;
		                    $newTag->alias = $model->generateAlias($tag);
		                    $newTag->state = 1;
		                    $newTag->save();

		                    $tag_id = $newTag->id;
	                    }

                        $tagsAassign = new Tagsassign();
                        $tagsAassign->item_id = $model->id;
                        $tagsAassign->tag_id = $tag_id;
                        $tagsAassign->save();
                    }
                }

                // upload only if valid uploaded file instance found
                if ($image !== false) {
                    // save thumbs to thumbPaths
                    $model->createThumbImages($image, $imagePath, $imgOptions, $thumbPath);
                }

                // Set Success Message
                Yii::$app->session->setFlash('success', Yii::t('articles', 'Item has been updated!'));

                return $this->redirect(['index']);
            }

	        // Set Error Message
	        Yii::$app->session->setFlash('error', Yii::t('articles', 'Item could not be saved!'));

	        return $this->render('update', ['model' => $model]);
        }

	    return $this->render('update', [
		    'model' => $model,
	    ]);
    }

	/**
	 * Deletes an existing Items model
	 *
	 * @param integer $id
	 *
	 * @throws \Exception
	 * @throws NotFoundHttpException
	 * @throws StaleObjectException
	 * @throws \Throwable
	 */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

	    /** @var Items $model */
	    if ($model->delete()) {
	        Yii::$app->session->setFlash('success', Yii::t('articles', 'Item has been deleted!'));
        }

	    Yii::$app->session->setFlash('error', Yii::t('articles', 'Error deleting image'));
    }

	/**
	 * Deletes selected Items models
	 *
	 * @throws \Exception
	 * @throws NotFoundHttpException
	 * @throws StaleObjectException
	 * @throws \Throwable
	 */
    public function actionDeletemultiple()
    {
        $ids = Yii::$app->request->post('ids');

        if (!$ids) {
            return;
        }

        foreach ($ids as $id)
        {
            $model = $this->findModel($id);

            if ($model->delete()) {
	            Yii::$app->session->setFlash('success', Yii::t('articles', 'Item has been deleted!'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('articles', 'Error deleting image!'));
            }
        }

        // Set Success Message
        Yii::$app->session->setFlash('success', Yii::t('articles', 'Delete Success!'));
    }

	/**
	 * Deletes an existing Items Image
	 *
	 * @param integer $id
	 *
	 * @return bool
	 * @throws InvalidParamException
	 * @throws NotFoundHttpException
	 */
	public function actionDeleteimage($id) 
	{
		$model = $this->findModel($id);

		if ($model->deleteImage()) {
			$model->image = '';
			$model->save();

			// Set Success Message
			Yii::$app->session->setFlash('success', Yii::t('articles', 'The image was removed successfully! Now, you can upload another by clicking Browse in the Image Tab.'));

			return true;
		}

		// Set Error Message
		Yii::$app->session->setFlash('error', Yii::t('articles', 'Error removing image. Please try again later or contact the system admin.'));

		return false;
	}

	/**
	 * Change article state: published or unpublished
	 *
	 * @param $id
	 *
	 * @return Response
	 * @throws NotFoundHttpException
	 */
    public function actionChangestate($id)
    {
        $model = $this->findModel($id);

        if($model->state) {
            $model->deactive();
            Yii::$app->getSession()->setFlash('warning', Yii::t('articles', 'Item unpublished'));
        } else {
            $model->active();
            Yii::$app->getSession()->setFlash('success', Yii::t('articles', 'Item published'));
        }

	    return $this->redirect(['index']);
    }

	/**
	 * Active selected Items models
	 *
	 * @throws ForbiddenHttpException
	 * @throws NotFoundHttpException
	 */
    public function actionActivemultiple()
    {
        $ids = Yii::$app->request->post('ids');

        if (!$ids) {
            return;
        }

        foreach ($ids as $id)
        {
            $model = $this->findModel($id);

            if (!$model->state) {
                $model->active();
                Yii::$app->getSession()->setFlash('success', Yii::t('articles', 'Items unpublished'));
            } else {
                throw new ForbiddenHttpException;
            }
        }
    }

    /**
     * Active selected Items models
     *
     * @throws NotFoundHttpException
     */
    public function actionDeactivemultiple()
    {
        $ids = Yii::$app->request->post('ids');

        if (!$ids) {
            return;
        }

        foreach ($ids as $id)
        {
            $model = $this->findModel($id);

            if($model->state) {
                $model->deactive();
                Yii::$app->getSession()->setFlash('warning', Yii::t('articles', 'Items published'));
            }
        }
    }

    /**
     * Finds the Items model based on its primary key value
     *
     * @param integer $id
     * 
     * @return Items
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Items::findOne($id)) !== null) {
            return $model;
        }

	    throw new NotFoundHttpException('The requested page does not exist.');
    }

	/**
	 * Check article language
	 *
	 * @param $id
	 *
	 * @return bool
	 * @throws NotFoundHttpException
	 */
    protected function checkArticleLanguage($id)
    {
        $model = $this->findModel($id);

	    return Yii::$app->language === $model->getLang() || 'all' === $model->getLangTag();
    }

}
