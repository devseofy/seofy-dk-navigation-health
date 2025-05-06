<div class="company-list-row">
    <div class="inner-flex-cont-row">
        <div class="col-sm">
            <div class="comp-thumbnail">
                <a href="<?php echo get_the_permalink(); ?>">
                    <?php echo get_the_post_thumbnail(get_the_ID(), 'medium'); ?>
                </a>
            </div>
        </div>
        <div class="col-sm">
            <div class="comp-name"><h3><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h3></div>
            <?php $category = get_post_meta(get_the_ID(), '_directory_category', true); ?>
            <div class="firma-category">
                <?php 
                $categories = explode(',', $category); // Split by comma
                foreach ($categories as $cat): ?>
                    <div class="comp-category">
                        <i class="fa-solid fa-tag"></i> <?php echo html_entity_decode(trim($cat)); ?>
                    </div>
                <?php endforeach; ?>

            </div>
            <div class="comp-address"><p>Adresse: <?php echo get_post_meta(get_the_ID(), '_street_name', true) . ' ' . get_post_meta(get_the_ID(), '_street_number', true) . '<br>' . get_post_meta(get_the_ID(), '_postal_code', true) . ' ' . get_post_meta(get_the_ID(), '_postal_area', true); ?></p></div>
        </div>
    </div>
    <div class="schedule-area">
        <?php echo do_shortcode('[seofy_opening_hours_current]'); ?>
        <div class="comp-btn">
            <a href="<?php echo get_the_permalink(); ?>">Kontakt</a>
        </div>
    </div>
    <div class="company-address-map">
        <iframe 
            width="100%" 
            height="210" 
            frameborder="0" 
            scrolling="no" 
            marginheight="0" 
            marginwidth="0" 
            loading="async"
            src="https://maps.google.com/maps?q=<?php echo get_post_meta(get_the_ID(), '_lat_map', true); ?>,<?php echo get_post_meta(get_the_ID(), '_lon_map', true); ?>&hl=da&z=14&amp;output=embed"
            >
        </iframe>
    </div>
</div>
