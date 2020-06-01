<?php
namespace App\Http\Controllers\Api\Extension\Social\Custom;

use App\Http\Controllers\Api\Extension\Social\Package\LikeController;
use App\Http\Controllers\Controller;
use App\Libraries\CustomHelper;
use Illuminate\Http\Request;
use View;
use Validator;


Class EntityLikeController extends LikeController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function listing(Request $request)
    {
        // trim/escape all
        $request->merge(array_map('strip_tags', $request->all()));
        $request->merge(array_map('trim', $request->all()));

        // validations
        $validator = Validator::make($request->all(), array(
            // entity vs entity type
            'target_' . $this->_entityModel->primaryKey => 'int|exists:' . $this->_entityModel->table . ',' . $this->_entityModel->primaryKey . ',' . $this->_entityTypeModel->primaryKey . ',' . $request->input('target_' . $this->_entityTypeModel->primaryKey),
            'type' => 'string|in:public,private',
            'actor_' . $this->_entityModel->primaryKey => 'int|exists:' . $this->_entityModel->table . ',' . $this->_entityModel->primaryKey . ',' . $this->_entityTypeModel->primaryKey . ',' . $request->input('actor_' . $this->_entityTypeModel->primaryKey),
        ));

        $request->type = trim($request->input("type", "")) == "" ? 'public' : $request->input("type", "public");

        // validate
        if ($validator->fails()) {
            $this->_apiData['message'] = $validator->errors()->first();
        } else if ($request->type == 'public' && intval($request->input('target_' . $this->_entityModel->primaryKey, 0)) == 0) {
            $this->_apiData['message'] = trans($this->_extIdentifier . '.target_entity_required');
        } else if ($request->type == 'private' && intval($request->input('actor_' . $this->_entityModel->primaryKey, 0)) == 0) {
            $this->_apiData['message'] = trans($this->_extIdentifier . '.actor_entity_required');
        } else {
            // success response
            $this->_apiData['response'] = "success";

            // init output data array
            $this->_apiData['data'] = $data = array();

            // defaults
            $data[$this->_pModel->objectIdentifier . "_listing"] = array();

            // allowed ordering/sorting
            $allowed_sorting = "desc,asc";
            $allowed_ordering = $this->_pModel->primaryKey . ",created_at";
            $primary_alias = 'a.';

            // sorting defaults
            $request->order_by = $request->input("order_by", "") == "" ? explode(",", $allowed_ordering)[0] : $request->order_by;
            $request->sorting = $request->input("sorting", "") == "" ? explode(",", $allowed_sorting)[0] : $request->sorting;
            $request->sorting = strtolower($request->sorting);

            //this need to generalize
            //add project conditions
            if(isset($request->target_entity_type_id) && $request->target_entity_type_id == 14){

                $query = $this->_pModel->select($primary_alias . $this->_pModel->primaryKey)
                    ->from($this->_pModel->table . ' AS a')
                    ->leftJoin($this->_entityModel->table . ' AS b', 'b.' . $this->_entityModel->primaryKey, '=', $primary_alias . 'data_' . $this->_entityModel->primaryKey)
                    ->leftJoin('product_flat as p','p.entity_id','=',$primary_alias .'target_entity_id')
                    ->where($primary_alias . 'type', '=', $request->type)
                    ->where($primary_alias . $this->_activityTypeModel->primaryKey, $this->_activityTypeData->{$this->_activityTypeModel->primaryKey})
                    ->where($primary_alias . $this->_eTypeExtMapModel->primaryKey, $this->_extMapData->{$this->_eTypeExtMapModel->primaryKey})// only data for this extension mapping
                   ->where('p.status','=',1)
                    ->where('p.availability','=',1)
                    ->whereNull($primary_alias . 'deleted_at')
                    ->whereNull('b.deleted_at');

            }
            else{
                // make query
                $query = $this->_pModel->select($primary_alias . $this->_pModel->primaryKey)
                    ->from($this->_pModel->table . ' AS a')
                    ->leftJoin($this->_entityModel->table . ' AS b', 'b.' . $this->_entityModel->primaryKey, '=', $primary_alias . 'data_' . $this->_entityModel->primaryKey)
                    ->where($primary_alias . 'type', '=', $request->type)
                    ->where($primary_alias . $this->_activityTypeModel->primaryKey, $this->_activityTypeData->{$this->_activityTypeModel->primaryKey})
                    ->where($primary_alias . $this->_eTypeExtMapModel->primaryKey, $this->_extMapData->{$this->_eTypeExtMapModel->primaryKey})// only data for this extension mapping
                    ->whereNull($primary_alias . 'deleted_at')
                    ->whereNull('b.deleted_at');
            }

            // if private, show ffavorites
            if ($request->type == 'private') {
                $query->where($primary_alias . 'actor_' . $this->_entityModel->primaryKey, $request->{'actor_' . $this->_entityModel->primaryKey});
            } else {
                $query->where($primary_alias . 'target_' . $this->_entityModel->primaryKey, $request->{'target_' . $this->_entityModel->primaryKey});
            }

            // get total
            $total_records = $query->count();
            //echo $query->toSql(); exit;

            // get paging query
            $query = $this->__pagingQuery($request, $query, $total_records, $primary_alias);

            // apply ordering
            $query->orderBy($primary_alias . $request->order_by, strtoupper($request->sorting));

            // get records
            $raw_records = $query->select($primary_alias . $this->_pModel->primaryKey)->get();

            // remove object mapping
            $raw_records = json_decode(json_encode($raw_records));

            // fetch records
            if (isset($raw_records[0])) {
                $records = array();
                // set actor ID to class
                $this->_pModel->currentActorID = $request->{'actor_' . $this->_entityModel->primaryKey};

                foreach ($raw_records as $raw_record) {
                    $records[] = $this->_pModel->getData($raw_record->{$this->_pModel->primaryKey});
                }
                // replace raw records
                $raw_records = $records;
            }

            // response data
            $data[$this->_pModel->objectIdentifier . "_listing"] = $raw_records;

            // get paging data
            $data["paging"] = $this->__paging($request, $raw_records);
            // assign to output
            $this->_apiData['data'] = $data;

        }

        // call hook
        $this->_apiData = CustomHelper::hookData($this->_pHook, __FUNCTION__, $request, $this->_apiData);

        return $this->__ApiResponse($request, $this->_apiData);
    }


}