<?php
   /*
   Plugin Name: IIIF Media
   Description: A plugin that adds an 'Insert from Course Collection' link to the Add Media window. This course collection is aggregated from a collection of IIIF manifests.  After manifests have been added to the Settings -> Media page, you may select images from these manifests by clicking on 'Insert from Course Collection' in the Add Media dialog.
   Version: 1.0
   License: GPL2
   */


require_once('manifest.class.php');



add_action('admin_enqueue_scripts', function(){
    //wp_enqueue_script( 'iiif-media-tab', plugin_dir_url( __FILE__ ) . '/js/mytab.js', array( 'jquery' ), '', true );
    //$data = array('site_url' => site_url(),'plugin_url' => plugin_dir_url( __FILE__ ),'ajax_url' => admin_url( 'admin-ajax.php' ));
    //wp_localize_script( 'iiif-media-tab', 'iiifvars', $data );
    
    wp_enqueue_style( 'iiif-media', plugins_url( '/css/style.css', __FILE__ ) );    

});






/*****************
* This adds a new button above the editor next to 'insert media'

function add_iiif_media_button() {
    echo '<a href="#" id="insert-iiif-media" class="button">Course Collection</a>';
}

add_action('media_buttons', 'add_iiif_media_button', 15);

*/


/*****************
* This adds a new link on the left hand side of the media uploader

function iiifsearch_add_mediaupload_tab($settings) {
  $settings['tabs'] = array('iiifsearch' => 'Insert from Course Collection');
  return $settings;
}
add_filter('media_view_settings', 'iiifsearch_add_mediaupload_tab');

*******************/



// Register and define the settings
add_action('admin_init', 'iiifmedia_admin_init');


function iiifmedia_admin_init(){
	register_setting(
		'media',                 // settings page
		'iiifmedia_options',          // option name
		'iiifmedia_validate_options'  // validation callback
	);
	
	add_settings_field(
		'iiifmedia_manifests',      // id
		'IIIF Manifests',              // setting title
		'iiifmedia_setting_input',    // display callback
		'media',                 // settings page
		'default'                  // settings section
	);

}

// Display and fill the form field
function iiifmedia_setting_input() {
	// get option 'iiif_manifests' value from the database
	if($options = get_option( 'iiifmedia_options' )) { $value = $options['iiif_manifests']; }
	else { $value = ""; }

	?>
	<textarea style='width:100%;height:300px;' id='iiif_manifests' name='iiifmedia_options[iiif_manifests]'><?php echo esc_attr( $value ); ?></textarea>
	<?php
}

/*
if(isset($_POST['iiifmedia_options'])) {
  
  
  if($options = get_option( 'iiifmedia_options' )) { 
  
	$obj = array();
	$manifestlist = $options['iiif_manifests'];
     
        $manifests = explode("\n",$manifestlist);

	foreach($manifests as $url) {
	  $url = trim(str_replace('https','http',$url));
	  $obj[] = parseManifest($url);
	}

	//$data = json_encode($obj);
	//update_option('iiif_json', $data);
 
  }
    
  
}
*/


// Validate user input and return validated data
function iiifmedia_validate_options( $input ) {
	//echo $input['iiif_manifests'];
	//$valid = array();
	//$valid['iiif_manifests'] = $input['iiif_manifests'];
	//return $valid;
	return $input;
}









/********************************
* Add a new link to the Add media dialog
*********************************/

function iiifsearch_upload_tab($tabs) {
    $tabs['iiifsearch'] = "Insert from Course Collection";
    return $tabs;
}
add_filter('media_upload_tabs', 'iiifsearch_upload_tab');



/********************************
* Include js and css in the dialog iframe 
* and call function to populate with html
*********************************/

function iiifsearch_add_upload_form() {
    wp_register_script( 'iiifsearch', plugins_url( '/js/iiifsearch.js', __FILE__ ) );
    wp_enqueue_script( 'iiifsearch' );
    $data = array(
      'site_url' => site_url(),
      'ajax_url' => admin_url( 'admin-ajax.php' )
    );
    wp_localize_script( 'iiifsearch', 'iiifvars', $data );
    wp_register_style('iiifsearch-css', plugins_url('css/style.css',__FILE__ ));
    wp_enqueue_style('iiifsearch-css');

    wp_iframe('iiifsearch_upload_tab_content');

}
add_action('media_upload_iiifsearch', 'iiifsearch_add_upload_form');










