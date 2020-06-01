<?php namespace App\Libraries;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * Class CustomHelper
 */
Class APIPaging
{

    /**
     * Paging Data (static)
     */
    private static $pagingData;

    /**
     * Constructor
     *
     * @param string $url URL
     */
    public function __construct()
    {

    }

    /**
     * @param array $params
     * @return mixed
     */
    public static function pagingQuery(Request $request,
                                       $query,
                                       int $total_records = 0,
                                       string $primary_alias = '',
                                       $model
    )
    {
        // set vars
        $primary_alias = trim($primary_alias) == "" ? $primary_alias : $primary_alias . '.';
        $primary_alias = str_replace('..', '.', $primary_alias); // take care of provided param error
        // get paging/params
        $limit = trim(strip_tags($request->input('limit', "")));
        $limit = $limit == "" ? PAGE_LIMIT_API : intval($limit);
        $offset = intval($request->input("offset", 0));
        $offset = $offset < 0 ? 0 : $offset;
        $next_offset = $prev_offset = $offset; // - init


        // datatables request
        if (intval($request->input("dt_request", 0)) == 1) {
            // offfset / limits
            $offset = $offset < $total_records ? $offset : ($total_records - 1);
            $offset = $offset < 0 ? 0 : $offset;
            // next offset
            $next_offset = ($offset + $limit) > $total_records ? ($total_records - $offset) : ($offset + $limit);
            // prev offset
            $prev_offset = $offset > 0 ? ($offset - $limit) : $offset;
            $prev_offset = $prev_offset > 0 ? $prev_offset : 0;
            // apply offset
            $query->skip($offset);
        } else {
            // apply new paging offset
            if ($request->sorting == "asc") {
                $query->where($primary_alias . $model->primaryKey, ">", $offset);
            } else {
                if ($offset > 0) {
                    $query->where($primary_alias . $model->primaryKey, "<", $offset);
                }
            }
        }

        // apply limit
        $query->take($limit);

        // set pagination response
        self::$pagingData = array(
            "limit" => $limit,
            "offset" => $offset,
            "total_records" => $total_records,
            "next_offset" => $next_offset,
            "prev_offset" => $prev_offset
        );

        // assign to output (for internal sharing)
        //$this->_apiData['data']["paging"] = self::$pagingData;

        return $query;
    }


    /**
     * paging
     *
     * @return Response
     */
    public static function paging(Request $request, $raw_records = array(), $model)
    {
        // init data
        $paging = self::$pagingData;


        // if not datatables request
        if (intval($request->input("dt_request", 0)) == 0) {
            $r_index = count($raw_records);
            $r_index = $r_index > 0 ? ($r_index - 1) : $r_index;
            if (count($raw_records) > 0) {
                $paging["next_offset"] = $raw_records[$r_index]->{$model->primaryKey};
                //$prev_offset = $raw_records[0]->{$this->_pModel->primaryKey};
            }
        }

        // set pagination response
        self::$pagingData = array(
            "limit" => $paging["limit"],
            "offset" => $paging["offset"],
            "total_records" => $paging["total_records"],
            "next_offset" => $paging["next_offset"],
            "prev_offset" => intval($request->offset),
            "page_records" => count($raw_records)
        );

        // assign to output (for internal sharing)
        return $paging;
    }
}