<?php
/*
Template Name: Custom Search Results
*/
get_header();
?>


<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <header class="page-header">
            <h1 class="page-title"><?php printf(__('Du søgte på: %s', 'your-theme-textdomain'), get_search_query()); ?></h1>
        </header>

        <?php

		// Process search query and display results
		if (isset($_GET['s']) && !empty($_GET['s'])) {
            $search_query = sanitize_text_field($_GET['s']);
            ?>
                <div class="search-box-area">
                    <p> Vi fandt desværre ingen by på din søgning, du kan vælge byer på listen herunder. </p>
                    <?php echo do_shortcode("[town_list]"); ?>
                </div>
            <?php
	
	

        }
        ?>

    </main>
</div>

<?php

get_footer();
?>