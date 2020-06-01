<?php
/**
 * Summary : Base database wrapper
 * Description : Handle all db related queries. Includes other functions for ease
 * to save efforts for altering or creating tables
 *
 * Created by PhpStorm.
 * User: Salman
 * Date: 12/28/2017
 * Time: 5:42 PM
 */

namespace App\Libraries\System;

use Illuminate\Support\Facades\DB;


/**
 * Class Post
 */
Class Database
{
    /**
     * db instance
     */
    public $query = NULL;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        // init db instance
        $this->query = DB::connection(config('database.default'));
    }


    /**
     * Check if column exists
     *
     * @param $table_name
     * @param $column_name
     * @return bool
     */
    public function isColumnExist($table_name, $column_name)
    {

        try {
            $where = "table_schema='" . MASTER_DB_NAME . "' AND TABLE_NAME='$table_name' AND COLUMN_NAME LIKE '%$column_name%'";
            $columns = "column_name,data_type,column_key";
            $order_by = "ORDER BY column_name asc";
            $column = $this->query->select("SELECT $columns FROM INFORMATION_SCHEMA.COLUMNS WHERE $where $order_by");
            if (!empty($column)) {
                return $column[0]->column_name;
            }

            return false;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Create column for a table
     *
     * @param $table_name
     * @param $column_name
     * @param string $type
     * @return bool
     */
    public function createColumn($table_name, $column_name, $type = "text NULL")
    {
        try {
            if (!$this->isColumnExist($table_name, $column_name)) {
                $data = $this->query->select("ALTER TABLE $table_name ADD `$column_name` $type");
                return $data;
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}