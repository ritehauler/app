<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
// models
//use App\Http\Models\Setting;

class Package extends Base {

    public function __construct() {
        // set tables and keys
        $this->__table = $this->table = 'package';
        $this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'name', 'key', 'description', 'hint', 'expiry_unit', 'expiry_duration', 'price', 'price_unit', 'created_at', 'updated_at', 'deleted_at');
    }

}
