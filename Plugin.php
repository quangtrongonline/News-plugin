<?php namespace Quangtrong\News;

use System\Classes\PluginBase;
use Backend;
use Event;
use Quangtrong\News\Models\Posts;
use Quangtrong\News\Models\Categories;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'quangtrong.news::lang.plugin.name',
            'description' => 'quangtrong.news::lang.plugin.description',
            'author'      => 'quangtrong.news::lang.plugin.author',
            'icon'        => 'icon-newspaper-o',
            'homepage'    => 'https://github.com/gergo85/oc-news'
        ];
    }

    public function registerNavigation()
    {
        return [
            'news' => [
                'label'       => 'quangtrong.news::lang.menu.news',
                'url'         => Backend::url('quangtrong/news/posts'),
                'icon'        => 'icon-newspaper-o',
                'iconSvg'     => 'plugins/quangtrong/news/assets/images/news-icon.svg',
                'permissions' => ['quangtrong.news.*'],
                'order'       => 201,

                'sideMenu' => [
                    'posts' => [
                        'label'       => 'quangtrong.news::lang.menu.posts',
                        'url'         => Backend::url('quangtrong/news/posts'),
                        'icon'        => 'icon-file-text',
                        'permissions' => ['quangtrong.news.posts']
                    ],
                    'categories' => [
                        'label'       => 'quangtrong.news::lang.menu.categories',
                        'url'         => Backend::url('quangtrong/news/categories'),
                        'icon'        => 'icon-tags',
                        'permissions' => ['quangtrong.news.categories']
                    ],
                    'subscribers' => [
                        'label'        => 'quangtrong.news::lang.menu.subscribers',
                        'url'         => Backend::url('quangtrong/news/subscribers'),
                        'icon'        => 'icon-user',
                        'permissions' => ['quangtrong.news.subscribers']
                    ],
                    'statistics' => [
                        'label'       => 'quangtrong.news::lang.menu.statistics',
                        'url'         => Backend::url('quangtrong/news/statistics'),
                        'icon'        => 'icon-area-chart',
                        'permissions' => ['quangtrong.news.statistics']
                    ],
                    'logs' => [
                        'label'       => 'quangtrong.news::lang.menu.logs',
                        'url'         => Backend::url('quangtrong/news/logs'),
                        'icon'        => 'icon-bar-chart',
                        'permissions' => ['quangtrong.news.logs']
                    ],
                    'settings' => [
                        'label'       => 'quangtrong.news::lang.menu.settings',
                        'url'         => Backend::url('system/settings/update/quangtrong/news/settings'),
                        'icon'        => 'icon-cogs',
                        'permissions' => ['quangtrong.news.settings']
                    ]
                ]
            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'quangtrong.news::lang.plugin.name',
                'description' => 'quangtrong.news::lang.backend_settings.description',
                'category'    => 'system::lang.system.categories.cms',
                'icon'        => 'icon-newspaper-o',
                'class'       => 'Quangtrong\News\Models\Settings',
                'order'       => 500,
                'keywords'    => 'news newsletter email statistics',
                'permissions' => ['quangtrong.news.settings']
            ]
        ];
    }

    public function registerReportWidgets()
    {
        return [
            'Quangtrong\News\ReportWidgets\Posts' => [
                'label'   => 'quangtrong.news::lang.widget.posts',
                'context' => 'dashboard'
            ],
            'Quangtrong\News\ReportWidgets\NewPosts' => [
                'label'   => 'quangtrong.news::lang.widget.newposts',
                'context' => 'dashboard'
            ],
            'Quangtrong\News\ReportWidgets\TopPosts' => [
                'label'   => 'quangtrong.news::lang.widget.topposts',
                'context' => 'dashboard'
            ],
            'Quangtrong\News\ReportWidgets\Subscribers' => [
                'label'   => 'quangtrong.news::lang.widget.subscribers',
                'context' => 'dashboard'
            ]
        ];
    }

    public function registerComponents()
    {
        return [
            'Quangtrong\News\Components\Posts'       => 'newsPosts',
            'Quangtrong\News\Components\Post'        => 'newsPost',
            'Quangtrong\News\Components\Subscribe'   => 'newsSubscribe',
            'Quangtrong\News\Components\Unsubscribe' => 'newsUnsubscribe'
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'quangtrong.news::mail.email_en' => 'E-mail',
            'quangtrong.news::mail.email_hu' => 'E-mail'
        ];
    }

    public function registerPermissions()
    {
        return [
            'quangtrong.news.posts' => [
                'tab'   => 'quangtrong.news::lang.menu.news',
                'label' => 'quangtrong.news::lang.permission.posts',
                'order' => 100,
                'roles' => ['publisher']
            ],
            'quangtrong.news.categories' => [
                'tab'   => 'quangtrong.news::lang.menu.news',
                'label' => 'quangtrong.news::lang.permission.categories',
                'order' => 200,
                'roles' => ['publisher']
            ],
            'quangtrong.news.subscribers' => [
                'tab'   => 'quangtrong.news::lang.menu.news',
                'label' => 'quangtrong.news::lang.permission.subscribers',
                'order' => 300,
                'roles' => ['publisher']
            ],
            'quangtrong.news.statistics' => [
                'tab'   => 'quangtrong.news::lang.menu.news',
                'label' => 'quangtrong.news::lang.permission.statistics',
                'order' => 400,
                'roles' => ['publisher']
            ],
            'quangtrong.news.import_export' => [
                'tab'   => 'quangtrong.news::lang.menu.news',
                'label' => 'quangtrong.news::lang.permission.import_export',
                'order' => 500,
                'roles' => ['publisher']
            ],
            'quangtrong.news.logs' => [
                'tab'   => 'quangtrong.news::lang.menu.news',
                'label' => 'quangtrong.news::lang.permission.logs',
                'order' => 600,
                'roles' => ['publisher']
            ],
            'quangtrong.news.settings' => [
                'tab'   => 'quangtrong.news::lang.menu.news',
                'label' => 'quangtrong.news::lang.permission.settings',
                'order' => 700,
                'roles' => ['publisher']
            ]
        ];
    }

    public function registerSchedule($schedule)
    {
        $schedule->command('queue:work --daemon --queue=newsletter')->everyMinute()->withoutOverlapping();
    }

    public function boot()
    {
        Event::listen('pages.menuitem.listTypes', function()
        {
            return [
                'news-category'       => 'quangtrong.news::lang.sitemap.category',
                'all-news-categories' => 'quangtrong.news::lang.sitemap.categories',
                'post-list' => 'quangtrong.news::lang.sitemap.post_list',
                'post-page' => 'quangtrong.news::lang.sitemap.post_page'
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function($type)
        {
            if ($type == 'post-list' || $type == 'post-page') {
                return Posts::getMenuTypeInfo($type);
            }elseif($type == 'news-category' || $type == 'all-news-categories') {
                return Categories::getMenuTypeInfo($type);
            }
        });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme)
        {
            if ($type == 'post-list' || $type == 'post-page') {
                return Posts::resolveMenuItem($item, $url, $theme);
            }elseif($type == 'news-category' || $type == 'all-news-categories') {
                return Categories::resolveMenuItem($item, $url, $theme);
            }
        });
    }
}
