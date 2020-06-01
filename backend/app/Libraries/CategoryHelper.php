<?php
namespace App\Libraries;

use App\Http\Models\SYSCategory;
/**
 * Class ProductHelper
 */
Class CategoryHelper
{

    private $_modelPath = '';
    private $_model = '';

    /**
     * CategoryHelper constructor.
     */
    public function __construct()
    {
        $this->_modelPath = config("system.MODEL_PATH");
        $this->_model = new SYSCategory();

    }

    /**
     * Get unique Parents of category ids
     * @param $ids
     * @return array|bool
     */
    public function getParentByCategories($ids)
    {
        $parent_rows = $this->_model->getParentsByCategoryIds($ids);

        if($parent_rows){

            if(count($parent_rows)){

                $parent = array();
                foreach($parent_rows as $parent_row){

                    $parent_arr = explode(',',$parent_row->parent_ids);
                    foreach($parent_arr as $p){
                        if($parent_row->category_id != $p){
                            $parent[] = $p;
                        }
                    }

                    if(count($parent)>0)
                        $parent =  array_unique($parent);
                }

                return $parent;
            } //end of count array
        }

        return false;
    }

    /**
     * increement/decrement category count of parent
     * @param $category_ids
     * @param string $adjust_operator
     */
    public function adjustProductCategoryParentCount($category_ids,$adjust_operator = '+')
    {

        if(count($category_ids)>0){

            foreach($category_ids as $sub_cat_id){
                $this->_model->UpdateProductCountInCategory($sub_cat_id,$adjust_operator);
            }
        }


     /*   $parents_ids = $this->getParentByCategories($category_ids);

        //Update category count for products
        if($parents_ids){

            if(count($parents_ids) > 0){

                foreach($parents_ids as $category_id){
                    $this->_model->UpdateProductCountInCategory($category_id,$adjust_operator);
                }
            }
        }*/
    }


}