jQuery(document).ready(function ($) {
    var ajaxUrl = blog_ajax.ajaxUrl;
    var currentCategory = 'all';
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
                action: 'custom_load_blog_posts',
                category_id: categoryId,
                paged: page,
            },
            beforeSend: function () {
                $('#load-more-btn').text('Loading...').prop('disabled', true);
            },
            success: function (response) {
                if (response) {
                    $('#blog-container').append(response);
                    currentPage++;
                } else {
                    hasMorePosts = false;
                    $('#load-more-btn').hide();
                    $('#custom-no-more-posts').show();
                }
            },
            complete: function () {
                $('#load-more-btn').text('Load More').prop('disabled', false);
                isLoading = false;
                $('.filter-blog-item-container').removeClass('loading');
            }
        });
    }

    $('.filter-blog-nav li').on('click', function () {
        var categoryId = $(this).data('blog-cat-id');

        if (categoryId !== currentCategory) {
            currentCategory = categoryId;
            currentPage = 1;
            hasMorePosts = true;
            $('#blog-container').html('');
            $('#load-more-btn').show();
            $('#custom-no-more-posts').hide();
            $('.filter-blog-nav li').removeClass('nav-active');
            $(this).addClass('nav-active');

            loadBlogPosts(currentCategory === 'all' ? '' : currentCategory, currentPage);
        }
    });

    $('#load-more-btn').on('click', function (e) {
        e.preventDefault();
        loadBlogPosts(currentCategory === 'all' ? '' : currentCategory, currentPage);
    });

    loadBlogPosts(currentCategory, currentPage);
});
