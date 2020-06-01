<?php namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes AS SoftDeletes;

// models
#use App\Http\Models\Setting;

class PLAttachment extends Base {
	
	use SoftDeletes;
    public $table = 'pl_attachment';
    public $timestamps = true;
	public $primaryKey;
    protected $dates = ['deleted_at'];
	
	public function __construct()
	{
		// set tables and keys
        $this->__table = $this->table;
		$this->primaryKey = 'attachment_id';
		$this->__keyParam = $this->primaryKey.'-';
		$this->hidden = array();
		
        // set fields
        $this->__fields   = array($this->primaryKey, 'attachment_type_id','attribute_code', "entity_type_id", "entity_id", 'title', 'content', "file", 'thumb','compressed_file','mobile_file','is_active', 'data_packet', 'created_at', 'updated_at', 'deleted_at');
	}


	/**
     * updateAttachmentByEntityID
     * @param int entity_id
     * @return Query
     */

    public function updateAttachmentByEntityID($entity_id,$attachments=array(),$featured=0,$attribute_code='gallery_items') {
		
		if(count($attachments)>0 && $entity_id!=0) {
			$this
			->where("entity_id","=",$entity_id)
			->where("attribute_code","=",$attribute_code)
			->update(array("is_active"=>0));
			foreach($attachments as $attachment){
					$data = array();
					$data['entity_id'] = $entity_id;
					$data['is_active'] = '1';
					if($featured==$attachment){
						 $data['is_featured'] = 1;
						 $this
						->where("is_featured","=",1)
						->where("entity_id","=",$entity_id)
						->update(array("is_featured"=>0));
					}
					
					$attCount = $this
					->where("attachment_id","=",$attachment)
					->where("entity_id","=",$entity_id)
					->count();
					if($attCount>0){
						 $this
						->where("attachment_id","=",$attachment)
						->where("entity_id","=",$entity_id)
						->update($data);
					}else{ 
						$this
						->where("attachment_id","=",$attachment)
						->where("entity_id","=",0)
						->update($data);
					}
			}
		}
    }	
	
