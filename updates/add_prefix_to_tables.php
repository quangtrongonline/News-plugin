<?php namespace Quangtrong\News\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class AddPrefixToTables extends Migration
{
    public function up()
    {
        Schema::rename('news_posts', 'quangtrong_news_posts');
        Schema::rename('news_subscribers', 'quangtrong_news_subscribers');
    }

    public function down()
    {
        Schema::rename('quangtrong_news_posts', 'news_posts');
        Schema::rename('quangtrong_news_subscribers', 'news_subscribers');
    }
}
