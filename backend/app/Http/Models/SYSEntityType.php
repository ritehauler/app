<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\SoftDeletes as SoftDeletes;

//use Illuminate\Routing\Route;

// init models
//use App\Http\Models\Conf;

class SYSEntityType extends Base
{

    use SoftDeletes;
    public $table = 'sys_entity_type';
    public $timestamps = TRUE;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    /**
     * SYSEntityType constructor.
     */
    public function __construct()
    {

        // use caching
        $this->__useCache = TRUE;
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'entity_type_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        $this->__fields = array($this->primaryKey, "title", 'identifier', "show_gallery", "allow_auth", 'is_social', 'target_entity', 'actor_entity', "use_flat_table", 'allow_backend_auth', 'show_in_menu', 'allow_add_attribute', 'depend_entity_type', 'template', 'wft_id', 'created_at', 'updated_at', 'deleted_at','import_permission','export_permission');
    }


    /**
     * @param $identifier
     * @return bool
     */
    public function getEntityTypeByName($identifier)
    {
        $row = $this->where('identifier', '=', $identifier)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0]) ? $row[0] : FALSE;
    }

    /**
     * @param $department
     * @param $user_entity
     * @return bool
     */
    public function checkPanelAccess($department, $user_entity)
    {
        $entity_auth_id = $user_entity->entity_auth_id;
        $result = \DB::select("SELECT sys_entity_type.entity_type_id, sys_entity.entity_id, sys_entity.entity_auth_id , sys_entity_role.role_id
        FROM sys_entity_type LEFT JOIN sys_entity ON sys_entity.entity_type_id = sys_entity_type.entity_type_id
        LEFT JOIN sys_entity_role ON sys_entity_role.entity_id = sys_entity.entity_id
        WHERE sys_entity_type.allow_backend_auth = 1 AND sys_entity_type.identifier = '$department' AND sys_entity.entity_auth_id = $entity_auth_id;");

        return (count($result)) ? $result[0] : FALSE;
    }


    /**
     * @param $id
     * @return bool
     */
    public function getEntityTypeById($id)
    {
        $row = $this->where($this->primaryKey, '=', $id)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0]) ? $row[0] : FALSE;
    }


    /**
     * @return bool
     */
    public static function isEntityExternalCall()
    {
        $request = Request();
        if (!isset($request->entity_type_id))
            return FALSE;
        $result = \DB::select('SELECT is_external FROM sys_entity_type WHERE entity_type_id = ' . $request->entity_type_id);
        if (isset($result[0]) && !empty($result[0]->is_external))
            return TRUE;

        return FALSE;
    }

    /**
     * @return mixed
     */
    public static function getUriMask()
    {
        $ressponse['controller'] = '';
        $ressponse['uri'] = '';
        $ressponse['type'] = '';
        $request = Request();

        $occurance_count = 0;
        $uri = str_replace(DIR_API, '', $request->path(), $occurance_count);
        if (!$occurance_count)
            return $ressponse;
        $request_type = strtolower($request->method());
        $sql = "SELECT * FROM api_method WHERE mask_uri = '$uri' AND type =  '" . $request_type . "'";

        $result = \Cache::remember(md5($sql), 1, function () use ($sql) {
            return \DB::select($sql);
        });
        if (isset($result[0])) {
            $ressponse['controller'] = $result[0]->method;
            $ressponse['uri'] = str_replace(DIR_SYSTEM, '', $result[0]->mask_uri);
            $ressponse['type'] = $result[0]->type;
            $ressponse['is_external'] = $result[0]->is_external;
            $ressponse['entity_type_id'] = $result[0]->type_id;
        }

        return $ressponse;
    }


    /**
     * @return mixed
     */
    public static function getUri()
    {
        $sql = "SELECT * FROM api_method Where method is not null And method != '' GROUP BY uri,`type` ";

        return \Cache::remember(md5($sql), 1, function () use ($sql) {
            return \DB::select($sql);
        });
    }

    /**
     * @param $title
     * @return bool
     */
    public function getEntityTypeByTitle($title)
    {
        $row = $this->where('title', '=', $title)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0]) ? $row[0] : FALSE;
    }


    /**
     * @param $table_name
     * @param $column_name
     * @param string $type
     * @return bool
     */
    public function create_column($table_name, $column_name, $type = "text NULL")
    {
        if (!$this->is_column_exist($table_name, $column_name)) {
            $data = \DB::select("ALTER TABLE $table_name ADD `$column_name` $type");

            return $data;
        }

        return FALSE;
    }


    /**
     * @param $table_name
     * @param $column_name
     * @return bool
     */
    public function is_column_exist($table_name, $column_name)
    {
        $where = "table_schema='" . MASTER_DB_NAME . "' AND TABLE_NAME='$table_name' AND COLUMN_NAME LIKE '$column_name'";
        $columns = "column_name,data_type,column_key";
        $order_by = "ORDER BY column_name asc";
        $column = \DB::select("SELECT $columns FROM INFORMATION_SCHEMA.COLUMNS WHERE $where $order_by");
        if (!empty($column)) {
            return $column[0]->column_name;
        }

        return FALSE;
    }


    /**
     * @param $table_name
     * @return bool
     */
    public function is_table_exist($table_name)
    {
        $where = "table_schema='" . MASTER_DB_NAME . "' AND TABLE_NAME='$table_name'";
        $column = \DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE $where");
        if (!empty($column)) {
            return $column[0]->TABLE_NAME;
        }

        return FALSE;
    }


    /**
     * @param $sql
     * @param int $timeout
     * @return mixed
     */
    public function cacheQuery($sql, $timeout = 1)
    {
        return \Cache::remember(md5($sql), $timeout, function () use ($sql) {
            return \DB::select($sql);
        });
    }


    /**
     * Get Entity Type ID by Identifier
     *
     * @param $identifier
     * @return bool
     */
    public function getIdByIdentifier($identifier)
    {
        $row = $this->select('entity_type_id')->where('identifier', '=', $identifier)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0]) ? $row[0]->entity_type_id : FALSE;
    }

    /**
     * @param $table_name
     */
    public function createTable($table_name)
    {
        $data = \DB::select("CREATE TABLE " . $table_name . "(
					id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
					entity_id bigint(20) unsigned NOT NULL,
					updated_at datetime NULL,
					deleted_at datetime NULL,
					created_at TIMESTAMP,
					KEY `entity_id` (`entity_id`),
					KEY `deleted_at` (`deleted_at`)
					)");

    }

    /**
     * @param $identifier
     * @return bool
     */
    public function getByIdentifier($identifier)
    {
        $row = $this->where('identifier', '=', $identifier)
            ->whereNull("deleted_at")
            ->get();

        return isset($row[0]) ? $row[0] : FALSE;
    }
}