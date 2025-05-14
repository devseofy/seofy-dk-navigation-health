<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://seofy.dk
 * @since      1.0.0
 *
 * @package    Seofy_Dk_Navigation_Health
 * @subpackage Seofy_Dk_Navigation_Health/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Seofy_Dk_Navigation_Health
 * @subpackage Seofy_Dk_Navigation_Health/admin
 * @author     Jonard Aragon <jonardaragon@gmail.com>
 */
class Seofy_Dk_Navigation_Health_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		//add_action('init', array ( $this, 'register_postal_code_post_type'));
		add_action('init', array ($this, 'register_towns_post_type'));
		add_shortcode('postal_code_csv', array( $this,'postal_code_csv_shortcode'));
		add_shortcode('town_csv', array( $this,'town_csv_shortcode'));
		add_shortcode('town_directory', array( $this,'town_directory_shortcode'));
		add_shortcode('town_page', array( $this,'town_page_link_shortcode'));
		add_shortcode('town_list', array( $this,'town_list_shortcode'));
		add_shortcode('town_nearest_companies', array( $this,'town_nearest_companies_shortcode'));
		add_shortcode('town_directory_categories', array($this, 'display_town_directory_categories'));
		add_action('category_add_form_fields', array ( $this,'add_category_shortcode_field'));
		add_action('category_edit_form_fields', array ( $this,'edit_category_shortcode_field'));
		add_action('edited_category', array ( $this,'save_category_shortcode_field'));
		add_action('created_category', array ( $this,'save_category_shortcode_field'));
		add_filter('category_template', array( $this, 'region_template_by_name'));
		add_filter('template_include', array($this, 'pcpt_display_template_include'));
		add_filter('template_include', array($this, 'tnpt_display_template_include'));
		add_filter('template_include', array($this,'custom_modify_post_template'));
		add_action( 'init', array($this,'town_taxonomy'), 0 );
		//add_shortcode('custom_search', array( $this, 'seofy_search_shortcode'));
		add_filter('template_include', array( $this, 'seofy_search_results_template'));
		add_filter( 'the_content', array($this, 'autolink_town') );
		add_action('add_meta_boxes', array( $this, 'seofy_dk_navigation_related_cities_meta_box'));
		add_action('save_post', array ( $this, 'save_related_cities_meta_box'));
		add_action('wp_ajax_search_cities', array($this, 'search_cities'));
		add_action( 'admin_enqueue_scripts',  array($this,'admin_enqueue_scripts_callback') );
		//add_filter('template_include', array($this,'custom_search_results_template'));


		add_action('admin_menu',  array($this,'export_companies_menu'));
		add_action('admin_enqueue_scripts',  array($this,'export_companies_scripts'));
		add_action('wp_ajax_my_export_companies', array($this,'my_export_companies'));
		add_action('wp_ajax_proxy_export_companies', array($this,'proxy_export_companies'));
		add_action('wp_ajax_nopriv_proxy_export_companies', array($this, 'proxy_export_companies'));

		add_action('wp_ajax_load_town_directory', array($this,'load_town_directory'));
		add_action('wp_ajax_nopriv_load_town_directory',array($this, 'load_town_directory'));

		// Hook into WordPress 'init' action to register the custom route
		add_action('init', array($this,'export_json_route'));
		add_action('init', array($this, 'register_health_category_post_types'));

		add_filter('template_include', array($this,  'load_custom_single_template'));
		add_shortcode('seofy_featured_companies', array( $this,'seofy_featured_companies_shortcode'));

	}	

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Seofy_Dk_Navigation_Health_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Seofy_Dk_Navigation_Health_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/seofy-dk-navigation-health-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Seofy_Dk_Navigation_Health_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Seofy_Dk_Navigation_Health_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/seofy-dk-navigation-health-admin.js', array( 'jquery' ), $this->version, false );
		//wp_enqueue_script('town-directory-ajax', plugin_dir_url( __FILE__ ) . '/js/town-directory-ajax.js', array('jquery'), $this->version, false );
	}
	

	function admin_enqueue_scripts_callback(){

		//Add the Select2 CSS file
		wp_enqueue_style( 'select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0-rc.0');
	
		//Add the Select2 JavaScript file
		wp_enqueue_script( 'select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', 'jquery', '4.1.0-rc.0');
	
		//Add a JavaScript file to initialize the Select2 elements
		wp_enqueue_script( 'select2-init', '/wp-content/plugins/select-2-tutorial/select2-init.js', 'jquery', '4.1.0-rc.0');
	
	}

	
	


	public function register_postal_code_post_type() {
		$labels = array(
			'name' => 'Postnummer',
			'singular_name' => 'Postnummer',
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'has_archive' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array('title', 'editor'),
		);
	
		register_post_type('postnummer', $args);

	}

	public function register_towns_post_type() {
		$labels = array(
			'name' => 'Byer',
			'singular_name' => 'Byer',
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'has_archive' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array('title', 'editor'),
		);
	
		register_post_type('by', $args);

	}

	public function postal_code_csv_shortcode($atts) {
		// Default attributes
	
		$atts = shortcode_atts(array(
			'region_name' => 'Region Hovedstaden', // Default CSV file name
		), $atts);
	
		// Get the file name from the attributes
		$file = 'postal_codes.csv';
	
		// Get the absolute path to the CSV file
		$csv_file_path = plugin_dir_path(__FILE__) . $file;
	
		$csv_data = array();
		if (($handle = fopen($csv_file_path, 'r')) !== false) {
			$headers = fgetcsv($handle); // Get the first row as headers
			while (($data = fgetcsv($handle)) !== false) {

				if ($data[5] == $atts['region_name']){
					if (isset($_GET['town_name'])){
						if ($data[1] == $_GET['town_name']){
							$csv_data[$data[1]][] = $data[0];
						}
					}else{
						$csv_data[$data[1]][] = $data[0];
					}
		
				}


			}
			fclose($handle);
		}
	

		if (!isset($_GET['q'])){
			$filteredData = $csv_data;
		}else{
			$searchString = $_GET['q'];
			foreach ($csv_data as $key => $value) {
				// Check if the search string is in the key
				if (stripos($key, $searchString) !== false) {
					$filteredData[$key] = $value;
				}
				// Check if the search string is in any of the values inside the array
				foreach ($value as $v) {
					if (stripos($v, $searchString) !== false) {
						$filteredData[$key] = $value;
						break; // Stop checking values once we find a match
					}
				}
			}
		}


		$originalRegion = $atts['region_name'];
		//$modifiedRegion = str_replace("Region ", "", $originalRegion);


					
		// Clear the existing CSV, if it exists
		if (!empty($imported_csv_data_pc)) {
			//echo '<p>CSV generated and updated successfully. <a href="' . plugin_dir_url(__FILE__) . 'post_metadata.csv" download>Download CSV</a></p>';
			$args = array(
				'post_type' => 'by', // Replace with your custom post type
				'posts_per_page' => -1, // Retrieve all posts of the custom post type
				'post_status' => 'publish',
				'tax_query' => array(
					array(
						'taxonomy' => 'town_taxonomy', // Replace with your custom taxonomy
						'field' => 'slug',
						'terms' => 'main-town', // Replace with your custom taxonomy term
					),
				),
				'meta_query' => array(
					array(
						'key' => '_tn_region', // Replace with your custom field name
						'value' => $originalRegion, // The desired value to filter by
						'compare' => '='
					),
					array(
						'key' => '_tn_postal_code',
						'value' => $imported_csv_data_pc, // Replace with your desired postal codes
						'compare' => 'IN'
					)
				)
			);
		
		}else{
			$args = array(
				'post_type' => 'by', // Replace with your custom post type
				'posts_per_page' => -1, // Retrieve all posts of the custom post type
				'post_status' => 'publish',
				'tax_query' => array(
					array(
						'taxonomy' => 'town_taxonomy', // Replace with your custom taxonomy
						'field' => 'slug',
						'terms' => 'main-town', // Replace with your custom taxonomy term
					),
				),
				'meta_query' => array(
					array(
						'key' => '_tn_region', // Replace with your custom field name
						'value' => $originalRegion, // The desired value to filter by
						'compare' => '='
					)
				)
			);
		}

		

		
		
		$custom_posts = new WP_Query($args);

		$custom_post_array = array();
		
		if ($custom_posts->have_posts()) {
		
		
			while ($custom_posts->have_posts()) {
				$custom_posts->the_post();
		
				$post_item = get_post();
	
				$key = $post_item->post_name;
		
				// Add the post to the array using the combined key
				$custom_post_array[$key][] = $post_item; // You can also store specific post data here
		
				// You can add more custom field values as needed
			}
		
			// Reset the post data
			wp_reset_postdata();
		
			// Now, $custom_post_array contains the posts from the "Main Town" category
			// grouped by the combination of custom field values
			// You can access the posts using $custom_post_array[$key]
		}




		$town_names = array();
		$postal_codes = array();
		$output = '';
		$directory_slug_value = get_option('site_category_slug');
		foreach ($filteredData as $key => $value) {
			$pcode_output = '';
			$town_names[] = $key;
		
			foreach ($value as $pcode){

				$slug = $this->createSlug($directory_slug_value.' '.$key.' '.$pcode);
				//if (array_key_exists($slug, $custom_post_array)){
					
					$pcode_output .= "<a href='/by/".$slug."'>".$pcode."</a>";
				//}
				
				
				
			}
	
			if ($pcode_output != ''){
				$output .= "<div class='postal-code-container'>";
				$output .= "<div class='town-postal-codes'>";
				$output .= "<h2>".$key."</h2>";
				$output .= $pcode_output;
				$output .= "</div>";
				$output .= "</div>";
			}

			
	
		}

		$select_output = '<div class="filter_header">';
		$select_output .= '<form>';
	
		if (!isset($_GET['q'])){
			$select_output .= "<input type='text' name='q' class='search-text'>";
		}else{
			$select_output .= "<input type='text' name='q' class='search-text' value='".$_GET['q']."'>";
		}
		
		$select_output .= "<input type='submit' name='submit' value='Submit'>";
		$select_output .= '</form>';
		$select_output .= '</div>';

		echo $select_output;
		echo $output;
	
		return ob_get_clean();
	}


	// Add Category Shortcode to category add form
	public function add_category_shortcode_field() {
		?>
		<div class="form-field">
			<label for="category_shortcode_field"><?php _e('Category Shortcode', 'text-domain'); ?></label>
			<input type="text" name="category_shortcode_field" id="category_shortcode_field" value="" />
		</div>
		<?php
	}

	public function edit_category_shortcode_field($term) {
		$custom_field_value = get_term_meta($term->term_id, 'category_shortcode_field', true);
		?>
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="category_shortcode_field"><?php _e('Category Shortcode', 'text-domain'); ?></label>
			</th>
			<td>
				<input type="text" name="category_shortcode_field" id="category_shortcode_field" value="<?php echo esc_attr($custom_field_value); ?>" />
			</td>
		</tr>
		<?php
	}

		// Save Category Shortcode value when creating or updating category
	public function save_category_shortcode_field($term_id) {
		if (isset($_POST['category_shortcode_field'])) {
			$custom_field_value = sanitize_text_field($_POST['category_shortcode_field']);
			update_term_meta($term_id, 'category_shortcode_field', $custom_field_value);
		}
	}


	// Assign custom template for specific categories by name
	public function region_template_by_name($template) {
		$cat_id = get_query_var('cat');

		//echo $cat_id;
		$category = get_category($cat_id);

		// Define category names that should have a custom template
		$custom_category_names = array('hovedstaden', 'midtjylland', 'nordjylland', 'sjaelland', 'syddanmark'); // Replace with your desired category names
		//echo $category->slug;
		// Check if current category name is in the custom category names array
		if (in_array($category->slug, $custom_category_names)) {
			
			$new_template = plugin_dir_path(__FILE__) . 'templates/region-category-template.php';
			
			if (file_exists($new_template)) {
				//echo $new_template;
				return $new_template;
			}
		}

		return $template;
	}

	// Register custom templates for the custom post type
	public function pcpt_display_template_include($template) {
		if (is_singular('postnummer')) {
			// Use custom single post template
			$new_template = plugin_dir_path(__FILE__) . 'templates/single-postal_code.php';
			if (file_exists($new_template)) {
				return $new_template;
			}
		} elseif (is_post_type_archive('postnummer')) {
			// Use custom archive template
			$new_template = plugin_dir_path(__FILE__) . 'templates/archive-postal_code.php';
			if (file_exists($new_template)) {
				return $new_template;
			}
		}

		
		return $template;
	}
	
	// Register custom templates for the custom post type
	public function tnpt_display_template_include($template) {
		if (is_singular('by')) {
			// Use custom single post template
			$new_template = plugin_dir_path(__FILE__) . 'templates/single-town_name.php';
			if (file_exists($new_template)) {
				return $new_template;
			}
		} elseif (is_post_type_archive('by')) {
			// Use custom archive template
			$new_template = plugin_dir_path(__FILE__) . 'templates/archive-town_name.php';
			if (file_exists($new_template)) {
				return $new_template;
			}
		}

		
		return $template;
	}

	

	// Modify single post template for 'movies' post type
	public function custom_modify_post_template($template) {
		if (is_singular('post')) {
			$new_template = plugin_dir_path(__FILE__) . 'templates/single-post.php';
			if (file_exists($new_template)) {
				return $new_template;
			}
		}
		return $template;
	}


	public function custom_search_results_template($template) {
		// Check if it's a search query
		if (is_search()) {
			// Add your custom condition here
			// For example, if you want to use a different template for a specific search term
			$search_term = get_search_query();
			
			if (isset($_GET['s']) && !empty($_GET['s'])) {
				wp_reset_postdata();
				$search_query = sanitize_text_field($_GET['s']);
				$search_by = isset($_GET['search_by']) ? $_GET['search_by'] : 'any'; // Get the selected search option
			
				if ($search_by == 'town') {
					$args = array(
						'post_type' => 'by',
						'posts_per_page' => 1,
						'meta_query' => array(
							array(
								'key' => '_tn_town',
								'value' => $search_query,
								'compare' => 'REGEXP', // Use REGEXP for case insensitivity
							)
						)
					);
			
					$query = new WP_Query($args);
			
					
			
					if ($query->have_posts()) {
						$query->the_post();
						$post_id = get_the_ID();
						print_r(get_permalink($post_id));
						// Redirect to the permalink of the retrieved post ID
						header('Location: ' . get_permalink($post_id), true);
						//wp_redirect(get_permalink($post_id));
						die();
					} else{
						return $template;
					}
				}elseif ($search_by == 'postal_code') {
					
					$town_name = $this->search_town_by_postal_code($search_query);

					if ($town_name != ""){
						$args = array(
							'post_type' => 'by',
							'posts_per_page' => 1,
							'meta_query' => array(
								array(
									'key' => '_tn_town',
									'value' => $town_name,
									'compare' => 'REGEXP', // Use REGEXP for case insensitivity
								)
							)
						);
	
						$query = new WP_Query($args);
				
						
				
						if ($query->have_posts()) {
							$query->the_post();
							$post_id = get_the_ID();
							print_r(get_permalink($post_id));
							// Redirect to the permalink of the retrieved post ID
							header('Location: ' . get_permalink($post_id), true);
							//wp_redirect(get_permalink($post_id));
							die();
						} 
					}else{
				
						return $template;
					}

				

					
				}else{
				
					return $template;
				}
			}


			return $template;
		}
		// Return the original template if the condition is not met
		return $template;
	}


	public function town_csv_shortcode($atts) {
		// Default attributes
	
		$atts = shortcode_atts(array(
			'postal_code' => '', // Default CSV file name
		), $atts);
	
		// Get the file name from the attributes
		$file = 'town_names.csv';
	
		// Get the absolute path to the CSV file
		$csv_file_path = plugin_dir_path(__FILE__) . $file;
	
		
		$csv_data = array();
		if (($handle = fopen($csv_file_path, 'r')) !== false) {
			$headers = fgetcsv($handle); // Get the first row as headers
			while (($data = fgetcsv($handle)) !== false) {

				if ($data[2] == $atts['postal_code']){

					$csv_data[$data[1]][] = $data[1].'|'.$data[4].' '.$data[1];
					
		
				}


			}
			fclose($handle);

			//$csv_data = array_keys($csv_data);
		}

		$args = array(
			'post_type' => 'by', // Replace with your custom post type
			'posts_per_page' => -1, // Retrieve all posts of the custom post type
			'tax_query' => array(
				array(
					'taxonomy' => 'town_taxonomy', // Replace with your custom taxonomy
					'field' => 'slug',
					'terms' => 'sub-city', // Replace with your custom taxonomy term
				),
			),
			'meta_query' => array(
				array(
					'key' => '_tn_postal_code', // Replace with your custom field name
					'value' => $atts['postal_code'], // The desired value to filter by
					'compare' => '='
				)
			)
		);
		
		
		$custom_posts = new WP_Query($args);

		$custom_post_array = array();
		
		if ($custom_posts->have_posts()) {
		
		
			while ($custom_posts->have_posts()) {
				$custom_posts->the_post();
		
				$post_item = get_post();
				// Combine custom field values as the key
				$key = $post_item->post_name;
		
				// Add the post to the array using the combined key
				$custom_post_array[$key][] = $post_item; // You can also store specific post data here
		
				// You can add more custom field values as needed
			}
		
			// Reset the post data
			wp_reset_postdata();
		
			// Now, $custom_post_array contains the posts from the "Main Town" category
			// grouped by the combination of custom field values
			// You can access the posts using $custom_post_array[$key]
		}


	
		$output = '<h2>'. $atts['postal_code']. ' Byer </h2><hr>';
		$town_names = array();
		$postal_codes = array();
		foreach($csv_data as $town_name){
			$town = explode('|', $town_name[0]);
			$town_names[] = $town[0];
			$slug = $this->createSlug($town[1]);
			$output .= "<div class='town-name-container'>";
			if (array_key_exists($slug, $custom_post_array)){
				$output .= "<a href='/by/".$slug ."'>".$town[0]."</a>";
			}else{
				$output .= "<a href='#'>".$town[0]."</a>";
			}
	
			$output .= "</div>";
		}

		echo $output;
	
		return ob_get_clean();
	}


	public function town_taxonomy() {
		$labels = array(
			'name'              => _x( 'Town Types', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Town Type', 'taxonomy singular name', 'textdomain' ),
			// Add more labels as needed
		);
	
		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true, // Set to true for categories, false for tags
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => false, // Customize the slug
			// Add more arguments as needed
		);
	
		register_taxonomy( 'town_taxonomy', 'by', $args );
	}


	public function createSlug($string) {
	    $string = mb_strtolower($string, 'UTF-8');
    
		// Replace spaces with hyphens
		$string = str_replace(' ', '-', $string);
		
		// Define an array of character replacements specific to Danish
		$danish_characters = array(
			'æ' => 'ae',
			'ø' => 'oe',
			'å' => 'aa'
		);

		// Replace Danish characters with their corresponding replacements
		$string = strtr($string, $danish_characters);

		// Remove special characters and non-alphanumeric characters
		$string = preg_replace('/[^a-z0-9\-]/', '', $string);
		
		// Remove consecutive hyphens
		$string = preg_replace('/-+/', '-', $string);
		
		// Trim hyphens from the beginning and end of the string
		$string = trim($string, '-');
		
		return $string;
	}

	public function seofy_search_shortcode($atts) {
		ob_start();
	
		// Extract shortcode attributes
		$atts = shortcode_atts(array(
			's' => 'Søg',
		), $atts);
	
		$placeholder = esc_attr($atts['s']);
		
		?>
		<form role="search" method="get" id="searchform" action="">
			<div class="search-radios">
			<div class="radio-wrap">
				<input type="radio" name="search_by" id="search_by_any" value="any" checked> <label for="search_by_any"> Any </label>
			</div>
			

			<div class="radio-wrap">
				<input type="radio" name="search_by" id="search_by_town" value="town"> <label for="search_by_town"> By </label>
			</div>
		

			<div class="radio-wrap">
				<input type="radio" name="search_by" id="search_by_postal_code" value="postal_code"> <label for="search_by_postal_code"> Postnummer </label>
        
			</div>
			
			</div>
			<div class="searchbox-wrapper">
				<input type="text" name="s" id="s" placeholder="<?php echo $placeholder; ?>" />
				<input type="submit" id="searchsubmit" value="Søg" />
			</div>
			
		</form>
	
		<?php

	
		return ob_get_clean();
	}


	public function seofy_search_results_template($template) {
		if (is_search()) {

			if (isset($_GET['s'])){
				$raw_town = $_GET['s'];
				$danish_characters = array(
					'æ' => 'ae',
					'å' => 'aa'
				);
		
				// Replace Danish characters with their corresponding replacements
				$town = strtr($raw_town, $danish_characters);
				// WP_Query arguments to search for 'by' custom post type with meta key '_tn_town'
				$args = array(
					'post_type'      => 'by',
					'meta_query'     => array(
						array(
							'key'     => '_tn_town',
							'value'   => $town,
							'compare' => '=',
						),
					),
					'nopaging'       => true, // Retrieve all matching posts
					'posts_per_page' => 1,    // Limit to 1 post
					'post_status'    => 'publish',
					'ignore_sticky_posts' => true,
					's'              => '',   // Ensure no regular search is performed
				);

				// Perform the query
				$query = new WP_Query($args);

				// Check if any posts were found
				if ($query->have_posts()) {
					// Redirect to the first matching post
					$post = $query->posts[0];
					wp_redirect(get_permalink($post->ID));
					exit;
				}else{
					$custom_template = plugin_dir_path(__FILE__) . 'templates/search-results.php';
	
					if (file_exists($custom_template)) {
						return $custom_template;
					}
				}
				//echo $town;
			}
			/*
		
			*/
		}
	
		return $template;
	}




	public function town_directory_shortcode($atts){
		ob_start();
		$atts = shortcode_atts(array(
			'town' => 'København',
			'region' => 'Region Hovedstaden', // Default CSV file name
			'health_category' => 'kiropraktor'
		), $atts);
		$town = $atts['town'];
		$region = $atts['region'];
		$health_category = $atts['health_category'];
		$postal_codes = array();
	
		$postal_codes = $this->getAllPostalCodesFromCSV($town, $region);
		if (!empty($postal_codes)){


			wp_enqueue_script('town-directory-ajax', plugin_dir_url( __FILE__ )  . '/js/town-directory-ajax.js', array('jquery'), $this->version, false);
			wp_localize_script('town-directory-ajax', 'ajax_pagination_params', array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'posts_per_page' => 12,
				'town' => $town,
				'region' => $region,
				'postal_codes' => $postal_codes,
				'health_category' => $health_category
			));
	
			// Output initial container
			echo '<div id="town-directory-container"></div>';
			echo '<div id="town-directory-pagination" style="text-align: center;margin: 20px auto;background: var(--accent);width: 20%;"><button id="load-more-posts">Læs Mere</button></div><div id="loading-spinner" style="display:none; width: 100px;margin: auto;"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 150"><path fill="none" stroke="#3483FF" stroke-width="15" stroke-linecap="round" stroke-dasharray="300 385" stroke-dashoffset="0" d="M275 75c0 31-27 50-50 50-58 0-92-100-150-100-28 0-50 22-50 50s23 50 50 50c58 0 92-100 150-100 24 0 50 19 50 50Z"><animate attributeName="stroke-dashoffset" calcMode="spline" dur="2" values="685;-685" keySplines="0 0 1 1" repeatCount="indefinite"></animate></path></svg> </div>';



		}
		$output = ob_get_clean();
		return $output;
	}

	public function load_town_directory() {
		$postal_codes = isset($_POST['postal_codes']) ? $_POST['postal_codes'] : array();
		$paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
		$health_category = isset($_POST['health_category']) ? $_POST['health_category'] : 'post';
	
		$args = array(
			'post_type' => $health_category,
			'posts_per_page' => 12,
			'paged' => $paged,
			'meta_query' => array(
				array(
					'key' => '_postal_code',
					'value' => $postal_codes,
					'compare' => 'IN',
				),
			),
		);
	
		$firma_query = new WP_Query($args);
	
		if ($firma_query->have_posts()) {
			ob_start();
	
			while ($firma_query->have_posts()) {
				$firma_query->the_post();
	
				// Include the post template here (you can reuse your existing template logic)
				include 'templates/town-directory-post.php';
			}
	
			wp_reset_postdata();
			$response = array(
				'html' => ob_get_clean(),
				'max_pages' => $firma_query->max_num_pages,
			);
	
			wp_send_json_success($response);
		} else {
			wp_send_json_error('No more posts');
		}
	}

	private function getAllPostalCodesFromCSV($town, $region) {

		
		// Define the path to the CSV file
		$csv_file_path = plugin_dir_path(__FILE__) . 'postal_codes_new.csv';
	
		// Check if the CSV file exists
		if (file_exists($csv_file_path)) {
			
			// Read the CSV file into an array
			//$csv_data = array_map('str_getcsv', file($csv_file_path));
			$csv_data = $this->read_csv_with_utf8_bom($csv_file_path);
			// Find the column index for "town_name" and "region_name"
			$town_column_index = array_search('town_name', $csv_data[0]);
			$region_column_index = array_search('region_name', $csv_data[0]);
			$postal_code_column_index = array_search('postal_code', $csv_data[0]);
	
			// Initialize an array to store the matching "postal_code" values
			$matching_postal_codes = array();
	
			// Iterate through the rows to find matches
			foreach ($csv_data as $row) {
				if ($row[$town_column_index] === $town && $row[$region_column_index] === $region) {
					// Add the "postal_code" value to the array
					$matching_postal_codes[] = $row[$postal_code_column_index];
				}
			}
	
			// Return the array of matching "postal_code" values
			return $matching_postal_codes;
		}
	
		// Return an empty array if no match is found or the CSV file doesn't exist
		return array();
	}

	private function read_csv_with_utf8_bom($csv_file_path) {
		// Read the CSV file into an array
		$lines = file($csv_file_path);
	
		// Remove UTF-8 BOM characters, if present
		$bom = pack("CCC", 0xef, 0xbb, 0xbf);
		foreach ($lines as $key => $line) {
			if (0 === strpos($line, $bom)) {
				$lines[$key] = substr($line, 3);
			}
		}
		// Convert lines to UTF-8 to handle special characters
		// Convert lines to UTF-8 to handle special characters
		$lines = array_map(function($line) {
			return mb_convert_encoding($line, 'UTF-8', mb_detect_encoding($line, 'UTF-8, ISO-8859-1', true));
		}, $lines);
		// Use array_map to parse the CSV data
		$csv_data = array_map('str_getcsv', $lines);
	
		return $csv_data;
	}
	




	public function seofy_dk_navigation_related_cities_meta_box() {
		add_meta_box(
			'related_cities_meta_box',
			'Related Cities',
			array ( $this, 'display_related_cities_meta_box'),
			'by',
			'normal',
			'high'
		);
	}

	public function display_related_cities_meta_box($post) {
		ob_start();
		
		// Retrieve the current selected cities
		$selected_cities = get_post_meta($post->ID, 'related_cities', true);
		
		
		// Output the nonce field
		wp_nonce_field('related_cities_nonce', 'related_cities_nonce');
		
		// Output the select box with the selected cities
		echo '<label for="related_cities">Select Related Cities:</label>';
		echo '<select id="related_cities" name="related_cities[]" multiple="multiple" style="width: 100%;">';
		
		$current_post_id = get_the_ID();

		$args = array(
			'post_type'      => 'by',    // Replace 'by' with your custom post type
			'posts_per_page' => -1,      // Retrieve all posts of the specified type
			'post__not_in'   => array($current_post_id),  // Exclude the current post
		);
		
		$query = new WP_Query($args);
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
		
				// Your loop code here
				if (!empty($selected_cities)){
					$selected = in_array(get_the_ID(), $selected_cities) ? 'selected="selected"' : '';
				}else{
					$selected = '';
				}
				//$selected = '';
				echo '<option value="' . esc_attr(get_the_ID()) . '" ' . $selected . '>' . esc_html(get_the_title()) . '</option>';
		
			}
		
			wp_reset_postdata(); // Reset post data
		}
	

		
		echo '</select>';
		//print_r($selected_cities);
	
		//return ob_get_clean();
	}

	public function save_related_cities_meta_box($post_id) {
		// Check if nonce is set
		if (!isset($_POST['related_cities_nonce'])) {
			return;
		}
	
		// Verify that nonce is valid
		if (!wp_verify_nonce($_POST['related_cities_nonce'], 'related_cities_nonce')) {
			return;
		}
	
		// Check if this is an autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
	
		// Check if the user has permissions to save data
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}
	
		// Save the selected cities
		if (isset($_POST['related_cities']) && is_array($_POST['related_cities'])) {
			$related_cities = array_map('intval', $_POST['related_cities']);
			update_post_meta($post_id, 'related_cities', $related_cities);
		} else {
			delete_post_meta($post_id, 'related_cities');
		}
	}

	


	public function search_cities() {
		$search_query = sanitize_text_field($_GET['q']);
		$args = array(
			'post_type' => 'by',
			's' => $search_query,
			'posts_per_page' => -1,
		);

		$cities = get_posts($args);

		$results = array();

		foreach ($cities as $city) {
			$results[] = array(
				'id' => $city->ID,
				'text' => $city->post_title,
			);
		}

		wp_send_json($results);
	}

	public function autolink_town( $text, $is_content = false ) {
		global $post;
		$post_id = $post->ID;
		if (is_singular('by')) {
			$raw_keywords = trim(get_post_meta($post->ID, '_tn_keywords', true));

			
		
			$base_url = get_site_url(); // Replace this with your actual base URL

			// Explode the raw keywords string into an array
			$keywords_array = explode(',', $raw_keywords);
		
			// Initialize an empty array to store the final result
			$keywords = array();


			// Loop through the keywords array
			foreach ($keywords_array as $keyword) {
				// Trim whitespace from the keyword
				$args = array(
					'post_type' => 'any',  // Adjust the post type as needed
					'meta_query' => array(
						array(
							'key' => 'rank_math_focus_keyword',
							'value' => $keyword,
							'compare' => '='
						)
					)
				);

				$query = new WP_Query($args);

				// Check if there is a post found
				if ($query->have_posts()) {
					// Get the permalink of the first post found
					$post = $query->next_post();
					$permalink = get_permalink($post->ID);

					// Add the keyword as the key and the permalink as the value to the result array
					$keywords[$keyword] = $permalink;
				}


			}
		
			
			if ( !is_array($keywords) || empty($keywords) ) 
				return $text;

			
		
			
			$linkedContent = $this->linkKeywords($text, $keywords);
			return $linkedContent;
		} else{
			return $text;
		}
		
	}

	private function linkKeywords($content, $keywords) {
		$linkedKeywords = []; // Keep track of linked keywords
		$existingLinks = [];
		$placeholders = [];
		$placeholderCounter = 0;
	
		// Find existing links to avoid duplicating links
		preg_match_all('/<a href="[^"]*">([^<]*)<\/a>/', $content, $existingLinkMatches);
		foreach ($existingLinkMatches[1] as $existingLinkMatch) {
			$existingLinks[] = strtolower($existingLinkMatch); // Add lowercased version to ensure case-insensitive comparison
		}
	
		// Split the content into an array separating text from shortcodes
		$shortcodePattern = '/(\[[^\]]+\])/';
		$parts = preg_split($shortcodePattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	
		foreach ($parts as &$part) {
			if (!preg_match($shortcodePattern, $part)) {
				// Temporarily remove headings, img, a, and iframe tags and replace them with unique placeholders
				$excludeTagsPattern = '/<(h[1-6]|img|a|iframe)\b[^>]*>.*?<\/\1>/is';
				$part = preg_replace_callback($excludeTagsPattern, function($matches) use (&$placeholders, &$placeholderCounter) {
					$placeholder = "{{placeholder-" . $placeholderCounter . "}}";
					$placeholders[$placeholder] = $matches[0];
					$placeholderCounter++;
					return $placeholder;
				}, $part);
	
				// Process for keyword linking
				foreach ($keywords as $keyword => $url) {
					$pattern = "/\b($keyword)\b/i";
					preg_match_all($pattern, $part, $matches, PREG_OFFSET_CAPTURE);
	
					$offset = 0;
					foreach ($matches[0] as $match) {
						$matchedKeyword = $match[0];
						// Check against already linked keywords and existing links
						if (!in_array(strtolower($matchedKeyword), array_map('strtolower', $linkedKeywords)) && !in_array(strtolower($matchedKeyword), $existingLinks)) {
							$start = $match[1] + $offset;
							$linkedKeywords[] = $matchedKeyword;
							$link = '<a href="' . $url . '">' . $matchedKeyword . '</a>';
							$part = substr_replace($part, $link, $start, strlen($matchedKeyword));
							$offset += strlen($link) - strlen($matchedKeyword);
						}
					}
				}
	
				// Reinsert the excluded tags back into their original positions
				foreach ($placeholders as $placeholder => $tagContent) {
					$part = str_replace($placeholder, $tagContent, $part);
				}
			}
		}
	
		// Reassemble the parts back into the full content
		$content = implode('', $parts);
	
		return $content;
	}

	private function get_custom_excerpt($post_id, $word_limit = 100) {
		$post = get_post($post_id);
	
		if ($post) {
			$content = get_the_excerpt();
	
			// Strip shortcodes and HTML tags
			$content = strip_tags($content);
			$content = preg_replace("/\[[^\]]+\]/", '', $content);
	
			// Get the words
			$words = explode(' ', $content);
	
			// Limit the number of words
			$excerpt = implode(' ', array_slice($words, 0, $word_limit));
	
			// Add an ellipsis if the content exceeds the word limit
			if (count($words) > $word_limit) {
				$excerpt .= '...';
			}
	
			return $excerpt;
		}
	
		return '';
	}

	public function town_page_link_shortcode($atts){
		//print_r($atts);
			
		$atts = shortcode_atts(array(
			'region' => '', 
			'postal_code' => '', 
			'category' => '', 
		), $atts);
		
		$this->search_post_by_region_and_town("Region ".$atts['region'], $atts['postal_code'], $atts['category']);
	}


	private function search_post_by_region_and_town($region, $postal_code, $category) {
		// Load the CSV file	// Define the path to the CSV file
		$csv_file_path = plugin_dir_path(__FILE__) . 'postal_codes_new.csv';
	
		// Check if the CSV file exists
		if (file_exists($csv_file_path)) {
			$csv_data = $this->read_csv_with_utf8_bom($csv_file_path);

		
			// Find the row with the matching postal code
			$matching_row = array_filter($csv_data, function($row) use ($postal_code) {
				return $row[0] == $postal_code;
			});

			
		
			// If no matching row is found, return or handle accordingly
			if (empty($matching_row)) {
				return;
			}

			$matching_row= reset($matching_row);
		
			// Extract town_name and region_name from the matching row
			$town_name = $matching_row[1];
			$region_name = $matching_row[5];
	
		
			// Create a WP_Query to search for posts with the specified conditions
			$args = array(
				'post_type' => 'by',  // Replace 'your_post_type' with your actual post type
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => '_tn_region',
						'value' => $region_name,
						'compare' => '='
					),
					array(
						'key' => '_tn_town',
						'value' => $town_name,
						'compare' => '='
					),
					array(
						'key' => '_tn_category',
						'value' => $category,
						'compare' => '='
					),
				),
			);
		
			$query = new WP_Query($args);
		
			// Check if any posts are found
			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();
					// Process each found post as needed
					$post_id = get_the_ID();
					// Do something with $post_id
					echo '<div class="directory-town-wrapper">';
					echo '<a class="town-link" href="'.get_the_permalink().'">'.$category.' '.$town_name.' </a>';
					echo '</div>';
				}
				wp_reset_postdata(); // Reset the post data to the main query
			} 

		}
	
	}

	private function search_post_by_region_and_town_for_list($category, $postal_code) {
		// Load the CSV file	// Define the path to the CSV file
		$csv_file_path = plugin_dir_path(__FILE__) . 'postal_codes_new.csv';
	
		// Check if the CSV file exists
		if (file_exists($csv_file_path)) {
			$csv_data = $this->read_csv_with_utf8_bom($csv_file_path);
		
		
			// Find the row with the matching postal code
			$matching_row = array_filter($csv_data, function($row) use ($postal_code) {
				return $row[0] == $postal_code;
			});

			
		
			// If no matching row is found, return or handle accordingly
			if (empty($matching_row)) {
				return;
			}
		
			$matching_row= reset($matching_row);
			
			// Extract town_name and region_name from the matching row
			$town_name = $matching_row[1];
			$region_name = $matching_row[5];
	
		
			// Create a WP_Query to search for posts with the specified conditions
			$args = array(
				'post_type' => 'by',  // Replace 'your_post_type' with your actual post type
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => '_tn_region',
						'value' => $region_name,
						'compare' => '='
					),
					array(
						'key' => '_tn_town',
						'value' => $town_name,
						'compare' => '='
					),
				),
			);
		
			$query = new WP_Query($args);
		
			// Check if any posts are found
			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();
					// Process each found post as needed
					$post_id = get_the_ID();
					// Do something with $post_id
					return $town_name;
				}
				wp_reset_postdata(); // Reset the post data to the main query
			} 

		}
	
	}

	private function search_region_from_csv($town) {
		// Load the CSV file	// Define the path to the CSV file
		$csv_file_path = plugin_dir_path(__FILE__) . 'postal_codes_new.csv';
	
		// Check if the CSV file exists
		if (file_exists($csv_file_path)) {
			$csv_data = $this->read_csv_with_utf8_bom($csv_file_path);
		
		
			// Find the row with the matching postal code
			$matching_row = array_filter($csv_data, function($row) use ($town) {
				return $row[1] == $town;
			});

			
		
			// If no matching row is found, return or handle accordingly
			if (empty($matching_row)) {
				return;
			}
		
			$matching_row = reset($matching_row);
		
			$region_name = $matching_row[5];
			return $region_name;

		}
	
	}

	private function search_town_by_postal_code($postal_code) {
		// Load the CSV file	// Define the path to the CSV file
		$csv_file_path = plugin_dir_path(__FILE__) . 'postal_codes_new.csv';
	
		// Check if the CSV file exists
		if (file_exists($csv_file_path)) {
			$csv_data = $this->read_csv_with_utf8_bom($csv_file_path);
		
		
			// Find the row with the matching postal code
			$matching_row = array_filter($csv_data, function($row) use ($postal_code) {
				return $row[0] == $postal_code;
			});

			
		
			// If no matching row is found, return or handle accordingly
			if (empty($matching_row)) {
				return;
			}
		
			$matching_row= reset($matching_row);
			
			// Extract town_name and region_name from the matching row
			$town_name = $matching_row[1];
			$region_name = $matching_row[5];
			
			return $town_name;

		}
	
	}

	public function town_nearest_companies_shortcode($atts){
		//print_r($atts);
			
		$atts = shortcode_atts(array(
			'region' => '', 
			'postal_code' => '',
			'health_category' => ''
		), $atts);
		
		$town = $this->search_post_by_region_and_town_for_list("Region ".$atts['region'], $atts['postal_code']);

		$region = $this->search_region_from_csv($town);

		$postal_codes = array();


		$postal_codes = $this->getAllPostalCodesFromCSV($town, $region);



		//print_r ($postal_codes);
		if (!empty($postal_codes)){

			
			$args = array(
				'post_type' => $atts['health_category'],
				'posts_per_page' => 10,
				'orderby'        => 'rand', // Order by random
				'meta_query'     => array(
					array(
						'key'     => '_postal_code', // Replace with your custom field name for postal code
						'value'   => $postal_codes,   // Replace with the desired value for postal code
						'compare' => 'IN',            // Comparison operator for postal code
					),
				),
			);

			$query = new WP_Query($args);
		
			if ($query->have_posts()) {
				ob_start();
				?>

				
				<div class="nearest-container">
					<ul>
		
					<?php while ($query->have_posts()) {  $query->the_post(); ?>

						<li><a href="<?php echo get_the_permalink(); ?>"> <?php echo get_the_title(); ?> </a></li>

					<?php } ?>

					</ul>

				</div>
				<?php

			
				wp_reset_postdata();
				$contents=ob_get_contents();
				ob_end_clean();
				return $contents;
			
				
			}

		}
	
	}

	public function town_list_shortcode($atts){
		$args = array(
			'post_type' => 'by',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
			'tax_query' => array(
				array(
					'taxonomy' => 'town_taxonomy', // Replace with your custom taxonomy
					'field' => 'slug',
					'terms' => 'main-by', // Replace with your custom taxonomy term
				),
			),
		);
	
		$current_page = get_permalink();
	
		$output = '<div class="towns__reset"><a href="'.$current_page.'"><i class="fa-solid fa-arrows-rotate"></i> Nulstil</a></div>';
		$output .= '<nav class="towns__nav"><div class="towns__letters">';
	
		$characters = array_merge(range('A', 'Z'), array('Æ', 'Ø', 'Å'));
	
		foreach ($characters as $char) {
			$output .= '<a href="#" class="towns__letter" data-starting-char="' . esc_attr($char) . '">' . esc_html($char) . '</a>';
		}
	
		$output .= '</div></nav>';
	
		$query = new WP_Query($args);
		if ($query->have_posts()) {
			$output .= '<div class="towns__list">';
			while ($query->have_posts()) {
				$query->the_post();
				$title = get_the_title();
				$permalink = get_the_permalink();
				$town_name = get_post_meta(get_the_ID(), '_tn_town', true);
				$output .= '<div class="towns__item" data-town-name="' . $town_name . '"><a href="' . $permalink . '">' . $town_name . '</a></div>';
			}
			$output .= '</div>';
			wp_reset_postdata();
		}
	
		return $output;
	}
	
	

	//

	public function export_companies_menu() {
		add_menu_page(
			'Export Companies',
			'Export Companies',
			'manage_options',
			'export-companies',
			array ($this, 'export_companies_page')
		);
	}

	public function export_companies_page() {
		?>
		<div class="wrap">
			<h1>Export Companies</h1>
			<p> Note: Do not close or restart this browser tab once you started to Export.</p>
			<button id="start-export">Start Export</button>
			<div id="progress-bar" style="width: 100%; background: #e0e0e0; height: 20px; display: none;">
				<div id="progress" style="width: 0; background: #0073aa; height: 100%;"></div>
			</div>
			<p id="progress-text" style="display: none;">Processing <span id="counter">0</span>/<span id="total">0</span></p>
			<p id="success-message" style="display: none; color: green;">All posts processed successfully!</p>
		</div>
		<script>
			var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
		</script>
		<?php
	}


	
	

	public function export_companies_scripts($hook) {
		if ($hook != 'toplevel_page_export-companies') {
			return;
		}
		wp_enqueue_script('export-script', plugin_dir_url(__FILE__) . 'js/export-script.js', array('jquery'), $this->version, false);
		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/seofy-dk-navigation-health-admin.js', array( 'jquery' ), $this->version, false );
	    // Localize the script with new data
		$my_export_nonce = wp_create_nonce('my_export_nonce');
		$ajax_url = admin_url('admin-ajax.php');
		wp_localize_script('export-script', 'myexportData', array(
			'ajax_url' => $ajax_url,
			'nonce' => $my_export_nonce
		));
	
	}

	public function my_export_companies() {
		check_ajax_referer('my_export_nonce', 'security');
	
		$args = array(
			'category_name' => 'firma',
			'posts_per_page' => -1
		);
	
		$posts = get_posts($args);
	
		$post_data = array();

		$site_url = preg_replace('#^https?://#', '', home_url());
		$site_url = rtrim($site_url, '/');

		foreach ($posts as $post) {
			$post_data[] = array(
				'databank_id' => get_post_meta($post->ID, '_databank_id', true),
				'postal_code' => get_post_meta($post->ID, '_postal_code', true),
				'data_name' => get_post_meta($post->ID, '_data_name', true),
				'data_phone' => get_post_meta($post->ID, '_data_phone', true),
				'data_website' => get_post_meta($post->ID, '_data_website', true),
				'data_facebook' => get_post_meta($post->ID, '_data_facebook', true),
				'data_email' => get_post_meta($post->ID, '_data_email', true),
				'street_name' => get_post_meta($post->ID, '_street_name', true),
				'street_number' => get_post_meta($post->ID, '_street_number', true),
				'postal_area' => get_post_meta($post->ID, '_postal_area', true),
				'municipality' => get_post_meta($post->ID, '_municipality', true),
				'region' => get_post_meta($post->ID, '_region', true),
				'province' => get_post_meta($post->ID, '_province', true),
				'lon_map' => get_post_meta($post->ID, '_lon_map', true),
				'lat_map' => get_post_meta($post->ID, '_lat_map', true),
				'lon_route' => get_post_meta($post->ID, '_lon_route', true),
				'lat_route' => get_post_meta($post->ID, '_lat_route', true),
				'place_name' => get_post_meta($post->ID, '_place_name', true),
				'directory_category' => get_post_meta($post->ID, '_directory_category', true),
				'opening_hours' => get_post_meta($post->ID, '_opening_hours', true),
				'site_url' => $site_url,
				'wp_link' => get_permalink($post->ID)  // Add the permalink
			);
		}
	
		wp_send_json_success($post_data);
	}


	public function proxy_export_companies() {
		check_ajax_referer('my_export_nonce', 'security');
	
		$post_data = json_decode(stripslashes($_POST['post_data']), true);
	
		$url = 'https://directories.seofy.dk/import_companies';
		$args = array(
			'body' => json_encode($post_data),
			'headers' => array(
				'Content-Type' => 'application/json'
			),
			'method' => 'POST'
		);
	
		$response = wp_remote_post($url, $args);
	
		if (is_wp_error($response)) {
			wp_send_json_error($response->get_error_message());
		} else {
			wp_send_json_success(wp_remote_retrieve_body($response));
		}
	}

	public function export_json_route() {
		// Add rewrite rule to map URL to function
		add_rewrite_rule('^api/v1/posts/?$', 'index.php?export_api=1', 'top');
		
		// Register the query variables
		add_filter('query_vars', function($vars) {
			$vars[] = 'export_api';
			$vars[] = 'offset';   // Add offset query variable
			$vars[] = 'limit';    // Add limit query variable
			return $vars;
		});
		
		// Check if the query variable is set and handle the request
		add_action('template_redirect', array($this, 'export_json_request'));
	}
	
	public function export_json_request() {
		global $wp_query;
		
		// Check if our custom query variable is present
		if (get_query_var('export_api')) {
			
			// Check if the request comes from cURL by checking the User-Agent
			if (strpos($_SERVER['HTTP_USER_AGENT'], 'curl') === false) {
				// If the User-Agent doesn't contain 'curl', send a 403 Forbidden response
				header('HTTP/1.1 403 Forbidden');
				echo json_encode(array('error' => 'Forbidden: This API can only be accessed using cURL.'));
				exit;
			}
			
			// Fetch the offset and limit from query vars, use defaults if not set
			$offset = get_query_var('offset') ? intval(get_query_var('offset')) : 0;
			$limit = get_query_var('limit') ? intval(get_query_var('limit')) : 10;  // Default to 10 if no limit provided
	
			// Fetch the latest posts with offset and limit
			$args = array(
				'category_name' => 'firma',
				'posts_per_page' => $limit,  // Number of posts to retrieve
				'offset' => $offset          // Offset for pagination
			);
			$posts = get_posts($args);
			
			// Prepare the posts data
			$data = array();
			foreach ($posts as $post) {
				$data[] = array(
					'databank_id' => get_post_meta($post->ID, '_databank_id', true),
					'postal_code' => get_post_meta($post->ID, '_postal_code', true),
					'data_name' => get_post_meta($post->ID, '_data_name', true),
					'data_phone' => get_post_meta($post->ID, '_data_phone', true),
					'data_website' => get_post_meta($post->ID, '_data_website', true),
					'data_facebook' => get_post_meta($post->ID, '_data_facebook', true),
					'data_email' => get_post_meta($post->ID, '_data_email', true),
					'street_name' => get_post_meta($post->ID, '_street_name', true),
					'street_number' => get_post_meta($post->ID, '_street_number', true),
					'postal_area' => get_post_meta($post->ID, '_postal_area', true),
					'municipality' => get_post_meta($post->ID, '_municipality', true),
					'region' => get_post_meta($post->ID, '_region', true),
					'province' => get_post_meta($post->ID, '_province', true),
					'lon_map' => get_post_meta($post->ID, '_lon_map', true),
					'lat_map' => get_post_meta($post->ID, '_lat_map', true),
					'lon_route' => get_post_meta($post->ID, '_lon_route', true),
					'lat_route' => get_post_meta($post->ID, '_lat_route', true),
					'place_name' => get_post_meta($post->ID, '_place_name', true),
					'directory_category' => get_post_meta($post->ID, '_directory_category', true),
					'opening_hours' => get_post_meta($post->ID, '_opening_hours', true),
					'site_url' => site_url(),  // Add site_url
					'wp_link' => get_permalink($post->ID)  // Add the permalink
				);
			}
			
			// Set the header to JSON
			header('Content-Type: application/json');
			
			// Output the data as JSON
			echo json_encode($data);
			
			// Prevent WordPress from rendering the normal page
			exit;
		}
	}

	public function register_health_category_post_types() {
		$post_types = [
			'kiropraktor'   => 'Kiropraktor',
			'fysioterapeut' => 'Fysioterapeut',
			'akupunktur'    => 'Akupunktør',
			'massoer'       => 'Massør',
			'zoneterapi'    => 'Zoneterapeut',
			'osteopat'      => 'Osteopat',
		];
	
		foreach ($post_types as $slug => $name) {
			register_post_type($slug, [
				'labels' => [
					'name'               => $name . 's',
					'singular_name'      => $name,
					'add_new'            => 'Add New',
					'add_new_item'       => 'Add New ' . $name,
					'edit_item'          => 'Edit ' . $name,
					'new_item'           => 'New ' . $name,
					'view_item'          => 'View ' . $name,
					'search_items'       => 'Search ' . $name . 's',
					'not_found'          => 'No ' . strtolower($name) . 's found',
					'not_found_in_trash' => 'No ' . strtolower($name) . 's found in Trash',
					'all_items'          => 'All ' . $name . 's',
					'archives'           => $name . ' Archives',
				],
				'public'       => true,
				'has_archive'  => true,
				'menu_position'=> 20,
				'menu_icon'    => 'dashicons-id',
				'supports'     => ['title', 'editor', 'thumbnail'],
				'show_in_rest' => true,
				'rewrite'      => ['slug' => $slug],
			]);
		}
		
	}
	
	public function load_custom_single_template($template) {
		if (is_singular(array('kiropraktor', 'fysioterapeut', 'akupunktur', 'massoer', 'zoneterapi', 'osteopat'))) {
			$custom_template = plugin_dir_path(__FILE__) . 'templates/single-health-directory.php';
			if (file_exists($custom_template)) {
				return $custom_template;
			}
		}
		return $template;
	}

	public function display_town_directory_categories() {
		global $post; // Needed to get the current post context
		ob_start();
	
		// Get current post's metadata
		$postal_code = get_post_meta($post->ID, '_tn_postal_code', true);
		$region = get_post_meta($post->ID, '_tn_region', true);
		$town = get_post_meta($post->ID, '_tn_town', true);
		$municipality = get_post_meta($post->ID, '_tn_municipality', true);
	
		// Build dynamic meta query
		$meta_query = array('relation' => 'AND');
		if (!empty($postal_code)) {
			$meta_query[] = array(
				'key'     => '_tn_postal_code',
				'value'   => $postal_code,
				'compare' => '='
			);
		}
		if (!empty($region)) {
			$meta_query[] = array(
				'key'     => '_tn_region',
				'value'   => $region,
				'compare' => '='
			);
		}
		if (!empty($town)) {
			$meta_query[] = array(
				'key'     => '_tn_town',
				'value'   => $town,
				'compare' => '='
			);
		}
		if (!empty($municipality)) {
			$meta_query[] = array(
				'key'     => '_tn_municipality',
				'value'   => $municipality,
				'compare' => '='
			);
		}
	
		// WP Query args
		$args = array(
			'post_type'      => 'by',
			'posts_per_page' => -1,
			'post__not_in'   => array($post->ID), // Exclude the current post
			'tax_query'      => array(
				array(
					'taxonomy' => 'town_taxonomy',
					'field'    => 'slug',
					'terms'    => 'main-by',
					'operator' => 'NOT IN',
				),
			),
			'meta_query'     => $meta_query,
		);
	
		$query = new WP_Query($args);
	
		if ($query->have_posts()) {
			echo '<div class="modern-town-grid">';
		
			while ($query->have_posts()) {
				$query->the_post();
				$post_id = get_the_ID();
		
				$cat = get_post_meta($post_id, '_tn_category', true);
				$image_url = plugin_dir_url(__FILE__) . 'images/' . strtolower(esc_attr($cat)) . '-behandler.jpg';
				$permalink = get_permalink($post_id);
				$town_name = get_post_meta($post_id, '_tn_town', true);
		
				echo '<a href="' . esc_url($permalink) . '" class="town-card">';
				echo '<div class="town-card-image">';
				echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($town_name) . '">';
				echo '</div>';
				echo '<div class="town-card-meta">';
				echo '<h4>' .  esc_html($cat) . '</h4>';
				echo '</div>';
				echo '</a>';
			}
		
			echo '</div>';
			wp_reset_postdata();
		} else {
			echo '<p>No related towns found.</p>';
		}
		
	
		return ob_get_clean();
	}

	public function seofy_featured_companies_shortcode($atts) {
		$atts = shortcode_atts(array(
			'columns' => 3,
		), $atts, 'seofy_featured_companies');
	
		ob_start();
	
		$args = array(
			'post_type' => ['kiropraktor', 'fysioterapeut', 'akupunktur', 'massoer', 'zoneterapi', 'osteopat'],
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key'     => '_company_featured',
					'value'   => 'yes',
					'compare' => '='
				)
			)
		);
		
		$query = new WP_Query($args);
	
		$columns_class = ($atts['columns'] == 4) ? 'columns-4' : 'columns-3';
	
		if ($query->have_posts()) {
			echo '<div class="seofy-featured-companies-wrapper">';
			echo '<div class="seofy-company-grid ' . esc_attr($columns_class) . '">';

			while ($query->have_posts()) {
				$query->the_post();
				?>
				<div class="company-list-card">
					<div class="company-thumbnail">
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail('medium'); ?>
						</a>

					</div>
					<div class="company-info">
						<h3 class="company-name">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<p class="company-address">
							<?php
							echo get_post_meta(get_the_ID(), '_street_name', true) . ' ' .
								 get_post_meta(get_the_ID(), '_street_number', true) . '<br>' .
								 get_post_meta(get_the_ID(), '_postal_code', true) . ' ' .
								 get_post_meta(get_the_ID(), '_postal_area', true);
							?>
						</p>
						<div class="company-categories">
							<?php
							$categories = explode(',', get_post_meta(get_the_ID(), '_directory_category', true));
							foreach ($categories as $cat): ?>
								<span class="category-tag"><i class="fa-solid fa-tag"></i> <?php echo esc_html(trim($cat)); ?></span>
							<?php endforeach; ?>
						</div>
						<div class="company-schedule">
							<?php echo do_shortcode('[seofy_opening_hours_current]'); ?>
							<div class="contact-button">
								<a href="<?php the_permalink(); ?>">Kontakt</a>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			echo '</div>'; // close seofy-company-grid
			echo '</div>'; // close seofy-featured-companies-wrapper
			
			wp_reset_postdata();
		}
	
		return ob_get_clean();
	}
	

}