<?php namespace Quangtrong\News\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class AddCategoryFieldToTable extends Migration
{
    public function up()
    {
        Schema::table('quangtrong_news_posts', function($table)
        {
            $table->string('category_id', 3)->default(0);
        });
    }

    public function down()
    {
        Schema::table('quangtrong_news_posts', function($table)
        {
            $table->dropColumn('category_id');
        });
    }
}
