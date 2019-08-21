<?php

/**
* @copyright Copyright &copy; Gogodigital Srls
* @company Gogodigital Srls - Wide ICT Solutions 
* @website http://www.gogodigital.it
* @github https://github.com/cinghie/yii2-articles
* @license GNU GENERAL PUBLIC LICENSE VERSION 3
* @package yii2-articles
* @version 0.6.3
*/

namespace cinghie\articles\models;

use Yii;

class Categories extends Articles
{
    /**
     * @inheritdoc
     */

    public static function tableName() {
        return '{{%article_categories}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {	
        return [
            [['name', 'language'], 'required'],
			[['parentid', 'state', 'ordering'], 'integer'],
			[['name', 'alias', 'image_caption', 'image_credits'], 'string', 'max' => 255],
            [['description', 'image', 'params', 'metadesc', 'metakey'], 'string'],
            [['access'], 'string', 'max' => 64],
			[['author', 'copyright'], 'string', 'max' => 50],
			[['language'], 'string', 'max' => 7],
            [['theme'], 'string', 'max' => 12],
			[['robots'], 'string', 'max' => 20],
			[['image'], 'file', 'extensions' => Yii::$app->controller->module->imageType,],
			[['image'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('articles', 'ID'),
            'name' => Yii::t('articles', 'Name'),
            'alias' => Yii::t('articles', 'Alias'),
            'description' => Yii::t('articles', 'Description'),
            'parentid' => Yii::t('articles', 'Parent'),
            'state' => Yii::t('articles', 'State'),
            'access' => Yii::t('articles', 'Access'),
            'theme' => Yii::t('articles', 'Theme'),
            'ordering' => Yii::t('articles', 'Ordering'),
            'image' => Yii::t('articles', 'Image'),
            'image_caption' => Yii::t('articles', 'Image Caption'),
            'image_credits' => Yii::t('articles', 'Image Credits'),
            'params' => Yii::t('articles', 'Params'),
            'metadesc' => Yii::t('articles', 'Metadesc'),
            'metakey' => Yii::t('articles', 'Metakey'),
            'robots' => Yii::t('articles', 'Robots'),
            'author' => Yii::t('articles', 'Author'),
            'copyright' => Yii::t('articles', 'Copyright'),
            'language' => Yii::t('articles', 'Language'),
        ];
    }

    /**
     * Return Parent Category
     * @return Categories
     */
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parentid'])->from(self::tableName() . ' AS parent');
    }

    /**
     * Return Parent Name
     * @return string
     */
	public function getParentName()
	{
        $model = $this->parent;
        return $model?$model->name:'';
    }

    /**
     * Return all Categories by parent Category
     * @return Categories
     */
    public function getCategories()
    {
        return $this->hasMany(Categories::className(), ['parentid' => 'id']);
    }

    /**
     * Return all Items by Category
     * @return Items
     */
    public function getArticleItems()
    {
        return $this->hasMany(Items::className(), ['catid' => 'id']);
    }
	
	/**
     * fetch stored file name with complete path 
     * @return string
     */
    public function getFilePath() 
    {
        return isset($this->image) ? Yii::getAlias(Yii::$app->controller->module->categoryImagePath).$this->image : null;
    }
	
	/**
     * fetch stored file url
     * @return string
     */
    public function getImageUrl()
    {
        $image   = Yii::$app->controller->module->categoryImagePath_ecommerce.$this->image;
        return Yii::$app->s3bucketService->getPresignedUrl($image, '+2 hours');
    }

    /**
     * fetch stored image url
     * @param $size
     * @return string
     */
    public function getImageThumbUrl($size = 'small')
    {
        $image   = Yii::$app->controller->module->categoryThumbPath_ecommerce.$size.'/'.$this->image;
        return Yii::$app->s3bucketService->getPresignedUrl($image, '+2 hours');
    }
	
	/**
    * Delete Image
    * @return mixed the uploaded image instance
    */
	public function deleteImage() 
	{
		// check if image exists on server
        if (empty($this->image)/* || !file_exists($image)*/) {
            return false;
        }
		
        $image   = Yii::$app->controller->module->categoryImagePath_ecommerce.$this->image;
        $imageS  = Yii::$app->controller->module->categoryThumbPath_ecommerce."small/".$this->image;

        //$s3 = Yii::$app->get('s3bucketService');
        //$s3->delete($image);
        //$s3->delete($imageS);

        //Yii::$app->s3bucketService->delete($image);
        //Yii::$app->s3bucketService->delete($imageS);
        // remared because of 403 error

        return true;

	}

    /**
     * Get Items by Category ID
     * @param integer $catid
     * @param string $order
     * @return Items
     */
    public function getItemsByCategory($catid,$order = 'title')
    {
        $items = Items::find()
            ->where(['catid' => $catid])
            ->andWhere(['state' => 1])
            ->andWhere(['or',['language' => 'All'],['SUBSTRING(language,1,2)' => Yii::$app->language]])
            ->orderBy($order)
            ->all();

        return $items;
    }

    /**
     * Return array for Category Select2
     * @return array
     */
	public function getCategoriesSelect2()
	{
        $categories = Categories::find()
            ->orderBy('name')
            ->all();

		$array[0] = Yii::t('articles', 'No Parent'); 
		
		foreach($categories as $category)
		{
			$array[$category['id']] = $category['name'];
		}
		
		return $array;
	}

    /**
     * Return array with Categories Themes
     * @return array
     */
    public function getThemesSelect2()
    {
        $array = ['blog' => 'blog','portfolio' => 'portfolio'];

        return $array;
    }
	
}
