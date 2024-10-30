<?php
/**
 * Plugin Name: LH Media Mime Types
 * Plugin URI: http://lhero.org/portfolio/lh-media-mime-types/
 * Description: List the mime type in the media listing, and allow attachmenst to be filtered by mime type
 * Author: Peter Shaw
 * Author URI: http://shawfactor.com
 * Version: 1.01
 * Text Domain: lh_media_mime_type
 * Domain Path: /languages
*/

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!class_exists('LH_Media_mime_type_plugin')) {

class LH_Media_mime_type_plugin {
    
    private static $instance;

    static function return_plugin_namespace(){
        
        return 'lh_media_mime_type';
        
    }    


    // ADD NEW Mime Column
    
    public function manage_upload_columns($columns){
    	
        $columns['post_mime_type'] = __('Mime Type', self::return_plugin_namespace());
    
        return $columns;
    
    }

    public function manage_upload_sortable_columns($sortable_columns){
        
        $sortable_columns['post_mime_type'] = 'post_mime_type';
        return $sortable_columns;
        
    }

    // SHOW THE Mime column content
    public function manage_media_custom_column( $column, $post_id ) {
	    
		global $post;
		if ( 'post_mime_type' == $column ) {
		    
		    $url = add_query_arg( 'post_mime_type', $post->post_mime_type);
		    
		    	    echo "<a href=\"".$url."\">".$post->post_mime_type."</a>";
	    
		}
		
	}
	
	

    public function media_query($query) {
    
    global $pagenow,$wpdb;
    
        if ( $pagenow == 'upload.php' ){
    
            if (isset($_GET['post_mime_type']) && ($_GET['post_mime_type'] !="")){
        
                $sql = "SELECT ID FROM ".$wpdb->posts." WHERE post_mime_type= '".$_GET['post_mime_type']."'";
        
                $custom_ids = $wpdb->get_col($sql);

                $query->set( 'post__in', $custom_ids ); 
        
            }
    
        }

    return $query;

    }


    public function media_order($orderby) {
        
        global $pagenow,$wpdb;
    
        if ( $pagenow == 'upload.php' ){
        
            if (isset($_GET['orderby']) && ($_GET['orderby'] =='post_mime_type')){
        
                $orderby = $wpdb->posts.'.post_mime_type ';
    
                if ($_GET['order'] == 'desc'){
        
                    $orderby .= 'DESC';
        
                } else {
    
                    $orderby .= 'ASC';    
        
                }
        
            }
    
        }
    
        return $orderby;
        
    }

    public function plugin_init(){
        
        // make plugin translatable
        load_plugin_textdomain( 'lh_add_media_from_url', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
    
        //add the columns    
        add_filter('manage_upload_columns', array($this,'manage_upload_columns'),10,1);
        add_filter( 'manage_upload_sortable_columns', array($this,"manage_upload_sortable_columns"),10,1);
        add_action( 'manage_media_custom_column', array( $this, 'manage_media_custom_column' ), 10, 2 );
        
        //Modify the backend queries to enable query by post_mime_type
        add_action('pre_get_posts', array($this, 'media_query'));
        
        //Modify the backend queries to order by post_mime_type
        add_filter('posts_orderby', array($this, 'media_order'));
            
    }
	
	
	/**
     * Gets an instance of our plugin.
     *
     * using the singleton pattern
     */
     
    public static function get_instance(){
        
        if (null === self::$instance) {
            
            self::$instance = new self();
            
        }
 
        return self::$instance;
        
    }




    public function __construct() {
    
        //run our hooks on plugins loaded to as we may need checks       
        add_action( 'plugins_loaded', array($this,'plugin_init'));
    
    
    }

}

$lh_media_mime_type_instance = LH_Media_mime_type_plugin::get_instance();


}



?>