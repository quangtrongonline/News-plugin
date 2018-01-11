<?php namespace Quangtrong\News\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'quangtrong_news_settings';

    public $settingsFields = 'fields.yaml';
}