	/**
     * getAttachmentByEntityID
     * @param int entity_id
     * @return Query
     */
    public function getAttachmentByEntityID($entity_id,$attachment_dir="",$attribute_code='gallery_items') {
		 $attachments_array = array();
		 $attachments =
			$this->where("entity_id","=",$entity_id)
				->where("attribute_code","=",$attribute_code)
				->where("is_active","=",'1')
				->whereNull("deleted_at")
				->get();
		
		if($attachments) {
			// load models
			
			$pModel = $this->__modelPath."PLAttachmentType";
			$pModel = new $pModel;
			try{
				
				foreach($attachments as $attachment){
					//print_r($attachment);die;
					$e1 = $pModel->getData($attachment->attachment_type_id);
					// unset unrequired
					unset($attachment->attachment_type_id, $attachment->updated_at, $attachment->deleted_at);
					// set thumbnail path
					if(!empty($attachment->thumb)) {
						//$data->thumb = \URL::to($attachment_dir.$data->thumb);
						//$attachment->thumb = $attachment_dir.$attachment->thumb;
						$attachment->thumb = \URL::to($attachment_dir.$attachment->thumb);
					}

					// set file path
					//$data->file = \URL::to($attachment_dir.$data->file);
					//$attachment->file = $attachment_dir.$attachment->file;
					$attachment->file = \URL::to($attachment_dir.$attachment->file);

                    if(!empty($attachment->compressed_file)){
                        $attachment->compressed_file = \URL::to($attachment_dir.$attachment->compressed_file);
                    }

                    if(!empty($attachment->mobile_file)){
                        $attachment->mobile_file = \URL::to($attachment_dir.$attachment->mobile_file);
                    }

					// set new
					$attachment->attachment_type = $e1;
					$attachments_array[] = $attachment;
				}
			} catch(Exception $e) {
				//unset($data);
			}
		}
		return $attachments_array;
    }

		
	/**
     * getData
     * @param int pk_id
     * @return Query
     */
    public function getData($pk_id = 0, $attachment_dir = "") {
		$data = $this->get($pk_id);
		
		if($data !== FALSE) {
			// load models
			$pModel = $this->__modelPath."PLAttachmentType";
			$pModel = new $pModel;
			try{
				// entity 1
				$e1 = $pModel->getData($data->attachment_type_id);
				// unset unrequired
				unset($data->attachment_type_id, $data->updated_at, $data->deleted_at);
				// set thumbnail path
				if($data->thumb !== NULL) {
					//$data->thumb = \URL::to($attachment_dir.$data->thumb);
					$data->thumb = $attachment_dir.$data->thumb;
				}
				// set file path
				//$data->file = \URL::to($attachment_dir.$data->file);
				$data->file = $attachment_dir.$data->file;
				// set new
				$data->attachment_type = $e1;
				
			} catch(Exception $e) {
				//unset($data);
			}
		}
		return $data;
    }
	
	
	/**
     * getData
     * @param int pk_id
     * @return Query
     */
    public function getMiniData($pk_id = 0, $attachment_dir = "") {		
		$data = $this->get($pk_id);
		if($data !== FALSE) {
			// load models
			$pModel = $this->__modelPath."PLAttachmentType";
			$pModel = new $pModel;
			// entity 1
			$e1 = $pModel->getMiniData($data->attachment_type_id);
			$data2 = (object)array();
			$data2->{$this->primaryKey} = $data->{$this->primaryKey};
			
			// set thumbnail path
			if($data->thumb !== NULL) {
				//$data->thumb = \URL::to($attachment_dir.$data->thumb);
				$data2->thumb = $attachment_dir.$data->thumb;
			}
			// set file path
			//$data2->file = \URL::to($attachment_dir.$data->file);
			$data2->file = $attachment_dir.$data->file;
			// set new
			$data2->attachment_type = $e1;
			
			unset($data);
			$data = $data2;
		}
		return $data;
    }
	
	
	/**
     * generateThumb
     * @param string $dir
     * @return Image
    */
	public function generateThumb_old($path = NULL, $filename = NULL, $size = NULL, $prefix = "thumb-"){
		$final_path = NULL;
		if(!is_null($path) && !is_null($filename) && !is_null($size)){
			// open an image file
			$img = \Image::make($path.$filename);
			// resize image instance
			$size = explode('x', $size);
			//$img->resize($size[0], $size[0]); // creates image {width}x{height}
			$img->fit($size[0]); // fits image as per width
			// insert a watermark
			//$img->insert($path.$prefix.$filename);
			// save image in desired format, and return path
			if($img->save($path.$prefix.$filename)) {
				$final_path = $path.$prefix.$filename;
			}
		}


		return $final_path;
	}

	public function generateThumb($path = NULL, $filename = NULL, $size = NULL, $prefix = "thumb-"){

		$final_path = NULL;
		if(!is_null($path) && !is_null($filename) && !is_null($size)){

			// open an image file
			$img = \Image::make($path."/".$filename);

			// resize image instance
			$size = explode('x', $size);
			//$img->resize($size[0], $size[0]); // creates image {width}x{height}
			$img->fit($size[0]); // fits image as per width
			// insert a watermark
			//$img->insert($path.$prefix.$filename);
			// save image in desired format, and return path
			if($img->save($path."/".$prefix.$filename)) {
				$final_path = $path."/".$prefix.$filename;
			}

		}
		return $final_path;
	}

	/**
	 * @param $entity_id
	 */
	public function deleteAttachmentByEntityID($entity_id){
		$this->where('entity_id', $entity_id)
		->delete();  // update the record in the DB.
	}

    /**
     * @param null $path
     * @param null $filename
     * @param null $compress_path
     * @param $prefix
     * @return null|string
     */
	public function compressImage($path = NULL, $filename = NULL,$compress_path = NULL, $prefix){

        $final_path = NULL;
        if(!is_null($path) && !is_null($filename) && !is_null($compress_path)){

            // open an image file
            $img = \Image::make($path."/".$filename);

            // save image in desired format, and return path
            if($img->save($compress_path.$prefix.$filename,60)) {
                $final_path = $compress_path.$prefix.$filename;
            }

        }
        return $final_path;
    }

