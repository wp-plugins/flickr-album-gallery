<?php
/**
 * Plugin Name: Flickr Album Gallery
 * Version: 1.0
 * Description: Simply easy to publish your Flickr photo albums on your WrodPress blog site
 * Author: Weblizar
 * Author URI: https://weblizar.com/plugins/flickr-album-gallery-pro/
 * Plugin URI: https://weblizar.com/plugins/flickr-album-gallery-pro/
 */
 
/**
 * Constant Variable
 */
define("FAG_TEXT_DOMAIN", "weblizar_fag");
define("FAG_PLUGIN_URL", plugin_dir_url(__FILE__));

/**
 * Flickr album gallery Plugin Class
 */
 class FlickrAlbumGallery {

	public function __construct() {
		if (is_admin()) {
			add_action('plugins_loaded', array(&$this, 'FAG_Translate'), 1);
			add_action('init', array(&$this, 'FlickrAlbumGallery_CPT'), 1);
			add_action('add_meta_boxes', array(&$this, 'Add_all_fag_meta_boxes'));
            add_action('admin_init', array(&$this, 'Add_all_fag_meta_boxes'), 1);
			add_action('save_post', array(&$this, 'Save_fag_meta_box_save'), 9, 1);
		}
	}
	
	/**
	 * Translate Plugin
	 */
	public function FAG_Translate() {
		load_plugin_textdomain('weblizar_fag', FALSE, dirname( plugin_basename(__FILE__)).'/lang/' );
	}
	
	// 2 - Register Flickr Album Custom Post Type
	public function FlickrAlbumGallery_CPT() {
		$labels = array(
			'name' => _x( 'Flickr Album Gallery', 'fa_gallery' ),
			'singular_name' => _x( 'Flickr Album Gallery', 'fa_gallery' ),
			'add_new' => _x( 'Add New Gallery', 'fa_gallery' ),
			'add_new_item' => _x( 'Add New Gallery', 'fa_gallery' ),
			'edit_item' => _x( 'Edit Photo Gallery', 'fa_gallery' ),
			'new_item' => _x( 'New Gallery', 'fa_gallery' ),
			'view_item' => _x( 'View Gallery', 'fa_gallery' ),
			'search_items' => _x( 'Search Galleries', 'fa_gallery' ),
			'not_found' => _x( 'No galleries found', 'fa_gallery' ),
			'not_found_in_trash' => _x( 'No galleries found in Trash', 'fa_gallery' ),
			'parent_item_colon' => _x( 'Parent Gallery:', 'fa_gallery' ),
			'all_items' => __( 'All Galleries', 'fa_gallery' ),
			'menu_name' => _x( 'Flickr Album Gallery', 'fa_gallery' ),
		);

		$args = array(
			'labels' => $labels,
			'hierarchical' => false,
			'supports' => array( 'title', ),
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 10,
			'menu_icon' => 'dashicons-format-gallery',
			'show_in_nav_menus' => false,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => false,
			'capability_type' => 'post'
		);

        register_post_type( 'fa_gallery', $args );
        add_filter( 'manage_edit-fa_gallery', array(&$this, 'fa_gallery_columns' )) ;
        add_action( 'manage_fa_gallery_posts_custom_column', array(&$this, 'fa_gallery_manage_columns' ), 10, 2 );
	}
	
	function fa_gallery_columns( $columns ){
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'Gallery' ),
            'shortcode' => __( 'Album Gallery Shortcode' ),
            'date' => __( 'Date' )
        );
        return $columns;
    }
	
    function fa_gallery_manage_columns( $column, $post_id ){
        global $post;
        switch( $column ) {
          case 'shortcode' :
            echo '<input type="text" value="[FAG id='.$post_id.']" readonly="readonly" />';
            break;
          default :
            break;
        }
    }
	
	// 3 - Meta Box Creator
	public function Add_all_fag_meta_boxes() {
		add_meta_box( __('Add Images', FAG_TEXT_DOMAIN), __('Add Images', FAG_TEXT_DOMAIN), array(&$this, 'fag_meta_box_form_function'), 'fa_gallery', 'normal', 'low' );
		add_meta_box ( __('Flickr Album Gallery Shortcode', FAG_TEXT_DOMAIN), __('Flickr Album Gallery Shortcode', FAG_TEXT_DOMAIN), array(&$this, 'fag_shortcode_meta_box_form_function'), 'fa_gallery', 'side', 'low');
		add_meta_box(__('Rate Us', FAG_TEXT_DOMAIN) , __('Rate Us', FAG_TEXT_DOMAIN), array($this, 'Rate_us_meta_box_function'), 'fa_gallery', 'side', 'low');
		add_meta_box(__('Upgrade To Pro Version', FAG_TEXT_DOMAIN) , __('Upgrade To Pro Version', FAG_TEXT_DOMAIN), array($this, 'Upgrade_to_meta_box_function'), 'fa_gallery', 'side', 'low');
		add_meta_box(__('Pro Features', FAG_TEXT_DOMAIN) , __('Pro Features', FAG_TEXT_DOMAIN), array($this, 'Pro_freatures_meta_box_function'), 'fa_gallery', 'side', 'low');
    }
	
	/**
	 * Rate Us Meta Box
	 */
	public function Rate_us_meta_box_function() { ?>
		<style>
		.fag-rate-us span.dashicons{
			width: 30px;
			height: 30px;
		}
		.fag-rate-us span.dashicons-star-filled:before {
			content: "\f155";
			font-size: 30px;
		}
		</style>
		<div align="center">
			<p>Please Review & Rate Us On WordPress</p>
			<a class="upgrade-to-pro-demo .fag-rate-us" style=" text-decoration: none; height: 40px; width: 40px;" href="http://wordpress.org/support/view/plugin-reviews/flickr-album-gallery" target="_blank">
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
			</a>
		</div>
		<div class="upgrade-to-pro-demo" style="text-align:center;margin-bottom:10px;margin-top:10px;">
			<a href="http://wordpress.org/support/view/plugin-reviews/flickr-album-gallery" target="_blank" class="button button-primary button-hero">RATE US</a>
		</div>
		<?php
	}
	
	/**
	 * Shortcode Meta Box
	 */
	public function fag_shortcode_meta_box_form_function() { ?>
		<p><?php _e("Use below shortcode in any Page/Post to publish your Flickr Album Gallery", FAG_TEXT_DOMAIN);?></p>
		<input readonly="readonly" type="text" value="<?php echo "[FAG id=".get_the_ID()."]"; ?>"> <?php
	}
	
	/**
	 * Upgrade To Meta Box
	 */
	public function Upgrade_to_meta_box_function() { ?>
		<div class="upgrade-to-pro-demo" style="text-align:center;margin-bottom:10px;margin-top:10px;">
			<a href="http://demo.weblizar.com/flickr-album-gallery-pro/" target="_blank" class="button button-primary button-hero">View Live Demo</a>
		</div>
		<div class="upgrade-to-pro" style="text-align:center;margin-bottom:10px;">
			<a href="http://weblizar.com/plugins/flickr-album-gallery-pro/" target="_blank" class="button button-primary button-hero">Upgrade To Pro</a>
		</div><?php
	}
	
	/**
	 * Pro Features Meta Box
	 */
	public function Pro_freatures_meta_box_function() { ?>
		<ul>
			<li class="plan-feature">Responsive Design</li>
			<li class="plan-feature">Gallery Layout</li>
			<li class="plan-feature">Unlimited Hover Color</li>
			<li class="plan-feature">10 Types of Hover Color Opacity</li>
			<li class="plan-feature">All Gallery Shortcode</li>
			<li class="plan-feature">Each Gallery Unique Shortcode</li>
			<li class="plan-feature">8 Hover Animation</li>
			<li class="plan-feature">6 Gallery Design Layout</li>
			<li class="plan-feature">8 Light Box Slider</li>
			<li class="plan-feature">Shortcode Button For Post & Page</li>
			<li class="plan-feature">Unique Settings For Each Gallery</li>
			<li class="plan-feature">Hide/Show Gallery Title</li>
		</ul>
		<?php
	}
	
	
	/**
	 * Gallery API Key & Album ID Form
	 */
	public function fag_meta_box_form_function($post) {
		
		$FAG_Settings = unserialize(get_post_meta( $post->ID, 'fag_settings', true));
		if(count($FAG_Settings[0])) {
			$FAG_API_KEY = $FAG_Settings[0]['fag_api_key'];
			$FAG_Album_ID = $FAG_Settings[0]['fag_album_id'];
			$FAG_Show_Title = $FAG_Settings[0]['fag_show_title'];
		}
		
		/**
		 * Default Settings
		 */
		 if(!isset($FAG_API_KEY)) {
			$FAG_API_KEY = "e54499be5aedef32dccbf89df9eaf921";
		 }
		 
		 if(!isset($FAG_Album_ID)) {
			$FAG_Album_ID = "72157645975425037";
		 }
		 
		 if(!isset($FAG_Show_Title)) {
			$FAG_Show_Title = "yes";
		 }
		?>
		<p><?php _e("Enter Flickr API Key", FAG_TEXT_DOMAIN ); ?></p>
		<input type="text" style="width:50%;" name="flickr-api-key" id="flickr-api-key" value="<?php echo $FAG_API_KEY; ?>"> <a title="Get your flickr account API Key"href="http://weblizar.com/get-flickr-api-key/" target="_blank"><?php _e("Get Your API Key", FAG_TEXT_DOMAIN ); ?></a>
		
		<p><?php _e("Enter Flickr Album ID", FAG_TEXT_DOMAIN ); ?></p>
		<input type="text" style="width:50%;" name="flickr-album-id" id="flickr-album-id" value="<?php echo $FAG_Album_ID; ?>"> <a title="Get your flickr photo Album ID" href="http://weblizar.com/get-flickr-album-id/" target="_blank"><?php _e("Get Your Album ID", FAG_TEXT_DOMAIN ); ?></a>
		<br><br>
		
		
		<p><?php _e("Show Gallery Title", FAG_TEXT_DOMAIN ); ?></p>
		<input type="radio" name="fag-show-title" id="fag-show-title" value="yes" <?php if($FAG_Show_Title == 'yes' ) echo "checked"; ?>>  <i class="fa fa-check fa-2x"></i> Yes
		<input type="radio" name="fag-show-title" id="fag-show-title" value="no" <?php if($FAG_Show_Title == 'no' ) echo "checked"; ?>>  <i class="fa fa-times fa-2x"></i> NO
		<br><br>
		
		<hr>
		<h3>Get more Image Sliders, Album Layouts, Hover Animations, Multiple Album Shortcodes. View details <a href="http://weblizar.com/plugins/flickr-album-gallery-pro/" target="_blank">Here</a></h3>
		<h3>Check Flicker Album Pro Details & <a href="http://demo.weblizar.com/flickr-album-gallery-pro/" target="_blank">Live Demo</a></h3>
		<?php
	}
	
	/**
	 * FAG Save
	 */
	public function Save_fag_meta_box_save($PostID) {
		if(isset($_POST['flickr-api-key']) && isset($_POST['flickr-album-id'])) {
			$FAG_API_KEY = $_POST['flickr-api-key'];
			$FAG_Album_ID = $_POST['flickr-album-id'];
			$FAG_Show_Title = $_POST['fag-show-title'];
			$FAGArray[] = array(
				'fag_api_key' => $FAG_API_KEY,
				'fag_album_id' => $FAG_Album_ID,
				'fag_show_title' => $FAG_Show_Title
			);
			update_post_meta($PostID, 'fag_settings', serialize($FAGArray));
		}
	}
}// end of class

