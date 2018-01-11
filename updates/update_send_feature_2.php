<?php namespace Quangtrong\News\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class UpdateSendFeature2 extends Migration
{
    public function up()
    {
        Schema::table('quangtrong_news_posts', function($table)
        {
            $table->dropColumn('send');
        });
    }

    public function down()
    {
        Schema::table('quangtrong_news_posts', function($table)
        {
            $table->boolean('send')->default(false)->change();
        });
    }
}
