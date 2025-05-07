jQuery(document).ready(function($) {
    var paged = 1;
    var loading = false;
    var max_pages = 1;

    function loadPosts() {
        if (loading || paged > max_pages) return;

        loading = true;
        $('#loading-spinner').show(); // Show the loader

        $.ajax({
            type: 'POST',
            url: ajax_pagination_params.ajax_url,
            data: {
                action: 'load_town_directory',
                paged: paged,
                postal_codes: ajax_pagination_params.postal_codes,
                health_category: ajax_pagination_params.health_category
            },
            success: function(response) {
                if (response.success) {
                    $('#town-directory-container').append(response.data.html);
                    max_pages = response.data.max_pages;
                    paged++;
                    loading = false;

                    if (paged > max_pages) {
                        $('#town-directory-pagination').hide();
                    }
                } else {
                    $('#town-directory-pagination').hide();
                }

                $('#loading-spinner').hide(); // Hide the loader after content is loaded
            },
            error: function() {
                $('#loading-spinner').hide(); // Hide the loader in case of an error
            }
        });
    }

    // Initial load
    loadPosts();

    // Handle "Load More" button click
    $('#town-directory-pagination').on('click', '#load-more-posts', function(e) {
        e.preventDefault();
        loadPosts();
    });
});
