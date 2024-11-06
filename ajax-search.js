jQuery(document).ready(function ($) {
    var $searchInput = $('#search-input');
    var $blogHeader = $('.filter-blog-header');
    var $blogItemsContainer = $('.filter-blog-item-container.hide-on-search');
    var $loadmoreContainer = $('.load-more-wrapper.hide-on-search');
    var $blogItems = $('.filter-blog-item-container .blog-item');
    var offset = 0;
    var postsPerPage = 9;

    $searchInput.on('input', function () {
        if ($(this).val()) {
            $blogHeader.addClass('typing-active');
            $blogItemsContainer.hide();
            $blogItems.hide();
            $('#search-results').show();
            $loadmoreContainer.hide();
        } else {
            $blogHeader.removeClass('typing-active');
            $blogItemsContainer.show();
            $blogItems.show();
            $('#search-results').hide();
            $loadmoreContainer.show();
        }

        var query = $(this).val();
        if (query.length < 3) {
            $('#search-results').html('<p>Please enter at least 3 characters.</p>').show();
        } else {
            offset = 0;
            performSearch(query, offset);
        }
    });

    function performSearch(query, offset = 0) {
        $.ajax({
            url: ajax_search.ajax_url,
            method: 'POST',
            data: {
                action: 'ajax_search',
                search_query: query,
                offset: offset
            },
            success: function (response) {
                if (response.success) {
                    if (offset === 0) {
                        $('#search-results').html(response.data).show();
                    } else {
                        $('#search-results').append(response.data);
                    }
                } else {
                    $('#search-results').html(response.data).show();
                }
            },
            error: function () {
                $('#search-results').html('<p>Error retrieving results</p>').show();
            }
        });
    }

    $('#search-submit').on('click', function () {
        offset = 0;
        var query = $searchInput.val();
        performSearch(query, offset);
    });

    $searchInput.on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            offset = 0;
            var query = $(this).val();
            performSearch(query, offset);
        }
    });

    $('#search-results').on('click', '#load-more-search-btn', function (e) {
        e.preventDefault();
        offset += postsPerPage;
        var query = $searchInput.val();
        performSearch(query, offset);
        $(this).parent().remove();
    });
});
