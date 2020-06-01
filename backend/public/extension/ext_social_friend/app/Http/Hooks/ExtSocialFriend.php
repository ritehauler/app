<?php namespace App\Http\Hooks;

// models
#use App\Http\Models\Achievement;

class ExtSocialFriend
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
        /*
         * Normal event data binding
         */
        // on error
        /*if ($base_data["response"] == "error") {
            $base_data["customize_me"] = 1;
        } else {
            // on success
            $base_data["test_me"] = 1;
        }*/

        /*
         * Explicitly on API call
         */

        /*if ($request->is(DIR_API.'*'))
        {
            // on error
            if ($base_data["response"] == "error") {
                $base_data["customize_me"] = 1;
            } else {
                // on success
                $base_data["test_me"] = 1;
            }
        }*/

        return $base_data;
    }

}