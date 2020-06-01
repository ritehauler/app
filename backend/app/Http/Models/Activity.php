<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
// models
use App\Http\Models\User;
use App\Http\Models\Category;
use App\Http\Models\Winner;

class Activity extends Base {

    public function __construct() {
        // set tables and keys
        $this->__table = $this->table = 'activity';
        $this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'key', 'description', 'user_points', 'target_user_points', 'send_notification', 'created_at', 'updated_at', 'deleted_at');
    }
}
