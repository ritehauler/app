<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Base
{

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table = 'state';
        $this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = [];

        // set fields
        $this->__fields = [$this->primaryKey, 'name', 'code', 'country_id', 'created_at', 'deleted_at'];
    }

    public function getData($id=0,$status = false)
    {
        $data = $this->get($id);
        return $data;
    }

}