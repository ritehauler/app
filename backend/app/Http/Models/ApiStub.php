<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// init models
//use App\Http\Models\Conf;

class ApiStub extends Base
{

    use SoftDeletes;
    public $table = 'api_stub';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'api_stub_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'title', 'object_identifier', 'request_type', 'endpoint_uri', 'description', 'request', 'response', 'is_active', 'created_at', 'updated_at', 'deleted_at');
    }




}