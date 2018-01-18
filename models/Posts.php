<?php namespace Quangtrong\News\Models;

use Model;
use BackendAuth;
use Carbon\Carbon;
use Cms\Classes\Page as CmsPage;
use Url;
use Cms\Classes\Theme;

class Posts extends Model
{
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Validation;

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    protected $table = 'quangtrong_news_posts';

    public $rules = [
        'title'    => 'required',
        'slug'     => ['required', 'regex:/^[a-z0-9\/\:_\-\*\[\]\+\?\|]*$/i', 'unique:quangtrong_news_posts'],
        'status'   => 'required|between:1,3|numeric',
        'featured' => 'required|between:1,2|numeric'
    ];

    protected $slugs = [
        'slug' => 'title'
    ];

    public $translatable = [
        'title',
        ['slug', 'index' => true],
        'introductory',
        'content'
    ];

    protected $dates = [
        'published_at',
        'last_send_at'
    ];

    public static $allowedSorting = [
        'title asc',
        'title desc',
        'created_at asc',
        'created_at desc',
        'updated_at asc',
        'updated_at desc',
        'published_at asc',
        'published_at desc'
    ];

    public $belongsTo = [
        'category' => [
            'Quangtrong\News\Models\Categories',
            'order' => 'name'
        ]
    ];

    public $hasMany = [
        'logs' => [
            'Quangtrong\News\Models\Logs',
            'key' => 'news_id'
        ],
        'logs_queued_count' => [
            'Quangtrong\News\Models\Logs',
            'key'   => 'news_id',
            'scope' => 'isQueued',
            'count' => true
        ],
        'logs_send_count' => [
            'Quangtrong\News\Models\Logs',
            'key'   => 'news_id',
            'scope' => 'isSend',
            'count' => true
        ],
        'logs_viewed_count' => [
            'Quangtrong\News\Models\Logs',
            'key'   => 'news_id',
            'scope' => 'isViewed',
            'count' => true
        ],
        'logs_clicked_count' => [
            'Quangtrong\News\Models\Logs',
            'key'   => 'news_id',
            'scope' => 'isClicked',
            'count' => true
        ],
        'logs_failed_count' => [
            'Quangtrong\News\Models\Logs',
            'key'   => 'news_id',
            'scope' => 'isFailed',
            'count' => true
        ]
    ];

    public $preview = null;

    public function getSendAttribute() {
        return $this->last_send_at != null;
    }

    /**
     * Check the ID of category
     */
    public function beforeSave()
    {
        if (!isset($this->category_id) || empty($this->category_id)) {
            $this->category_id = 0;
        }
    }

    /**
     * Keep the original send and last_send_at attribute because they are read only
     */
    public function beforeUpdate()
    {
        if (($lastSend = $this->getOriginal('last_send_at')) != null) {
            $this->last_send_at = $lastSend;
        }
    }

