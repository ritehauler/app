<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Mail;
// models
use App\Http\Models\Setting;
use App\Http\Models\Conf;
use App\Http\Models\EmailTemplate;

class Preference extends Base {

    public function __construct() {
        // set tables and keys
        $this->__table = $this->table = 'preference';
        $this->primaryKey = $this->__table . '_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'key', 'type', 'default_value', 'created_at', 'updated_at', 'deleted_at');
    }

}
