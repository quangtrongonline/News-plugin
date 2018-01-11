<?php namespace Quangtrong\News\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class UpdateSendFeature extends Migration
{
    public function up()
    {
        Schema::table('quangtrong_news_posts', function($table)
        {
            $table->dateTime('last_send_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('quangtrong_news_posts', function($table)
        {
            $table->dropColumn('last_send_at');
        });
    }
}
