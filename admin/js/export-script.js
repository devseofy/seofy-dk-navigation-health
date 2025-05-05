jQuery(document).ready(function($) {
    $('#start-export').on('click', function() {
        $('#progress-bar').show();
        $('#progress-text').show();
        $('#success-message').hide();

        $.ajax({
            url: myexportData.ajax_url,
            type: 'POST',
            data: {
                action: 'my_export_companies',
                security: myexportData.nonce
            },
            success: function(response) {
                if (response.success) {
                    let posts = response.data;
                    let total = posts.length;
                    let counter = 0;
                    $('#total').text(total);

                    function sendPostData(index) {
                        if (index >= total) {
                            $('#success-message').show();
                            return;
                        }

                        $.ajax({
                            url: myexportData.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'proxy_export_companies',
                                security: myexportData.nonce,
                                post_data: JSON.stringify(posts[index])
                            },
                            success: function(response) {
                                console.log('Response:', response);  // Log the response
                                //console.log('Post data:', JSON.stringify(posts[index]));
                                counter++;
                                $('#counter').text(counter);
                                $('#progress').css('width', (counter / total * 100) + '%');
                        
                                // Send next post
                                sendPostData(index + 1);
                            }
                        });
                    }

                    // Start sending posts
                    sendPostData(0);
                }
            }
        });
    });
});
