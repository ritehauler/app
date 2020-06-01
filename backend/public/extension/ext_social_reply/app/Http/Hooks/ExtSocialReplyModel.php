<?php namespace App\Http\Hooks;

// models
#use App\Http\Models\Achievement;

class ExtSocialReplyModel
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
    public function post($request, $base_data)
    {
        //$base_data;

        return $base_data;
    }

}