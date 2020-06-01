<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class PLAttachmentType extends Base
{

    use SoftDeletes;
    public $table = 'pl_attachment_type';
    public $timestamps = true;
    public $primaryKey;
    protected $dates = ['deleted_at'];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    /*protected $casts = [
        'title' => 'string',
        "attachment_type_id" => "integer"
    ];*/

    public function __construct()
    {
        // set tables and keys
        $this->__table = $this->table;
        $this->primaryKey = 'attachment_type_id';
        $this->__keyParam = $this->primaryKey . '-';
        $this->hidden = array();

        // set fields
        $this->__fields = array($this->primaryKey, 'title', 'identifier', 'thumb_dimension', "allowed_extensions", 'created_at', 'updated_at', 'deleted_at');
        $this->_class_name = $this;
    }


    /**
     * getMiniData
     * @param int id
     * @return Query
     */
    public function getMiniData($id)
    {
        $data = $this->get($id);
        if ($data !== FALSE) {
            $data2 = (object)array();
            $data2->{$this->primaryKey} = $data->{$this->primaryKey};
            $data2->identifier = $data->identifier;
            $data2->title = $data->title;
            unset($data);
            $data = $data2;
        }
        return $data;
    }


}
