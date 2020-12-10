<?php
/* Manifest class for parsing manifests */


class Manifest {

  public $url;

  function __construct( $url ){
    $data = json_decode(file_get_contents(trim($url)));
    $this->url = $url;
    $this->title = $this->getItemLabel($data);
    $this->canvases = $this->getCanvases($data);
  }

  function getItemLabel($data) {
    return $this->stringOrFirstInArray($data->label);
  }


  function getCanvases($data) {

          if($canvases = $data->sequences[0]->canvases) {
         
		  foreach($canvases as $canvas) {
		  
		   if(isset($canvas->label)) { $o->title = $this->stringOrFirstInArray($canvas->label); }
		   else { $o->title = $this->title; }
		   $images = $canvas->images;
		   $cnt = 1;
		   foreach($images as $image) {
		   
		     $o = new StdClass();
		     $o->id = $image->resource->service->{'@id'};

		     $o->title = $this->stringOrFirstInArray($image->title);
		     if(isset($id)) {
		       if($o->id == $id) { $returnObj[] = $o; }
		     }
		     else { $returnObj[] = $o; }
		   }
		  }
	  }
     return $returnObj;
  }


  function stringOrFirstInArray($val) {
       if(is_array($val)) {
	  $temp = "";
	  foreach($val as $v) { $temp .= $v." "; }
	  return $temp;
	  }
       return $val;
  }





}