global $FlickrAlbumGallery;
$FlickrAlbumGallery = new FlickrAlbumGallery();

/**
 * Flickr Album Gallery Shortcode Detect Function
 */
function FlickrAlbumGalleryShortCodeDetect() {
    global $wp_query;
    $Posts = $wp_query->posts;
    $Pattern = get_shortcode_regex();

    //foreach ($Posts as $Post) {
        //if (   preg_match_all( '/'. $Pattern .'/s', $Post->post_content, $Matches ) && array_key_exists( 2, $Matches ) && in_array( 'FAG', $Matches[2] ) ) {
		//if ( strpos($Post->post_content, 'FAG' ) ) {
			//JS
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'fag-bootstrap-min-js', plugins_url('js/bootstrap.min.js', __FILE__ ), array('jquery'), false, true );
			wp_enqueue_script( 'fag-imagesloaded-pkgd-min-js', plugins_url('js/imagesloaded.pkgd.min.js', __FILE__ ), array('jquery'), false, true );
			wp_enqueue_script( 'fag-jquery-blueimp-gallery-min-js', plugins_url('js/jquery.blueimp-gallery.min.js', __FILE__ ), array('jquery'), false, true );
			wp_enqueue_script( 'fag-bootstrap-image-gallery-min-js', plugins_url('js/bootstrap-image-gallery.min.js', __FILE__ ), array('jquery'), false, true );
			wp_enqueue_script( 'fag-flickr-jquery-js', plugins_url('js/flickr-jquery.js', __FILE__ ), array('jquery'), false, true );
			
			//CSS
			wp_enqueue_style('fag-bootstrap-min-css', FAG_PLUGIN_URL.'css/bootstrap.min.css');
			wp_enqueue_style('fag-blueimp-gallery-min-css', FAG_PLUGIN_URL.'css/blueimp-gallery.min.css');
			wp_enqueue_style('fag-site-css', FAG_PLUGIN_URL.'css/site.css');
			wp_enqueue_style('fag-font-awesome-latest', FAG_PLUGIN_URL.'css/font-awesome-latest/css/font-awesome.min.css');
			
            //break;
        //} //end of if
    //} //end of foreach
}
add_action( 'wp', 'FlickrAlbumGalleryShortCodeDetect' );

