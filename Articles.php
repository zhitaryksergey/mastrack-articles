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

namespace MahmudS\articles;

use Yii;

class Articles extends \yii\base\Module
{

    public $controllerNamespace = 'MahmudS\articles\controllers';

		// Select User Class
	public $userClass         = 'dektrium\user\models\User';

	// Select Article Languages
	public $languages         = [ "en-GB" => "en-GB" ];

    // Select Date Format
    public $dateFormat        = "d F Y";

	// Select Editor: no-editor, ckeditor, imperavi, tinymce, markdown
	public $editor 	          = "imperavi";

	// Select Path To Upload Category Image
	public $categoryImagePath = "@webroot/images/articles/categories/";
    public $categoryImagePath_ecommerce = "images/articles/categories/";

	// Select URL To Upload Category Image
    public $categoryImageURL  = "@web/images/articles/categories/";

	// Select Path To Upload Category Thumb
	public $categoryThumbPath = "@webroot/images/articles/categories/thumb/";
	public $categoryThumbPath_ecommerce = "images/articles/categories/thumb/";

	// Select URL To Upload Category Image
	public $categoryThumbURL  = "@web/images/articles/categories/thumb/";

	// Select Path To Upload Item Image
	public $itemImagePath  	  = "@webroot/images/articles/items/";
	public $itemImagePath_ecommerce = "images/articles/items/";

	// Select URL To Upload Item Image
	public $itemImageURL  	  = "@web/images/articles/items/";

	// Select Path To Upload Item Thumb
	public $itemThumbPath     = "@webroot/images/articles/items/thumb/";
	public $itemThumbPath_ecommerce = "images/articles/items/thumb/";

	// Select URL To Upload Item Thumb
	public $itemThumbURL      = "@web/images/articles/items/thumb/";

	// Select Image Name: categoryname, original, casual
	public $imageNameType 	  = "categoryname";

	// Select Image Types allowed
	public $imageType    	  = "image/jpg,image/jpeg,image/gif,image/png";

	// Select Path To Upload Attachments
	public $attachPath        = "@webroot/attachments/";
	public $attachPath_ecommerce = "/attachments/";

	// Select URL To Upload Attachment
	public $attachURL         = "@web/attachments/";

	// Select Attachment Types allowed
	public $attachType    	  = "application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, .csv, .pdf, text/plain, .jpg, .jpeg, .gif, .png";

	// Show Titles in the views
	public $showTitles        = true;

	// Thumbnails Options
	public $thumbOptions =	[
		'small'  => ['quality' => 100, 'width' => 600, 'height' => 450],
	];

	// Url Rules
	public $urlRules = [
		'<id:\d+>/<alias:[A-Za-z0-9 -_.]+>' => 'articles/categories/view',
		'<cat>/<id:\d+>/<alias:[A-Za-z0-9 -_.]+>' => 'articles/items/view',
	];

	/**
	 * @inheritdoc
	 */

    public function init()
    {
        parent::init();
		$this->registerTranslations();
        $this->setupImageDirectory();
        Yii::$container->set($this->userClass);
    }

	/**
	 * Translating module message
	 */
	public function registerTranslations()
    {
		if (!isset(Yii::$app->i18n->translations['articles*'])) 
		{
			Yii::$app->i18n->translations['articles*'] = [
				'class' => 'yii\i18n\PhpMessageSource',
				'basePath' => __DIR__ . '/messages',
			];
		}
    }

	/**
	 * Setup image directory
	 */
    protected function setupImageDirectory()
    {
        // create image directory as described if it's not exist yet
        if(!file_exists(Yii::getAlias($this->categoryImagePath)))
        {
            mkdir(Yii::getAlias($this->categoryImagePath), 0755, true);
        }

        if(!file_exists(Yii::getAlias($this->categoryThumbPath)))
        {
            mkdir(Yii::getAlias($this->categoryThumbPath), 0755, true);
        }
        foreach ($this->thumbOptions as $thumb => $array)
        {
            if(!file_exists(Yii::getAlias($this->categoryThumbPath . $thumb . '/')))
            {
                mkdir(Yii::getAlias($this->categoryThumbPath . $thumb . '/'), 0755, true);
            }
        }
        if(!file_exists(Yii::getAlias($this->itemImagePath)))
        {
            mkdir(Yii::getAlias($this->itemImagePath), 0755, true);
        }
        if(!file_exists(Yii::getAlias($this->itemThumbPath)))
        {
            mkdir(Yii::getAlias($this->itemThumbPath), 0755, true);
        }
        foreach ($this->thumbOptions as $thumb => $array)
        {
            if(!file_exists(Yii::getAlias($this->itemThumbPath . $thumb . '/')))
            {
                mkdir(Yii::getAlias($this->itemThumbPath . $thumb . '/'), 0755, true);
            }
        }
        if(!file_exists(Yii::getAlias($this->attachPath)))
        {
            mkdir(Yii::getAlias($this->attachPath), 0755, true);
        }
    }
}