    public function scopeListFrontEnd($query, $options)
    {
        extract(array_merge([
            'page'     => 1,
            'perPage'  => 10,
            'sort'     => 'created_at',
            'search'   => '',
            'categories'   => null,
            'category'   => null,
            'status'   => 1,
            'featured' => 0
        ], $options));

        $searchableFields = [
            'title',
            'slug',
            'introductory',
            'content'
        ];

        if ($status) {
            $query->isPublished();
        }

        if ($featured != 0) {
            $query->isFeatured($featured);
        }

        if (!is_array($sort)) {
            $sort = [$sort];
        }

        foreach ($sort as $_sort) {
            if (in_array($_sort, array_keys(self::$allowedSorting))) {
                $parts = explode(' ', $_sort);

                if (count($parts) < 2) {
                    array_push($parts, 'desc');
                }

                list($sortField, $sortDirection) = $parts;

                $query->orderBy($sortField, $sortDirection);
            }
        }

        $search = trim($search);

        if (strlen($search)) {
            $query->searchWhere($search, $searchableFields);
        }

       /*
         * Categories
         */
        if ($categories !== null) {
            if (!is_array($categories)) $categories = [$categories];
            $query->whereHas('category', function($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }
        /*
         * Category, including children
         */
        if ($category !== null) {
            $category = Categories::find($category);
            $categories = $category->getAllChildrenAndSelf()->lists('id');
            $query->whereHas('category', function($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }
        return $query->paginate($perPage, $page);
    }

    /**
     * Allows filtering for specifc categories
     * @param  Illuminate\Query\Builder  $query      QueryBuilder
     * @param  array                     $categories List of category ids
     * @return Illuminate\Query\Builder              QueryBuilder
     */
    public function scopeFilterCategories($query, $categories)
    {
        return $query->whereHas('categories', function($q) use ($categories) {
            $q->whereIn('id', $categories);
        });
    }

    public function scopeIsPublished($query)
    {
        if (BackendAuth::check()) {
            return $query;
        }

        return $query
            ->where('status', 1)
            ->whereNotNull('published_at')
            ->where('published_at', '<', Carbon::now())
        ;
    }

    public function scopeIsFeatured($query, $value = 1)
    {
        return $query->where('featured', $value);
    }

//
    // Menu helpers
    //

    /**
     * Handler for the pages.menuitem.getTypeInfo event.
     * Returns a menu item type information. The type information is returned as array
     * with the following elements:
     * - references - a list of the item type reference options. The options are returned in the
     *   ["key"] => "title" format for options that don't have sub-options, and in the format
     *   ["key"] => ["title"=>"Option title", "items"=>[...]] for options that have sub-options. Optional,
     *   required only if the menu item type requires references.
     * - nesting - Boolean value indicating whether the item type supports nested items. Optional,
     *   false if omitted.
     * - dynamicItems - Boolean value indicating whether the item type could generate new menu items.
     *   Optional, false if omitted.
     * - cmsPages - a list of CMS pages (objects of the Cms\Classes\Page class), if the item type requires a CMS page reference to
     *   resolve the item URL.
     * @param string $type Specifies the menu item type
     * @return array Returns an array
     */
    public static function getMenuTypeInfo($type)
    {
        $result = [];

        if ($type == 'post-page') {

            $references = [];
            $posts = self::orderBy('title')->get();
            foreach ($posts as $post) {
                $references[$post->id] = $post->title;
            }

            $result = [
                'references'   => $references,
                'nesting'      => false,
                'dynamicItems' => false
            ];
        }

        if ($type == 'post-list') {
            $result = [
                'dynamicItems' => true
            ];
        }

        if ($result) {
            $theme = Theme::getActiveTheme();

            $pages = CmsPage::listInTheme($theme, true);
            $cmsPages = [];
            foreach ($pages as $page) {
                if (!$page->hasComponent('newsPost')) {
                    continue;
                }
                $cmsPages[] = $page;
            }

            $result['cmsPages'] = $cmsPages;
        }

        return $result;
    }

    /**
     * Handler for the pages.menuitem.resolveItem event.
     * Returns information about a menu item. The result is an array
     * with the following keys:
     * - url - the menu item URL. Not required for menu item types that return all available records.
     *   The URL should be returned relative to the website root and include the subdirectory, if any.
     *   Use the Url::to() helper to generate the URLs.
     * - isActive - determines whether the menu item is active. Not required for menu item types that
     *   return all available records.
     * - items - an array of arrays with the same keys (url, isActive, items) + the title key.
     *   The items array should be added only if the $item's $nesting property value is TRUE.
     * @param \RainLab\Pages\Classes\MenuItem $item Specifies the menu item.
     * @param \Cms\Classes\Theme $theme Specifies the current theme.
     * @param string $url Specifies the current page URL, normalized, in lower case
     * The URL is specified relative to the website root, it includes the subdirectory name, if any.
     * @return mixed Returns an array. Returns null if the item cannot be resolved.
     */
    public static function resolveMenuItem($item, $url, $theme)
    {
        $result = null;

        if ($item->type == 'post-page') {
            if (!$item->reference || !$item->cmsPage)
                return;

            $category = self::find($item->reference);
            if (!$category)
                return;

            $pageUrl = self::getPostPageUrl($item->cmsPage, $category, $theme);
            if (!$pageUrl)
                return;

            $pageUrl = Url::to($pageUrl);

            $result = [];
            $result['url'] = $pageUrl;
            $result['isActive'] = $pageUrl == $url;
            $result['mtime'] = $category->updated_at;
        }
        elseif ($item->type == 'post-list') {
            $result = [
                'items' => []
            ];

            $posts = self::isPublished()
            ->orderBy('title')
            ->get();

            foreach ($posts as $post) {
                $postItem = [
                    'title' => $post->title,
                    'url'   => self::getPostPageUrl($item->cmsPage, $post, $theme),
                    'mtime' => $post->updated_at,
                ];

                $postItem['isActive'] = $postItem['url'] == $url;

                $result['items'][] = $postItem;
            }
        }

        return $result;
    }

    /**
     * Returns URL of a post page.
     *
     * @param $pageCode
     * @param $category
     * @param $theme
     */
    protected static function getPostPageUrl($pageCode, $category, $theme)
    {
        $page = CmsPage::loadCached($theme, $pageCode);
        if (!$page) return;

        $properties = $page->getComponentProperties('newsPost');
        if (!isset($properties['slug'])) {
            return;
        }

        /*
         * Extract the routing parameter name from the category filter
         * eg: {{ :someRouteParam }}
         */
        if (!preg_match('/^\{\{([^\}]+)\}\}$/', $properties['slug'], $matches)) {
            return;
        }

        $paramName = substr(trim($matches[1]), 1);
        $params = [
            $paramName => $category->slug
        ];
        $url = CmsPage::url($page->getBaseFileName(), $params);

        return $url;
    }
}