/**
 * Flickr Album gallery Short Code [FAG]
 */
require_once("flickr-album-gallery-short-code.php");

/**
 * Documentation Page
 */
add_action('admin_menu' , 'FAG_DOC_Menu_Function');
function FAG_DOC_Menu_Function() {
	add_submenu_page('edit.php?post_type=fa_gallery', __('Upgrade To Pro', FAG_TEXT_DOMAIN), __('Upgrade To Pro', FAG_TEXT_DOMAIN), 'administrator', 'flickr-docs', 'FAG_DOC_Page_Function');
}
function FAG_DOC_Page_Function(){ 
	wp_enqueue_script('bootstrap-min-js', FAG_PLUGIN_URL.'js/bootstrap.min.js');
	wp_enqueue_script('weblizar-tab-js', FAG_PLUGIN_URL .'js/option-js.js',array('jquery', 'media-upload', 'jquery-ui-sortable'));
	wp_enqueue_style('weblizar-option-style-css', FAG_PLUGIN_URL .'css/weblizar-option-style.css');
	wp_enqueue_style('op-bootstrap-css', FAG_PLUGIN_URL. 'css/bootstrap.min.css');
	wp_enqueue_style('weblizar-bootstrap-responsive-google', FAG_PLUGIN_URL .'css/bootstrap-responsive.css');
	wp_enqueue_style('font-awesome-min-css', FAG_PLUGIN_URL.'css/font-awesome-latest/css/font-awesome.min.css');
	wp_enqueue_style('Respo-pricing-table-css', FAG_PLUGIN_URL .'css/pricing-table-responsive.css');
	wp_enqueue_style('pricing-table-css', FAG_PLUGIN_URL .'css/pricing-table.css');
	wp_enqueue_style('fag-bootstrap-min-css', FAG_PLUGIN_URL.'css/bootstrap.dashboard.css');
	require_once("flicker-album-gallery-help.php");
}
?>