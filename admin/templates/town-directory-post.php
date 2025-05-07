<div class="company-list-row">
    <div class="company-main-content">
        <div class="company-thumbnail">
            <a href="<?php echo get_the_permalink(); ?>">
                <?php echo get_the_post_thumbnail(get_the_ID(), 'medium'); ?>
            </a>
        </div>
        <div class="company-info-section">
            <h3 class="company-name">
                <a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a>
            </h3>
            <p class="company-address">
                <?php echo get_post_meta(get_the_ID(), '_street_name', true) . ' ' . get_post_meta(get_the_ID(), '_street_number', true) . '<br>' . get_post_meta(get_the_ID(), '_postal_code', true) . ' ' . get_post_meta(get_the_ID(), '_postal_area', true); ?>
            </p>
            <div class="company-categories">
                <?php 
                $categories = explode(',', get_post_meta(get_the_ID(), '_directory_category', true));
                foreach ($categories as $cat): ?>
                    <span class="category-tag"><i class="fa-solid fa-tag"></i> <?php echo html_entity_decode(trim($cat)); ?></span>
                <?php endforeach; ?>
            </div>

            <div class="company-schedule">
                <?php echo do_shortcode('[seofy_opening_hours_current]'); ?>
            </div>

            <div class="contact-row">
                <a href="<?php echo get_the_permalink(); ?>" class="contact-button">Kontakt</a>
                <button class="map-toggle-button" data-map-id="map-<?php echo get_the_ID(); ?>">Se kort</button>
            </div>

            <div class="map-tooltip" id="map-<?php echo get_the_ID(); ?>">
                <iframe 
                    width="100%" 
                    height="210" 
                    frameborder="0" 
                    loading="lazy"
                    src="https://maps.google.com/maps?q=<?php echo get_post_meta(get_the_ID(), '_lat_map', true); ?>,<?php echo get_post_meta(get_the_ID(), '_lon_map', true); ?>&hl=da&z=14&output=embed"
                ></iframe>
            </div>
        </div>
    </div>
</div>
