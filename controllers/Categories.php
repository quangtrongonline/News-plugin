<?php namespace Quangtrong\News\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Quangtrong\News\Models\Categories as Item;
use Quangtrong\News\Models\Posts;
use Db;
use Flash;
use Lang;

class Categories extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\ReorderController::class
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public $requiredPermissions = ['quangtrong.news.categories'];

    public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Quangtrong.News', 'news', 'categories');
    }

    public function onDeactivate()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            foreach ($checkedIds as $itemId) {
                if (!$item = Item::where('hidden', 1)->whereId($itemId)) {
                    continue;
                }

                $item->update(['hidden' => 2]);
            }

            Flash::success(Lang::get('quangtrong.news::lang.flash.deactivate'));
        }

        return $this->listRefresh();
    }

    public function onRemove()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            foreach ($checkedIds as $itemId) {
                if (!$item = Item::whereId($itemId)) {
                    continue;
                }

                $item->delete();

                Posts::where('category_id', $itemId)->update(['category_id' => 0]);
                Db::table('quangtrong_news_relations')->where('categories_id', $itemId)->delete();
            }

            Flash::success(Lang::get('quangtrong.news::lang.flash.remove'));
        }

        return $this->listRefresh();
    }
}
