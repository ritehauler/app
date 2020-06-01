<?php

/**
 * Class ProductHelper
 */
namespace App\Libraries;
use App\Http\Models\FlatTable;
use App\Http\Models\SYSTableFlat;

Class ProductHelper
{
    private $_SYSTableFlatModel = '';

    /**
     * ProductHelper constructor.
     */
    public function __construct()
    {
        $this->_SYSTableFlatModel = new SYSTableFlat('product');
    }

    /**
     * Get Product max price
     * @param $where_column
     * @param $where_value
     * @return mixed
     */
    public function getMaxPrice($where_column=false, $where_value=false)
    {
        $flat_table_model = new FlatTable();
        return $flat_table_model->getMaxPrice($where_column, $where_value);
    }

    /**
     * Get Product type option value
     * @param $identifier
     * @return int
     */
    public function getProductTypeByIdentifier($identifier)
    {
        if ($identifier == "bundle") return 3;
        else if ($identifier == "recipe") return 2;
        return 1;
    }

    /**
     * Get Max Price by Product type
     * @param $identifier
     * @return mixed
     */
    public function getMaxPriceByProductType($identifier)
    {
        $value = $this->getProductTypeByIdentifier($identifier);
        $flat_table_model = new FlatTable();
        return $flat_table_model->getMaxPrice("product_type",$value);

    }

    /**
     * Get Product which has Promotion
     * @param bool $date
     * @return bool
     */
    public function getExpiredPromotionProducts($date=false,$get_columns = array())
    {
        $where_condition = 'product_promotion_id > 0';
        $where_condition  .= ($date) ? " AND promotion_end_date < '$date'" : "";
        return $this->_SYSTableFlatModel->getDataByWhere($where_condition,$get_columns);
    }

    /**
     * Get Products where promotion has to apply
     * @param bool $date
     * @return bool
     */
    public function getPromotionProducts($date=false)
    {
        $date = (!$date) ? date('Y-m-d') : $date;
        $flat_table_model = new FlatTable();
        return $flat_table_model->getPromotionProducts($date);
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return bool
     */
    public function totalProducts($start_date,$end_date)
    {
        $where_condition = " created_at >= '$start_date' AND created_at <= '$end_date' AND status = 1";
        $return = $this->_SYSTableFlatModel->getColumnByWhere($where_condition,'COUNT(entity_id) as total_count');
        return $return->total_count;
    }

    /**
     * @param $product_type
     * @return array
     */
    public function topProducts($product_type,$start_date,$end_date)
    {
        $flat_table_model = new FlatTable();
        $products = $flat_table_model->getTopProducts($product_type,$start_date,$end_date);
        $return = array();
        if($products){
            foreach($products as $product){
                $return['title'][] = $product->title;
                $return['total'][] = ($product->total && $product->total > 0) ? $product->total : 0;
            }
        }
       return $return;
    }

    /**
     * set request identifier for products by their type
     * @param $product_type
     * @return string
     */
    public static function getRequestedIdentifierByType($is_other)
    {
        if($is_other == 1) return  'other_item';
        else if($is_other == 0) return  'item';

    }

    /**
     * Label of Product type to render in form
     * @param $product_type
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public static function getProductTypeLabel($product_type,$default_type = false)
    {
        if($product_type == 3){
            $form_heading = trans('panel.bundle');
        }
        else if($product_type == 2){
            $form_heading = trans('panel.recipe');
        }
        else{
            if($default_type)
                $form_heading = $default_type;
            else
                $form_heading = trans('panel.product');
        }

        return $form_heading;
    }


}