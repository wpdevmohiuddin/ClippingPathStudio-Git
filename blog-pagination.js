jQuery(document).ready(function ($) {
    var ajaxUrl = blog_ajax.ajax_url;
    var $blogContainer = $('#blog-container');
    var currentCategory = $('.filter-blog-nav li.nav-active').data('blog-cat-id');
    var currentPage = 1;
    var isLoading = false;
    var hasMorePosts = true;

    function loadBlogPosts(categoryId, page) {
        if (isLoading || !hasMorePosts) return;
    
        isLoading = true;
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'cps_blog_filter',
                category_id: categoryId,
                paged: page,
            },
            beforeSend: function () {
                $('#load-more-btn').text('Loading...').prop('disabled', true);
            },
            success: function (response) {
                if (response) {
                    $blogContainer.append(response);
                    currentPage = page;
                } else {
                    hasMorePosts = false;
                    $('#load-more-btn').text('No more posts').prop('disabled', true);
                }
            },
            complete: function () {
                $('#load-more-btn').text('Load More').prop('disabled', false);
                isLoading = false;
            }
        });
    }

    $('.filter-blog-nav li').on('click', function () {
        var categoryId = $(this).data('blog-cat-id');

        if (categoryId !== currentCategory) {
            currentCategory = categoryId;
            currentPage = 1;
            hasMorePosts = true;
            $('.filter-blog-nav li').removeClass('nav-active');
            $(this).addClass('nav-active');

            $blogContainer.html('');
            loadBlogPosts(currentCategory === 'all' ? '' : currentCategory, currentPage);
        }
    });

    $('#load-more-btn').on('click', function (e) {
        e.preventDefault();
        if (!isLoading) {
            loadBlogPosts(currentCategory, currentPage + 1);
        }
    });
});