/********************************
* populate the iframe with the search interface 
html
*********************************/

function iiifsearch_upload_tab_content() {
  $wait_icon = plugin_dir_url( __FILE__ ) . 'images/loading.gif';
  
  $html = media_upload_header();
  $html .= "&nbsp;&nbsp; Width: <input type='radio' name='width' value='250'/>thumbnail <input type='radio' checked='checked' name='width' value='450'/>medium <input type='radio' name='width' value='800'/>large </p>";
?>
<div class='media-frame-content'>
  <div class='attachments-browser'>
    <div class='media-toolbar'>toolbar</div>
    <ul class='iiif-media'></ul>
    <div class='media-sidebar'><h3>Media details</h3>
      <div class='attachment-details'>
      
      			<input type='hidden' id='attachment-details-iiif' value=''/>
      
		 	<p><label for='attachment-details-title' class='name'>Title</label><br />
			<textarea id='attachment-details-title'></textarea></p>

		 	<p><label for='attachment-details-caption' class='name'>Caption</label><br />
			<textarea id='attachment-details-caption'></textarea></p>

		 	<p><label for='attachment-details-size' class='name'>Size</label><br />
			<select id='attachment-details-size'>
			  <option value='240'>Small (240px)</option>
			  <option value='500'>Medium (500px)</option>
			  <option value='800'>Large (800px)</option>
			  <option value='1200'>Larger (1200px)</option>
			</select></p>
			<p><input type='checkbox' id='attachment-details-featured'/> Featured Image</p>
			<p><button id="iiif-insert" class='button button-large'>Insert</button></p>
      
      </div>
    </div>
  </div>
</div> <!-- /media-frame-content -->
<?php
}






/********************************
* interface html
********************************

function include_iiif_media_button_js_file() {
    wp_register_script( 'iiifmedia', plugins_url( '/js/iiifmedia.js', __FILE__ ) );
    wp_enqueue_script( 'iiifmedia' ,array('jquery'), '1.0', true);

}
add_action('wp_enqueue_media', 'include_iiif_media_button_js_file');
*/

function getManifestData($url) {
  $arrContextOptions=array( "ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false));  
  $data = file_get_contents($url, false, stream_context_create($arrContextOptions));
  return json_decode($data);
}



function parseManifest($url, $id = NULL) {
  if($data = getManifestData(trim($url))) {

    if($data->{'@type'} == 'sc:Manifest') {
    	  $manifest = new StdClass();
    	  
          $manifest->id = $data->{'@id'};
    
	  $title = $data->label;

	  if(is_array($title)) {
	  	$t = "";
	  	foreach($title as $tit) { $t .= $tit." "; }
	  	$title = $t;
	  	}
	  $manifest->title = addslashes((string)$title);
	  
	  $canvases = $data->sequences[0]->canvases;
          $manifest->canvases = array();
          
          foreach($canvases as $canvas) {
            $o = new StdClass();
            
            if($canvas->label) { $o->title = $canvas->label; }
            else { $o->title = $title; }
             
            $images = $canvas->images;
            $o->id = $images[0]->resource->service->{'@id'};

            if(isset($id)) {
              if($o->id == $id) { $manifest->canvases[] = $o; }
            }
            else { $manifest->canvases[] = $o; }

          }

    }
    return $manifest;
  }
  else { return array('not a valid manifest'); }
}









/********************************
* expose json interface (easier than trying to do jsonp)
********************************/

if(isset($_GET['iiif'])) {
  $items = array();

  //$manifests = file(plugins_url( './', __FILE__ )."/manifests.txt");
  
  if($options = get_option( 'iiifmedia_options' )) { 
     $manifestlist = $options['iiif_manifests']; 
  }

  $manifests = explode("\n",$manifestlist);
 
  foreach($manifests as $url) {
    $url = trim(str_replace('https','http',$url));
    // if an id is provided, just deliver that id in parsemanifest
    if(isset($_GET['id'])) { $items = array_merge(parseManifest($url, $_GET['id']),$items); }
    else { $items = array_merge(parseManifest($url),$items); }
  }
  
  header('Content-Type: application/json');
  echo json_encode($items);
  die();
}












function crb_insert_attachment_from_url($url, $parent_post_id = null) {

	if( !class_exists( 'WP_Http' ) )
		include_once( ABSPATH . WPINC . '/class-http.php' );


	$http = new WP_Http();
	$response = $http->request( $url );
	if( $response['response']['code'] != 200 ) {
		return false;
	}
//die('1');
	$upload = wp_upload_bits( basename($url), null, $response['body'] );
	if( !empty( $upload['error'] ) ) {
		return false;
	}
//die('2');
	$file_path = $upload['file'];
	$file_name = basename( $file_path );
	$file_type = wp_check_filetype( $file_name, null );
	$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
	$wp_upload_dir = wp_upload_dir();

	$post_info = array(
		'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
		'post_mime_type' => $file_type['type'],
		'post_title'     => $attachment_title,
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	// Create the attachment
	$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );

	// Include image.php
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	// Define attachment metadata
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );

	// Assign metadata to attachment
	wp_update_attachment_metadata( $attach_id,  $attach_data );

	return $attach_id;

}










