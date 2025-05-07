<div class="company-list-row">
    <div class="company-content">
        <div class="company-thumbnail">
            <a href="<?php echo get_the_permalink(); ?>">
                <?php echo get_the_post_thumbnail(get_the_ID(), 'medium'); ?>
            </a>
            <div class="company-schedule">
                <?php echo do_shortcode('[seofy_opening_hours_current]'); ?>
                <div class="contact-button">
                    <a href="<?php echo get_the_permalink(); ?>">Kontakt</a>
                </div>
            </div>
        </div>
        <div class="company-info">
            <h3 class="company-name">
                <a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a>
            </h3>
            <p class="company-address">
                <?php echo get_post_meta(get_the_ID(), '_street_name', true) . ' ' . get_post_meta(get_the_ID(), '_street_number', true) . '<br>' . get_post_meta(get_the_ID(), '_postal_code', true) . ' ' . get_post_meta(get_the_ID(), '_postal_area', true); ?>
            </p>
            <p>
                <?php echo get_post_meta(get_the_ID(), 'rank_math_description', true); ?>
            </p>
            <div class="company-categories">
                <?php 
                $categories = explode(',', get_post_meta(get_the_ID(), '_directory_category', true));
                foreach ($categories as $cat): ?>
                    <span class="category-tag"><i class="fa-solid fa-tag"></i> <?php echo html_entity_decode(trim($cat)); ?></span>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>
