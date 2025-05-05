<?php
// Custom template for displaying single custom post type

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
     
                <div class="entry-content">
                    <?php the_content(); ?>
                </div><!-- .entry-content -->

            </article><!-- #post-<?php the_ID(); ?> -->

        <?php endwhile; ?>
        <?php 
        $postal_code = get_post_meta($post->ID, '_pn_postal_code', true);
        $town_name = get_post_meta($post->ID, '_pn_town', true);
        $region_name = get_post_meta($post->ID, '_pn_region', true);



        //$address = urlencode($postal_code . ', ' . $town_name . ', ' .  $region_name.' Tandlæger');
        //$address = urlencode($town_name . ', Denmark Tandlæger');
        ?>
        <?php 

        if (!is_null($postal_code) || $postal_code != ""){
            $pc = $postal_code;
            $args = array(
                'post_type' => 'post',
                'posts_per_page' => -1,
                'meta_query'     => array(
                    array(
                        'key'     => '_postal_code', // Replace with your custom field name
                        'value'   => $pc,     // Replace with the desired value of the custom field
                        'compare' => '=',  // Comparison operator. Possible values: '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS', 'NOT EXISTS', 'REGEXP', 'NOT REGEXP', 'RLIKE'
                    ),
                ),
            );
            
            $query = new WP_Query($args);
            $map_locations = array();
            
            if ($query->have_posts()) {
                echo '<div class="company-container">';
            
                while ($query->have_posts()) {
                    $query->the_post();
                    echo '<div class="company-area">';
                    echo '<div class="company-name"><h2><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2></div>';
                    echo '<div class="company-excerpt">' . get_the_excerpt() . '</div>';
                    echo '<div class="more-btn"><a href="' . get_permalink() . '">Læs mere</a></div>';

                    echo '</div>';

                    $latValue = get_post_meta(get_the_ID(), '_lat_map', true);
                    $lonValue = get_post_meta(get_the_ID(), '_lon_map', true);
                    if (($latValue) && ($lonValue)) {
                        $map_location['title'] = get_the_title();
                        $map_location['link'] = get_permalink();
                        $map_location['lat'] = $latValue;
                        $map_location['lon'] = $lonValue;
                        $map_locations[] = $map_location;
                    }
                }
            
                echo '</div>';
            
                wp_reset_postdata();
            } else {
                $map_locations = array();
                // Define the endpoint URL
                $endpoint = "https://api.dataforsyningen.dk/postnumre/". $postal_code;

                // Fetch data from the endpoint
                $response = file_get_contents($endpoint);

                // Check if the request was successful
                if ($response === false) {
                    echo "Failed to retrieve data.";
                } else {
                    // Decode the JSON response
                    $data = json_decode($response);
    
                    // Check if JSON decoding was successful
                    if ($data === null) {
                        echo "Failed to decode JSON data.";
                    } else {

                        // Add more fields as needed
                       $map_location['title'] = $data->nr;
                       $map_location['link'] = get_site_url().'/postnummer/'.$data->nr;
                        $map_location['lat'] = $data->visueltcenter[1];
                        $map_location['lon'] = $data->visueltcenter[0];
                        $map_locations[] = $map_location;
                    }
                }
                $target = (int) $postal_code;

                $min_range = $target - 100;
                $max_range = $target + 100;
        
                $postal_codes = array();
        
                for ($i = $min_range; $i <= $max_range; $i++) {
                    // Ensure that the postal code is in a valid range
                    if ($i >= 0 && $i <= 9999) {
                        $postal_codes[] = $i;
                    }
                }

                $args = array(
                    'post_type' => 'post',
                    'posts_per_page' => -1,
                    'meta_query'     => array(
                        array(
                            'key'     => '_postal_code', // Replace with your custom field name
                            'value'   => $postal_codes,     // Replace with the desired value of the custom field
                            'compare' => 'IN',  // Comparison operator. Possible values: '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS', 'NOT EXISTS', 'REGEXP', 'NOT REGEXP', 'RLIKE'
                        ),
                    ),
                );
                
                $query = new WP_Query($args);
          
                if ($query->have_posts()) {
                    echo '<div class="company-container">';
                
                    while ($query->have_posts()) {
                        $query->the_post();
    
        
                        $latValue = get_post_meta(get_the_ID(), '_lat_map', true);
                        $lonValue = get_post_meta(get_the_ID(), '_lon_map', true);
                        if (($latValue) && ($lonValue)) {
                            $map_location['title'] = get_the_title();
                            $map_location['link'] = get_permalink();
                            $map_location['lat'] = $latValue;
                            $map_location['lon'] = $lonValue;
                            $map_locations[] = $map_location;
                        }
                    }
                
                    echo '</div>';
                    echo 'Ingen liste under dette postnummer, men her er den nærmeste liste inden for en radius på 15 kilometer.';
                    wp_reset_postdata();
                }

            }
        }


        ?>
        <?php 
        
            if (!empty($map_locations)){ 

                $initial_lat = $map_locations[0]['lat'];
                $initial_lon = $map_locations[0]['lon'];

                $targetLatitude = $map_locations[0]['lat']; // Replace with your target latitude
                $targetLongitude = $map_locations[0]['lon']; // Replace with your target longitude


                $locationsWithinRadius = [];

                foreach ($map_locations as $location) {
                    $distance = calculateDistance($targetLatitude, $targetLongitude, $location['lat'], $location['lon']);
                    if ($distance <= 15) { // Check if the distance is within 5 kilometers
                        $locationsWithinRadius[] = $location;
                    }
                }
                $map_locations = array();
                $map_locations =$locationsWithinRadius;
     
                
        ?>
            <div class="postal_map">
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
                <div id="map" style="width: 100%; height: 400px;"></div>
                <!-- Include Leaflet JS -->
                <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
                <script>
                // Initialize the map
                var map = L.map('map').setView([<?php echo $initial_lat; ?>, <?php echo $initial_lon; ?>], 15);

                // Add a tile layer to the map (you can use other tile providers)
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                // Define store locations

                var storeLocations = [
                <?php

                    foreach ($map_locations as $location) {
                        echo "{ title: '{$location['title']}', lat: {$location['lat']}, lng: {$location['lon']}, link: '{$location['link']}' },\n";
                    }
                ?>
                ];


                /*var storeLocations = [
                    {lat: 14.643548468198198, lng: 120.97873427076198, title: 'Pin 1', link: 'https://storea.com'},
                    {lat: 14.638565851123639, lng: 120.97452856685075, title: 'Pin 2', link: 'https://storea.com'}
                    // Add more store locations as needed
                ];*/

                // Add markers for each store location
                storeLocations.forEach(function(location) {
                    var marker = L.marker([location.lat, location.lng]).addTo(map);
                    var popupContent = '<a href="' + location.link + '">' + location.title + '</a>';
                    marker.bindPopup(popupContent);
                });
                </script>

            </div>

            <?php 
                //$content = '[town_csv postal_code ="'.$postal_code.'"]'; // Replace with the shortcode you want to execute

                //$shortcode_output = do_shortcode($content);
                //echo $shortcode_output;
            ?>

        <?php } ?>
    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
<?php 
    function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $latDifference = $lat2 - $lat1;
        $lonDifference = $lon2 - $lon1;

        $a = sin($latDifference / 2) * sin($latDifference / 2) +
            cos($lat1) * cos($lat2) * sin($lonDifference / 2) * sin($lonDifference / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance; // Distance in kilometers
    }
?>