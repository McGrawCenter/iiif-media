<?php
   /*
   Plugin Name: IIIF Media
   Description: A plugin that adds a 'IIIF Manifests' link to the Add Media window. This course collection is aggregated from a collection of IIIF manifests.  After manifests have been added to Media -> IIIF Manifests, images from these manifests may be included in posts and pages by clicking on 'IIIF Manifests' in the Add Media dialog.
   Version: 1.0
   License: GPL2
   */
   
   
class IIIFMedia {

  function __construct() {
    
    add_action( 'admin_menu', array( $this, 'iiifmedia_add_media_options_page') , 11);
    // Register and define the settings
    add_action('admin_init', array( $this, 'iiifmedia_admin_init') );
    add_action( 'admin_enqueue_scripts', array( $this, 'iiifmedia_admin_scripts') );
    add_filter('media_upload_tabs', array( $this, 'iiifsearch_upload_tab') ); 
    add_action('media_upload_iiifsearch', array( $this, 'iiifsearch_add_upload_form') ); 
    /* ajax */
    add_action( 'wp_ajax_manifest', array( $this, 'iiif_get_manifest') );
    add_action( 'wp_ajax_manifests', array( $this, 'iiif_get_manifests') );
  }
  
  
  function iiifmedia_admin_scripts( $hook ) {
      wp_enqueue_style( 'iiifmediacss', plugins_url( '/css/style.css', __FILE__ ) );
      wp_enqueue_script( 'parser', plugin_dir_url( __FILE__ ) . 'js/manifest_parser.js', array(), '1.0' );
      wp_enqueue_script( 'iiifmedia', plugin_dir_url( __FILE__ ) . 'js/script.js', array(), '1.0' );
  }
 
  
  
  /*****************
  * 
  ****************************************************/

  function iiifmedia_add_media_options_page () {
	add_media_page(
		__( 'IIIF Manifests', 'textdomain' ),
		__( 'IIIF Manifests', 'textdomain' ),
		'manage_options',
		'iiif-media',
		array( $this, 'iiifmedia_media_menu_setting_input')
	);
  }  
  
  
  /*****************
  * 
  ****************************************************/
  function iiifmedia_media_menu_setting_input() {
	?>
	<div class='wrap'>
	  <h1>IIIF Manifests</h1>
	  <form name='iiifmanifestadd' action="?page=iiif-media" method="POST">
	  <p>
	    <label for="manifesturl">Add New</label>
	    <input type="text" class='regular-text' id="manifesturl" name="manifesturl" value="" placeholder="Manifest URL"/>
	    <input type='hidden' name='manifestobj' id='manifestobj'/>
	  </p>
	  <div id="manifestpreview" style="display:none;">
	     <input id='submit' class='button button-primary' style='margin:0 0 10px 10px; float:right;' type='submit' value='Add'/>
	     <div id="manifestpreview-content"></div>
	  </div>
	  </form>
	</div>
	<div id="mymanifests"  style='width:98%;'>
	<table class="wp-list-table widefat striped table-view-list">
	<thead>
	<tr>
		<th class="manage-column">Thumbnail</th>
		<th class="manage-column">Title/Description</th>
		<th class="manage-column"></th>
	</tr>
	</thead>
	  <?php
	  if($options = get_option( 'iiifmedia_manifests' )) {
	    
	    foreach($options as $o) {
	      $obj = json_decode( stripslashes($o) );
	      echo "<tr>";
	      echo "<td style='width:126px;'><img src='{$obj->images[0]->thumb}' width='120' /></td>";
	      echo "<td>{$obj->label}</td>";
	      echo "<td><button class='button button-primary' style='margin:0 0 10px 10px;'>Remove</button></td>";
	      echo "</tr>";
	    }
	  }
	  ?>
		
	</table>
	</div>

	<?php
	
  }

  /********************************
  * register setting and save
  *********************************/

  function iiifmedia_admin_init(){
	register_setting(
		'media',                 // settings page
		'iiifmedia_manifests',          // option name
		'iiifmedia_validate_options'  // validation callback
	);
	
	if(isset($_POST['manifesturl']) && isset($_POST['manifestobj'])) {
	//update_option('iiifmedia_manifests', array());
	  $options = get_option( 'iiifmedia_manifests' );
	  $options[$_POST['manifesturl']] = $_POST['manifestobj'];
	  update_option('iiifmedia_manifests', $options);
	}

  }  

  /********************************
  * Add a new link to the Add media dialog
  *********************************/

  function iiifsearch_upload_tab($tabs) {
      $tabs['iiifsearch'] = "IIIF Manifests";
      return $tabs;
  }
  
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
      wp_localize_script( 'iiifsearch', 'vars', $data );
      wp_register_style('iiifsearch-css', plugins_url('css/style.css',__FILE__ ));
      wp_enqueue_style('iiifsearch-css');

      wp_iframe(array( $this, 'iiifsearch_upload_tab_content') );

  }

  /*********************************
  * populate the iframe with the search interface html
  *********************************/

  function iiifsearch_upload_tab_content() {
    $wait_icon = plugin_dir_url( __FILE__ ) . 'images/loading.gif';
    $html = media_upload_header();
    $html .= "<div class='iiifimage-controls'><strong>Size</strong>: ";
    $html .= "<input type='radio' name='width' id='iiifimage-width-thm' value='250'/><label for='iiifimage-width-thm'>thumbnail</label>";
    $html .= "<input  type='radio' name='width' id='iiifimage-width-med'  value='450' checked='checked' /><label for='iiifimage-width-med'>medium</label>";
    $html .= "<input type='radio' name='width' id='iiifimage-width-lg'  value='800'/><label for='iiifimage-width-lg'>large</label>";
    //$html .= "<strong style='margin-left:20px;'>Align</strong>: <input type='radio' name='align' value='left' checked='checked'/>left <input  type='radio' name='align' value='center'/>center <input type='radio' name='align' value='right'/>right </div>";
    $html .= "<div id='iiifsearchresults'><img src='{$wait_icon}' width='80' height='80'/><br /></div>";

    echo $html;
  }
  
/*********************** AJAX ***************************/




  function iiif_get_manifest() {
	if(isset($_GET['url'])) {
	  $m = new Manifest($_GET['url']);
	  header('Content-Type: application/json');
	  echo json_encode($m);
	}
	$m = array('error'=>'url GET variable needed');
	wp_die(); // this is required to terminate immediately and return a proper response
  }




  function iiif_get_manifests() {
	if($options = get_option( 'iiifmedia_manifests' )) { 
	  foreach($options as &$option) { $option = json_decode(stripslashes($option)); }
	  header('Content-Type: application/json');
	  echo json_encode($options);
	  die();
	  }
	wp_die(); // this is required to terminate immediately and return a proper response
  } 
 

}   
   
new IIIFMedia();
