<?php namespace App\Http\Hooks\Magento2;

// models
#use App\Http\Models\Achievement;

use Illuminate\Http\Request;

class CategoryRequest
{

    protected $__modelPath = "\App\Http\Models\\";

    public function __construct()
    {

    }


    /**
     * Post
     * @param object $request
     * @param array $base_data
     * @return Object
     */
    public function get(Request $request, $base_data)
    {
        // replace params
        //$base_data['categoryId'] = $base_data['category_id'];

        return $base_data;
    }

}