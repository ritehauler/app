<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSCategory extends Base
{

    use SoftDeletes;
    public $table = 'sys_category';
    public $timestamps = true;
    public $primaryKey;
    public $data;
    protected $dates = ['deleted_at'];



    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'category_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey,'is_parent', 'parent_id', "title", "description",'parent_ids','product_count','level','is_featured','featured_type','category_type','status','created_at', 'updated_at', 'deleted_at');
    }

    public function getData($id=0,$status = false)
    {
        $data = $this->get($id);

        if ($data !== FALSE) {
            $data->child = $this->getChild($id,$status);

            //Calculate parent product count only by sum of active sub categories
            if($data->parent_id == 0 && $data->child){
                $parent_product_count = 0;
                if(count($data->child)>0){

                    foreach($data->child as $child){
                      //  print_r($child);
                        if($child->status == 1){
                            $product_count  = ($child->product_count > 0) ? $child->product_count : 0;
                            $parent_product_count += $product_count;
                        }
                    }
                }
                $data->product_count =  $parent_product_count;
        }

        }

        return $data;
    }

    public function getChild($id=0,$status = false)
    {
        $child = array();
        $query = $this->select('category_id')
                    ->whereNull("deleted_at")
                    ->where("parent_id", "=", $id);

        if($status){
            $query = $query->where('status','=',1);
        }

        $query = $query->get();

        foreach ($query as $record) {
            $child[] = $this->getData($record->category_id);
        }
        return $child;
    }

    public function addParentList($id=0)
    {
        \DB::statement("CALL category_parents($id)");

    }

    public function UpdateProductCountInCategory($id=0,$operation='+')
    {
        \DB::statement("CALL update_category_product_count($id,'$operation')");

    }

    /**
     * @param int $parent_id
     * @return bool
     */
    public function getCategoryType($parent_id)
    {
        $row = $this->select('category_type')
            ->whereNull("deleted_at")
            ->where("category_id", "=", $parent_id)->get();


        return isset($row[0]) ? $row[0]->category_type : false;
    }

    /**
     * Get Category title by role id
     * @param $id
     * @return bool
     */
    public function geCategoryTitleById($id){
        $row = $this->select('title')->where($this->primaryKey, '=', $id)
            ->whereNull("deleted_at")
            ->get();
        return isset($row[0])?$row[0]->title:false;
    }

    /**Get Category data by title
     * @param $title
     * @return bool
     */
    public function getCategoryByTitle($title){
        $row = $this->where('title', '=', $title)
            ->whereNull("deleted_at")
            ->get();
        return isset($row[0])?$row[0]:false;
    }

    /**
     * Get parents by category ids
     * @param $ids
     * @return bool
     */
    public function getParentsByCategoryIds($ids)
    {
        $row = $this->whereIn('category_id', $ids)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0])?$row:false;
    }



}