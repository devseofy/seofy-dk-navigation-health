<?php
/**
 * Template Name: Custom Single Post Template for Category
 */

// Custom template for displaying single custom post type
$map_locations = array();

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
 
        <?php while (have_posts()) : the_post(); ?>
            <?php 
            
                            // Get the featured image HTML
                $featured_image = get_the_post_thumbnail(get_the_ID(), 'large');

                // Get the post title
                $post_title = get_the_title();
                $wordpress_url = site_url();
                $site_url = home_url(); // This will include the scheme (http or https)

                // If you want to remove the "https://" part, you can use str_replace
                $site_url_without_https = str_replace('https://', '', $site_url);

                $site_name = get_bloginfo('name');
            
                $postal_code = get_post_meta(get_the_ID(), '_postal_code', true);
                $data_name = get_post_meta(get_the_ID(), '_data_name', true);
                $data_phone = get_post_meta(get_the_ID(), '_data_phone', true);
                $data_website = get_post_meta(get_the_ID(), '_data_website', true);
                $data_facebook = get_post_meta(get_the_ID(), '_data_facebook', true);
                $data_email = get_post_meta(get_the_ID(), '_data_email', true);
            
                $street_name = get_post_meta(get_the_ID(), '_street_name', true);
                $street_number = get_post_meta(get_the_ID(), '_street_number', true);
                $postal_area = get_post_meta(get_the_ID(), '_postal_area', true);
                $municipality = get_post_meta(get_the_ID(), '_municipality', true);
                $region = get_post_meta(get_the_ID(), '_region', true);
                $province = get_post_meta(get_the_ID(), '_province', true);
                $lon_map = get_post_meta(get_the_ID(), '_lon_map', true);
                $lat_map = get_post_meta(get_the_ID(), '_lat_map', true);
                $lon_route = get_post_meta(get_the_ID(), '_lon_route', true);
                $lat_route = get_post_meta(get_the_ID(), '_lat_route', true);
                $place_name = get_post_meta(get_the_ID(), '_place_name', true);
                $category = get_post_meta(get_the_ID(), '_directory_category', true);
                $is_claimed = get_post_meta(get_the_ID(), '_is_claimed', true);
                $other_details = get_post_meta(get_the_ID(), '_other_details', true);
                $databank_id_field = '_databank_id'; // Replace with the key of your custom field

                $databank_id_value = get_post_meta(get_the_ID(), $databank_id_field, true);
                    echo do_shortcode("[town_page region='".$region."' postal_code='".$postal_code."' category='".$category."']");
                    wp_reset_postdata();
            ?>

            <?php 

            if (isset($_GET['status'])) {
                $status = $_GET['status'];
                if ($status === 'success') {
                    echo '<div class="alert alert-success">Page successfully claimed</div>';
                } elseif ($status === 'failed') {
                    echo '<div class="alert alert-failed">Failed, something went wrong</div>';
                }
            }

            if (isset($_GET['message=status'])) {
                $status = $_GET['message_status'];
                if ($status === 'success') {
                    echo '<div class="alert alert-success">Din besked blev sendt.</div>';
                }
            }
            ?>

            <div class="firma-wrapper">
                    <div class="firma-left-wrapper">
                        <div class="firma-image">
                            <?php echo $featured_image; ?>
                        </div>
                         <div class="firma-schedule-today">
                           <?php echo do_shortcode('[seofy_opening_hours_current]'); ?>
                        </div>
                        <?php if ($data_email != ""){ ?>
                        <div class="claim-listing-btn">
                            <div class="btn"><a target="_blank" href="<?php echo $wordpress_url; ?>/kontakt-firma?firma_id=<?php echo get_the_ID(); ?>"> Kontakt Firma </a></div>
                        
                        </div>
                        <?php } ?>
                        <div class="contact-info-data">
                            
                            <div id="details">
                                <div class="comp-phone"><?php echo ($data_phone != "")?$data_phone:"<i> Ikke tilgængligt </i>"; ?></div>
                                <div class="comp-website"><?php echo ($data_website != "")?$data_website:"<i> Ikke tilgængligt </i>"; ?></div>
                                <div class="comp-facebook"><?php echo ($data_facebook != "")?$data_facebook:"<i> Ikke tilgængligt </i>"; ?></div>
                                
                            </div>  
                            <div class="comp-address">
                                <label class="firma-address"> <i class="fa fa-map-marker" aria-hidden="true"></i> Adresse </label>
                                <span><?php echo $street_name.' '.$street_number.' '.$postal_code.' '.$postal_area; ?></span>
                            </div>
                            <div class="comp-review">
                                <label class="firma-address"> <i class="fa-solid fa-feather"></i> Skriv en anmeldelse </label>
                                <span><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                            </div>
                        
                        </div>
                        <div class="near-firma-wrapper">
                            <div class="near-firma">
                                <label class="near-firma-label"> <i class="fa-solid fa-location-arrow"></i> Nærmeste  <?php echo $category; ?></label>
                                <div class="nearest-firmas">
                                <?php echo do_shortcode("[town_nearest_companies region='".$region."' postal_code='".$postal_code."']"); wp_reset_postdata(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="firma-right-wrapper">
                        <div class="firma-header">
                            <h2><?php echo  $post_title; ?></h3>
                            <div class="claim-listing-btn">
                                <?php if ($is_claimed === "no" || $is_claimed == ""){ ?>
                                    <div class="btn"><a target="_blank" href="<?php echo $wordpress_url; ?>/gor-krav-pa-din-virksomhed?firma_id=<?php echo get_the_ID(); ?>"> Gør krav på denne virksomhed</a></div>
                                <?php } else { ?>
                                    <div class="claimed-wrapper">  <i class="fa-solid fa-circle-check"></i> Hævdede </div>
                                <?php } ?>
                            </div>
                            <div class="firma-category">
                                <div class="comp-category"><i class="fa-solid fa-tag"></i> <?php echo $category; ?></div>
                            </div>

                            
                        </div>
                                                    
                        <div class="entry-content">
                            <?php the_content(); ?>
                            
                        </div><!-- .entry-content -->

                        
                        <div class="opening-hours-wrapper">
                        <?php  echo do_shortcode('[seofy_opening_hours]'); ?>
                        </div>
                        <div class="other-details-wrapper">
                            <h4> Andre detaljer </h4>
                            <div class="other-details-content">
                            <?php if ($other_details != ""){ ?>
                                    <?php echo $other_details; ?>
                                <?php } else { ?>
                                    <div class="no-other-details"><i> Ikke tilgængligt </i></div>
                            <?php } ?>
                            </div>
                        </div>
                        <div class="full-map-area">
                            <h4>Lokation</h4>
                            <?php if ($lat_map != "" && $lon_map !=""){ ?>
                            <iframe 
                                    width="100%" 
                                    height="400" 
                                    frameborder="0" 
                                    scrolling="no" 
                                    marginheight="0" 
                                    marginwidth="0" 
                                    loading="async"
                                    src="https://maps.google.com/maps?q=<?php echo $lat_map; ?>,<?php echo $lon_map; ?>&hl=da&z=14&amp;output=embed"
                                    >
                                </iframe>
                            <?php } else { ?>
                                <iframe 
                                    width="100%" 
                                    height="400" 
                                    frameborder="0" 
                                    scrolling="no" 
                                    marginheight="0" 
                                    marginwidth="0" 
                                    loading="async"
                                    src="https://maps.google.com/maps?q=<?php echo $street_name.' '.$street_number.' '.$postal_code.' '.$postal_area; ?>&hl=da&z=14&amp;output=embed"
                                    >
                                </iframe>
                            <?php } ?>
                        </div>
                        <div class="claim-footer-note">
                            <?php if ($is_claimed === "no" || $is_claimed == ""){ ?>
                                <div class="unclaimed-message">
                                    <p>Profilen er lavet af <?php echo $site_url_without_https; ?> på baggrund af offentligt tilgængelige oplysninger. <br> <a href="<?php echo $wordpress_url; ?>/gor-krav-pa-din-virksomhed?firma_id=<?php echo get_the_ID(); ?>"> Indgiv rettelser her  </a></p>
                                </div>


                            <?php } else { ?>
                                <div class="claimed-message">
                                    <p> Profilen er lavet af <?php echo $post_title; ?>. </p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

            </div>

        <?php endwhile; ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>