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

namespace mahmuds\articles\widgets;

use mahmuds\articles\models\Categories;
use mahmuds\articles\widgets\ItemWidget;
use yii\bootstrap\Widget;

class CategoryWidget extends Widget
{
    public $id;
    public $classes;

    public function init()
    {
        parent::init();

        /*
         * Set id to 1, if not set in widget
         */
        if(!$this->id) {
            $this->id = 1;
        }
    }

    public function run()
    {
        $items = Categories::getItemsByCategory($this->id);

        echo '<div class="row">';

        foreach($items as $item)
        {
            echo ItemWidget::widget([
                'id' => $item['id'],
                'classes' => $this->classes,
                'orderby' => 'title'
            ]);
        }

        echo '</div>';
    }
}