    /**
     * Create image for mobile app
     * with calculating aspect ratio
     * @param null $path
     * @param null $filename
     * @param null $compress_path
     * @param $prefix
     * @return null|string
     */
    public function createMobileImage($path = NULL, $filename = NULL,$compress_path = NULL, $prefix){

        $final_path = NULL;
        if(!is_null($path) && !is_null($filename) && !is_null($compress_path)){

            // open an image file
            $img = \Image::make($path."/".$filename);

            $width = $img->width();
            $height = $img->height();
            $mobile_size = $new_height = config('constants.MAX_MOBILE_IMAGE_SIZE');

            if($width >= $mobile_size || $height >= $mobile_size){

                if($height > $width){
                    $img->resize(null,$mobile_size, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                else{
                    $img->resize($mobile_size,null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }

                // save image in desired format, and return path
                if($img->save($compress_path.$prefix.$filename)) {
                    $final_path = $compress_path.$prefix.$filename;
                }

            }else{

                // save image in medium compress format
                if($img->save($compress_path.$prefix.$filename,60)) {
                    $final_path = $compress_path.$prefix.$filename;
                }
            }

        }
        return $final_path;
    }

    /**
     * @param $entity_id
     * @return Query|array
     */
    public function getAttachmentByEntity($entity_id)
    {
       $attachments = $this->getAttachmentByEntityID($entity_id);

       if(count($attachments) >0 )
       {
           $return = array();

           foreach($attachments as $attachment)
           {
               $images = new \StdClass();
               $images->attachment_id = $attachment->attachment_id;
               $images->entity_id = $attachment->entity_id;
               $images->file = $attachment->file;
               $images->thumb = $attachment->thumb;
               $images->compressed_file = $attachment->compressed_file;
               $images->mobile_file = $attachment->mobile_file;

               $return[] = $images;
           }

           return $return;
       }
       return $attachments;
    }

    /**
     * @param $attachment_id
     * @return \StdClass
     */
    public function getAttachmentGallery($attachment_id)
    {
        $image = new \StdClass();
        $data = $this->get($attachment_id);
        if(isset($data->attachment_id)){

            $image->attachment_id = $data->attachment_id;
            $image->entity_id = $data->entity_id;

            $image->file =  !empty($data->file) ? \URL::to($data->file) : $data->file;
            $image->thumb =  !empty($data->thumb) ? \URL::to($data->thumb) : $data->thumb;
            $image->compressed_file =  !empty($data->compressed_file) ? \URL::to($data->compressed_file) : $data->compressed_file;
            $image->mobile_file =  !empty($data->mobile_file) ? \URL::to($data->mobile_file) : $data->mobile_file;


        } //echo "<pre>"; print_r($image); exit;
       return $image;

    }

    public function updateAttachmentByEntity($attachment_ids,$entity_id)
    {
        $row = \DB::select("Update $this->table set entity_id = $entity_id WHERE $this->primaryKey IN ($attachment_ids)");
        return isset($row) ? $row : false;
    }

    /**
     * @param $data
     * @return \StdClass
     */
    public function getAttachmentBasicInfo($data)
    {
        $image = new \StdClass();
        if(isset($data->attachment_id)){
            $image->attachment_id = $data->attachment_id;
            $image->entity_id = $data->entity_id;

            $image->file =  !empty($data->file) ? \URL::to($data->file) : $data->file;
            $image->thumb =  !empty($data->thumb) ? \URL::to($data->thumb) : $data->thumb;
            $image->compressed_file =  !empty($data->compressed_file) ? \URL::to($data->compressed_file) : $data->compressed_file;
            $image->mobile_file =  !empty($data->mobile_file) ? \URL::to($data->mobile_file) : $data->mobile_file;

        }

        return $image;
    }

}