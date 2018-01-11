<?php namespace Quangtrong\News\Models;

use Model;

class Categories extends Model
{
    use \October\Rain\Database\Traits\Sluggable;    //Dùng để tạo slug
    // use \October\Rain\Database\Traits\Sortable; //Dùng để sắp xếp dự liệu theo thứ tự
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\NestedTree;   //Dùng để kéo thả danh mục, nếu k use sẽ báo lỗi 

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    protected $table = 'quangtrong_news_categories';

    public $rules = [
        'name'   => 'required',
        'slug'   => ['required', 'regex:/^[a-z0-9\/\:_\-\*\[\]\+\?\|]*$/i', 'unique:quangtrong_news_categories'],
        'status' => 'required|between:1,2|numeric',
        'hidden' => 'required|between:1,2|numeric'
    ];

    // Khai báo trường slug để tạo bởi \October\Rain\Database\Traits\Sluggable;
    // Thuôc tính này bắt buộc phải có nếu sử dụng Traits trên
    // Nếu không sẽ báo lỗi
    protected $slugs = [
        'slug' => 'name'
    ];

    public $translatable = [
        'name',
        ['slug', 'index' => true],
        'summary',
        'content'
    ];
}
