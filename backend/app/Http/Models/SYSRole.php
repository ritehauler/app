<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class SYSRole extends Base
{

    use SoftDeletes;
    public $table = 'sys_role';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];



    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'role_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'parent_id','entity_type_id', "title", 'slug', "description", "created_by",'is_group', 'created_at', 'updated_at', 'deleted_at');
    }

    /**
     * @param $entity_type_id
     * @return bool
     */
    function getGroupRoleByEntityType($entity_type_id)
    {
        $row = $this->where("entity_type_id","=",$entity_type_id)
            ->where('parent_id','<>',0)
            ->where('is_group','<>',1)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0]) ? $row : false;
    }

    public static function getDepartmentGroup($parent_title)
    {
        $where_clause = ($parent_title == '*')? '' : $where_clause = " WHERE a.title = '$parent_title'";
        return DB::select("SELECT b.role_id, b.parent_id, b.title, a.title AS parent_title  FROM sys_role a
INNER JOIN sys_role b ON a.role_id = b.parent_id $where_clause");
    }

    /**
     * Get role title by role id
     * @param $id
     * @return bool
     */
    public function getRoleTitleById($id){
        $row = $this->select('title')->where($this->primaryKey, '=', $id)
            ->whereNull("deleted_at")
            ->get();
        return isset($row[0])?$row[0]->title:false;
    }

    /**Get role data by title
     * @param $title
     * @return bool
     */
    public function getRoleByTitle($title,$role_id = false){
        $query = $this->where('title', '=', $title)
            ->whereNull("deleted_at");

        if($role_id)   $query->where("$this->primaryKey",'<>',$role_id);
        $row =  $query->get();
        return isset($row[0])?$row[0]:false;
    }

    /**
     * @param $entity_type_id
     * @return bool
     */
    public function getRoleByEntityType($entity_type_id){
        $row = $this->where('entity_type_id', '=', $entity_type_id)
            ->whereNull("deleted_at")
            ->get();
        return isset($row[0])?$row[0]->role_id:false;
    }

    /**
     * @param $entity_type_id
     * @return bool
     */
    function getGroupByEntityType($entity_type_id)
    {
        $row = $this->where("entity_type_id","=",$entity_type_id)
            ->where('is_group','=',1)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0]) ? $row : false;
    }

    /**
     * @param $entity_type_id
     * @param $parent_id
     * @return bool
     */
    function getRoleByEntityTypeAndGroup($entity_type_id,$parent_id)
    {
        $row = $this->where("entity_type_id","=",$entity_type_id)
            ->where('parent_id','=',$parent_id)
            ->where('is_group','<>',1)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0]) ? $row : false;
    }

    /**
     * Get entity type id of role
     * @param $slug
     * @return bool
     */
    public function getEntityTypeIDBySlug($slug)
    {
        $row = $this->select('entity_type_id')->where("slug","=",$slug)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0]) ? $row[0]->entity_type_id : false;
    }

    /**
     * @param $entity_type_id
     * @return bool
     */
    public function getRoleIdByEntityType($entity_type_id)
    {
        $row =  $this->where('entity_type_id',$entity_type_id)
            ->where('parent_id','=',0)
            ->get();

        return isset($row[0]) ? $row[0] : false;
    }

}