/*********************** AJAX ***************************/



/*************************************
*  get details of one manifest
*************************************/

add_action( 'wp_ajax_manifest', 'iiif_get_manifest' );

function iiif_get_manifest() {
	if(isset($_GET['url'])) {
	  $url = $_GET['url'];
	  
	  if(strstr($url,'manifest=')) {
	     $x = parse_url($url);
	     $parts = parse_url($url);
	     parse_str($parts['query'], $query);
	     $url = $query['manifest'];
	   }

	  $m = new Manifest($url);
	  header('Content-Type: application/json');
	  echo json_encode($m);
	}
	$m = array('error'=>'url GET variable needed');
	wp_die(); // this is required to terminate immediately and return a proper response
}


add_action( 'wp_ajax_manifests', 'iiif_get_manifests' );


/*************************************
* get a list of all the manifests
*************************************/
function iiif_get_manifests() {

	if($options = get_option('iiifmedia_options')) { 
	  $value = nl2br($options['iiif_manifests']);
	  $array = explode("<br />",$value);

	  foreach($array as &$a) { $a = trim($a); }
	    header('Content-Type: application/json');
	    echo json_encode($array);
	    die();
	  }

	wp_die(); // this is required to terminate immediately and return a proper response
}



/*************************************
* if you click on a iiif image, set it as featured image
*************************************/

add_action( 'wp_ajax_media_featured', 'iiif_media_featured' );

function iiif_media_featured() {

	$data = $_POST;

	//$url = "http://iiif-cloud.princeton.edu/iiif/2/96%2Fd5%2Fbe%2F96d5bee308ac42c4b184e9cbbfff2dc7%2Fintermediate_file/full/1200,/0/default.jpg";
	$url = "http://iiif-cloud.princeton.edu/iiif/2/9d%2F3e%2F66%2F9d3e665520bb4207b576ee352b91497a%2Fintermediate_file/full/1200,/0/default.jpg";
	$post_id = 342;

	$data = array('message'=>'success');
	$data['attachment_id'] = crb_insert_attachment_from_url($url, $parent_post_id);
	$data['thumb'] = wp_get_attachment_image_src($data['attachment_id']);
	
	header('Content-Type: application/json');
	echo json_encode($data);
	wp_die();
}



/*************************************
*  add a manifest
*************************************/

add_action( 'wp_ajax_addmanifest', 'iiif_add_manifest' );

function iiif_add_manifest() {

	if(isset($_GET['url'])) {
	  $m = new Manifest($_GET['url']);
	  header('Content-Type: application/json');
	  echo json_encode($m);
	}
	$m = array('error'=>'url GET variable needed');
	wp_die();
}


add_action( 'wp_ajax_manifests', 'iiif_get_manifests' );

?>
