<?php
add_action('wp_enqueue_scripts', 'divi_child_enqueue_styles');
function divi_child_enqueue_styles()
{
    $parent_style = 'parent-style'; // This is 'divi-theme-style' for the Divi theme.

    // Enqueue existing styles and scripts
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css' );
    wp_enqueue_style('owl-carousel-2', get_stylesheet_directory_uri() . '/css/owl.carousel.min.css');
    wp_enqueue_script('owl-carousel-2-script', get_stylesheet_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '2.3.4', true);
    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.0/gsap.min.js', array('jquery'), '3.14.3', true);
    wp_enqueue_script('scroll-trigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.0/ScrollTrigger.min.js', array('jquery'), '3.15.3', true);

    // Enqueue your new JavaScript file
//     define('SERVICES_SCROLL_VERSION', '1.0.8');
// 	wp_enqueue_script('services-scroll', get_stylesheet_directory_uri() . '/js/services_scroll.js', array('jquery'), SERVICES_SCROLL_VERSION, true);


    // Conditional scripts for specific pages
    if (is_singular('post') || is_page('contact-us') || is_page('thank-you')) {
        wp_enqueue_script('contact_us_script', get_stylesheet_directory_uri() . '/js/contact_us_script.js', array('jquery'), '1.0', true);
        wp_enqueue_script('thank_you_script', get_stylesheet_directory_uri() . '/js/thank_you_script.js', array('jquery'), '1.0', true);
    }

    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
}


function services_slider_scripts(){
    if( get_post_type()=='service' ){
        wp_enqueue_style('owl-carousel-2', get_stylesheet_directory_uri() . '/css/owl.carousel.min.css');
        wp_enqueue_script('owl-carousel-2-script', get_stylesheet_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '2.3.4', true);
    }
}
add_action('wp_enqueue_scripts', 'services_slider_scripts',200);


function clippingpath_custom_scripts() {
    wp_enqueue_script('before-after-slider', get_stylesheet_directory_uri() . '/js/before-after-slider.js', array('jquery'), '1.0.5', true);
    wp_enqueue_script('blog-ajax', get_stylesheet_directory_uri() . '/js/blog-ajax.js', array('jquery'), '3.0.1', true);
    wp_localize_script('blog-ajax', 'blog_ajax', array('ajaxUrl' => admin_url('admin-ajax.php')));
    wp_enqueue_script('custom-ajax-search', get_stylesheet_directory_uri() . '/js/ajax-search.js', array('jquery'), '2.0.1', true);
    wp_localize_script('custom-ajax-search', 'ajax_search', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_enqueue_style('style.css', get_stylesheet_directory_uri() . '/style.css');

    $loader_svg_url = get_stylesheet_directory_uri() . '/images/loader.svg';
    $custom_css = "
        .filter-blog-item-container.loading:before {
            content: url('" . esc_url($loader_svg_url) . "');
            position: absolute;
            top: 57%;
            left: 50%;
            transform: translate(-57%, -50%);
            width: 55px;
            z-index: 9;
        }
    ";
    wp_add_inline_style('style.css', $custom_css);
}
add_action('wp_enqueue_scripts', 'clippingpath_custom_scripts', 10);





/* --------------------------------------- */
/*            Get Posts by Order           */
/* --------------------------------------- */
add_action('pre_get_posts', 'order_properties');
function order_properties($query)
{
    if (isset($_GET["orderby"])) {
        $value = strtoupper(filter_input(INPUT_GET, 'orderby', FILTER_SANITIZE_STRING));
        $order = strtoupper(filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING));
        if ($order == 'asc') {
            $order = 'ASC';
        } elseif ($order == 'desc') {
            $order = 'DESC';
        }
        $query->set('orderby', $value);
        $query->set('order', $order);
    }
}

/* --------------------------------------- */
/*             Control Excerpt             */
/* --------------------------------------- */
add_filter('excerpt_length', function ($length) {
    return 20;
});
add_filter('excerpt_more', function ($more) {
    return '...';
});

/* --------------------------------------- */
/*           Popular Posts Widget          */
/* --------------------------------------- */
require get_stylesheet_directory() . '/inc/widgets/popular_posts.php';
//Register Popular_Post_Widget widget
function register_popular_post_widget()
{
    register_widget('Popular_Post_Widget');
}
add_action('widgets_init', 'register_popular_post_widget');

//Register Related_Post_Widget widget
require get_stylesheet_directory() . '/inc/widgets/related_posts.php';

function register_related_post_widget()
{
    register_widget('Related_Post_Widget');
}
add_action('widgets_init', 'register_related_post_widget');

/* --------------------------------------- */
/*      Post Author and date shortcode     */
/* --------------------------------------- */
add_shortcode('post-author-date', 'kt_post_author_date_shortcode');
function kt_post_author_date_shortcode($atts)
{
    ob_start();
    $post_id = get_the_ID();
    if ($post_id) {
	    $wpm = 200;
	    $text_content = strip_shortcodes( get_the_content() );
	    $str_content = strip_tags( $text_content );
	    $word_count = str_word_count( $str_content );
	    $readtime = ceil( $word_count / $wpm );
	    if ($readtime == 1) {
		    $postfix = " minute";
	    } else {
		    $postfix = " minutes";
	    }
	    $readingtime = $readtime . $postfix;
        ?>
        <div class="post-meta">
            <div class="post-author">
                <?php echo get_avatar(get_the_author_meta('ID'), 32); ?>
                <p>By <a href="<?php echo get_author_posts_url( get_the_author_meta('ID') ); ?>"><?php echo get_the_author(); ?></a></p>
            </div>
            <span class="post-meta-separator">|</span>
            <div class="post-date"><p><?php echo get_the_date('M j, Y', $post_id); ?></p></div>
            <span class="post-meta-separator">|</span>
            <div class="post-reading-time"><p><?php echo $readingtime." read"; ?></p></div>
        </div>
    <?php $postVariable = ob_get_clean();
        return $postVariable;
    }
}

/* --------------------------------------- */
/*        Get Related Posts of Post        */
/* --------------------------------------- */
function codeless_get_related_posts($post_id, $related_count, $args = array())
{
    $terms = get_the_terms($post_id, 'category');

    if (empty($terms)) $terms = array();

    $term_list = wp_list_pluck($terms, 'slug');

    $related_args = array(
        'post_type' => 'post',
        'posts_per_page' => $related_count,
        'post_status' => 'publish',
        'post__not_in' => array($post_id),
        'orderby' => 'rand',
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $term_list
            )
        )
    );
    return new WP_Query($related_args);
}

/* --------------------------------------- */
/*         Related Posts Shortcode         */
/* --------------------------------------- */
add_shortcode('related-posts', 'kt_related_posts_shortcode');
function kt_related_posts_shortcode($atts)
{
	ob_start();
	$the_query = codeless_get_related_posts(get_the_ID(), 3);
	global $cl_from_element;
	$cl_from_element['is_related'] = true;

	if ($the_query->have_posts()) { ?>
        <ul class="related-posts-listing">
			<?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
                <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php if (has_post_thumbnail()) { ?>
                        <a href="<?php the_permalink(); ?>">
                            <div class="post-image" style="background-image: url(<?php the_post_thumbnail_url('full'); ?>)"></div>
                        </a>
					<?php } else { ?>
                        <a href="<?php the_permalink(); ?>">
                            <div class="post-image" style="background-image: url(<?php echo wp_get_attachment_url(916); ?>)"></div>
                        </a>
					<?php } ?>
                    <a class="related-post-title" href="<?php the_permalink(); ?>">
                        <h2><?php the_title(); ?></h2>
                    </a>
                    <div class="related-post-meta">
                        <p><span>By <?php the_author(); ?></span> | <span><?php the_date(); ?></span> | <span><?php echo get_the_category_list( ', ', ', ' ); ?></span></p>
                    </div>
                </li>
			<?php endwhile;
			wp_reset_postdata(); ?>
        </ul>
		<?php
		wp_reset_query();
		$cl_from_element['is_related'] = false;
		$postVariable = ob_get_clean();
		return $postVariable;
	}
}

/* --------------------------------------- */
/*    Enable upload for webp image files   */
/* --------------------------------------- */
function webp_upload_mimes($existing_mimes)
{
    $existing_mimes['webp'] = 'image/webp';
    return $existing_mimes;
}
add_filter('mime_types', 'webp_upload_mimes');

/* --- enable preview webp image files. -- */
function webp_is_displayable($result, $path)
{
    if ($result === false) {
        $displayable_image_types = array(IMAGETYPE_WEBP);
        $info = @getimagesize($path);

        if (empty($info)) {
            $result = false;
        } elseif (!in_array($info[2], $displayable_image_types)) {
            $result = false;
        } else {
            $result = true;
        }
    }

    return $result;
}
add_filter('file_is_displayable_image', 'webp_is_displayable', 10, 2);

function is_blog()
{
    return (is_archive() || is_author() || is_category() || is_home() || is_single() || is_tag()) && 'post' == get_post_type();
}

/* --------------------------------------- */
/*     Shortcode for CTA by Kazi Tihum     */
/* --------------------------------------- */
function kt_cta_shortcode($atts = array())
{

    // set up default parameters
    extract(shortcode_atts(array(
        'title' => 'Start your free trial today.<br>No credit card required.',
        'button_text' => 'START FREE TRIAL',
        'button_link' => '#',
        'theme' => '1',
        'background_image' => '/wp-content/uploads/2015/10/slide_product_bg.jpg',
        'text_color' => 'light',
        'text_align' => 'center',
    ), $atts));

    ob_start();
    ?>
    <div class="dynamic-cta theme-<?php echo $theme; ?> text-<?php echo $text_color; ?>" style="background-image: url(<?php echo $background_image; ?>); text-align: <?php echo $text_align; ?>">
        <h2><?php echo $title; ?></h2>
        <a href="<?php echo $button_link; ?>"><?php echo $button_text; ?></a>
    </div>
<?php
    // Output needs to be return
    $content = ob_get_clean();
    return $content;
}
// register shortcode
add_shortcode('dynamic_cta', 'kt_cta_shortcode');

function new_cta_function ( $attr ):string {
    $attr = shortcode_atts(
        array(
            'content' => '',
            'cta_text' => '',
            'url' => ''
        ),$attr
    );
    $content = "<style>#new_cta_shortcode_wrapper{display:flex;align-items:center;padding-bottom:1em}.new_cta_shortcode_content{display:flex;align-items:center;justify-content:center;text-align:center;width:60%;position:relative;background:#000;z-index:1;min-height:120px;max-height:120px;padding-left:20px}.new_cta_shortcode_content:after{content:'';position:absolute;display:block;width:100%;height:100%;top:0;left:0;z-index:-1;background:#000;transform-origin:bottom left;-ms-transform:skew(-30deg,0deg);-webkit-transform:skew(-30deg,0deg);transform:skew(-30deg,0deg)}.new_cta_shortcode_content p{color:#fff;font-size:24px}.new_cta_shortcode_cta_text{display:flex;align-items:center;justify-content:center;text-align:center;width:40%;background:#73bf44;z-index:0;min-height:120px;max-height:120px}.new_cta_shortcode_cta_text div{background:#000;padding:10px 20px;border-radius:50px}.new_cta_shortcode_cta_text p{color:#fff;font-size:18px}@media (max-width:980px){#new_cta_shortcode_wrapper{display:block}.new_cta_shortcode_content{width:100%;padding:30px 20px 20px;min-height:100%;max-height:100%}.new_cta_shortcode_content:after{display:none}.new_cta_shortcode_cta_text{width:100%}.new_cta_shortcode_content p{font-size:20px}.new_cta_shortcode_cta_text p{font-size:16px}.new_cta_shortcode_cta_text{background:#000;min-height:100%;padding-bottom:25px}.new_cta_shortcode_cta_text div{background:#73bf44}}</style>";
    $content .= '<a href="'.$attr['url'].'" target="_blank"><div id="new_cta_shortcode_wrapper">';
    $content .= "<div class='new_cta_shortcode_content'><p>".$attr['content']."</p></div>";
    $content .= "<div class='new_cta_shortcode_cta_text'><div><p>".$attr['cta_text']."</p></div></div>";
    $content .= '</div></a>';
    return $content;
}
add_shortcode('new_cta_section', 'new_cta_function');

function get_started_cta( $attr ):string {
    $attr = shortcode_atts(
            array(
					'cta_text' => '',
                    'img_url' => ''
            ),$attr
    );
	$content = "<style>.gsc_wrapper{background:url(/wp-content/uploads/2022/06/get_started_cta_bg.png);background-size:cover;padding:40px 25px 40px 40px;margin-bottom:20px}.gsc_content{display:flex;align-items:center;flex-wrap:wrap}.gsc-left{flex:0 0 60%;padding-right:30px}.gsc-right{flex:0 0 40%;line-height:0}.gsc-title{padding-bottom:10px;padding-right:30px;}.gsc-title h4{font-weight:600;line-height:32px;color:#388508}.gsc-email-box{display:flex;flex-wrap:wrap}.gsc-email-box input{width:65%;font-size:12px;background:#FEFDFE;border-radius:3px 0 0 3px;padding:14px 15px;border:none;color:#aaa}.gsc-email-box button{width:35%;font-size:12px;background:#73BF44;border-radius:0 3px 3px 0;font-weight:700;color:#FEFDFE;border:none;cursor:pointer;transition: all 300ms ease 0ms;}.gsc-email-box button:hover{background:#353535;}@media (max-width:767px){.gsc_wrapper{padding:40px 20px}.gsc_content{flex-direction:column-reverse}.gsc-left{padding:0;margin-top:10px}.gsc-title{padding-right:0;}.gsc-title h4{text-align:center;line-height:1.5em}.gsc-email-box input{width:100%;border-radius:3px}.gsc-email-box button{width:100%;border-radius:3px;padding:14px 15px;margin-top:5px}}</style>";
    $content .= '<div class="gsc_wrapper">';
    $content .= '<div class="gsc_content">';
	$content .= '<div class="gsc-left">';
	$content .= '<div class="gsc-title"><h4>'.$attr['cta_text'].'</h4></div>';
	$content .= '<div class="gsc-email-box">';
	$content .= '<input type="email" placeholder="&#9993; Enter E-mail address" value="">';
	$content .= '<button type="submit" class="gsc_btn">GET STARTED</button>';
	$content .= '</div>';
	$content .= '</div>';
	$content .= '<div class="gsc-right">';
	$content .= '<img src="'.$attr['img_url'].'">';
	$content .= '</div>';
    $content .= '</div>';
    $content .= '</div>';
    return $content;
}
add_shortcode('get_started_cta', 'get_started_cta');

add_filter('rank_math/snippet/breadcrumb', function ($entity) {
    return $entity;
});

//This function prints the JavaScript to the footer
function cf7_footer_script(){ ?>
  
<script>
document.addEventListener( 'wpcf7mailsent', function( event ) {
    location = 'https://clippingpathstudio.com/thank-you/';
}, false );
</script>
  
<?php } 
  
add_action('wp_footer', 'cf7_footer_script'); 

// Custom structured url for service post type
add_filter( 'post_type_link', function( $post_link, $post, $leavename ) {

	$post_types = array(
		'service'
	);

	if ( in_array( $post->post_type, $post_types ) && 'publish' === $post->post_status ) {
		$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
	}

	return $post_link;

}, 10, 3 );

add_action( 'pre_get_posts', function( $query ) {

	// Only noop the main query.
	if ( ! $query->is_main_query() ) {
		return;
	}

	// Only noop our very specific rewrite rule match.
	if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
		return;
	}

	// 'name' will be set if post permalinks are just post_name, otherwise the page rule will match.
	if ( ! empty( $query->query['name'] ) ) {

			$post_types = array(
			'post', // important to  not break your standard posts
			'page', // important to  not break your standard pages
			'service'
		);

		$query->set( 'post_type', $post_types );

	}

} );

function dpdfg_custom_filters_taxonomies($taxonomies, $props) {
	return 'cps_gallery_category';
}
add_filter('dpdfg_custom_filters_taxonomies', 'dpdfg_custom_filters_taxonomies', 10, 2);

function dp_dfg_custom_lightbox($images) {
	if(empty($images) && has_post_thumbnail()){
		$images[] = get_the_post_thumbnail_url();
	}
	return $images;
}
add_filter('dp_dfg_custom_lightbox', 'dp_dfg_custom_lightbox', 10, 1);

function custom_mobile_menu_script(){
	wp_enqueue_script('module_customizer', get_stylesheet_directory_uri() . '/js/module_customizer.js', array( 'jquery' ), '5.9.70', true );
}
add_action('wp_enqueue_scripts', 'custom_mobile_menu_script',200);

function my_theme_load_scripts(){
    if( is_category() ){
        wp_enqueue_script('selected_filter_category', get_stylesheet_directory_uri() . '/js/selected_filter_category.js', array( 'dp-divi-filtergrid-frontend-bundle' ), '1.0', true );
    }
}
add_action('wp_enqueue_scripts', 'my_theme_load_scripts',200);

/**
 * Redirect to the homepage all users trying to access feeds.
 */
function disable_feeds() {
	wp_redirect( home_url() );
	die;
}

// Disable global RSS, RDF & Atom feeds.
add_action( 'do_feed',      'disable_feeds', -1 );
add_action( 'do_feed_rdf',  'disable_feeds', -1 );
add_action( 'do_feed_rss',  'disable_feeds', -1 );
add_action( 'do_feed_rss2', 'disable_feeds', -1 );
add_action( 'do_feed_atom', 'disable_feeds', -1 );

// Disable comment feeds.
add_action( 'do_feed_rss2_comments', 'disable_feeds', -1 );
add_action( 'do_feed_atom_comments', 'disable_feeds', -1 );

// Prevent feed links from being inserted in the <head> of the page.
add_action( 'feed_links_show_posts_feed',    '__return_false', -1 );
add_action( 'feed_links_show_comments_feed', '__return_false', -1 );
remove_action( 'wp_head', 'feed_links',       2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );

// Price Calculator
function priceCalculator() {
    ob_start();
    ?>
    <div class="price_calculator_wrapper">
        <div class="services_item_container">
            <div class="service_item active" data-value="Clipping Path">
                <div class="service_item_details">
                    <h2>Clipping Path</h2>
                    <p>Our clipping path service is e-business oriented, and the service includes fine-drawn manual Photoshop clipping path service by professionals. We ensure faster, cost-effective, and easy-to-order service.</p>
                    <div class="included_service_item">
                        <p>What's Included</p>
                        <ul>
                            <li>Fast Turnaround</li>
                            <li>Budget-friendly</li>
                            <li>Quick Response 24/7</li>
                            <li>High-quality Images</li>
                        </ul>
                    </div>
                </div>
                <div class="services_complexity_items">
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Simple</p>
                            <span class="tooltip"><p class="tooltip-text">A simple clipping path is for images of round, rectangular, and small curved-shaped, requiring a single path & a straight curve. For example, wheels, books, balls, plates, etc.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Simple-Clipping-Path.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$0.29 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Medium</p>
                            <span class="tooltip"><p class="tooltip-text">Medium clipping path requires more focus and contains designs, holes, and anchor points in the images. This service applies to bracelets, group shoes or rings, etc.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Hard-Clipping-Path.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$1.49 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Complex</p>
                            <span class="tooltip"><p class="tooltip-text">When images have more designs, shapes, curves, and holes, we treat them as complex. The service includes the chain, jewelry, group people, etc.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Complex-Clipping-Path.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$7 <span>/Per photo</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service_item" data-value="Background Removal">
                <div class="service_item_details">
                    <h2>Background Removal</h2>
                    <p>Background removal of your image involves removing unwanted background objects. Unwanted objects in a photo distract buyers or potential customers, especially images with unwanted backgrounds. See our background removal low rates in different categories.</p>
                    <div class="included_service_item">
                        <p>What's Included</p>
                        <ul>
                            <li>Fast Turnaround</li>
                            <li>Budget-friendly</li>
                            <li>Quick Response 24/7</li>
                            <li>High-quality Images</li>
                        </ul>
                    </div>
                </div>
                <div class="services_complexity_items">
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Simple</p>
                            <span class="tooltip"><p class="tooltip-text">Simple removal implies a single path, straight curves, shape, solid body, and no holes in images. It applies to round and rectangular images such as plates, rings, mobile, etc.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Simple-Background-Removal.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$0.29 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Medium</p>
                            <span class="tooltip"><p class="tooltip-text">Medium removal means few straight curves, anchor points, not many holes, and a bit complex shape. Medium removal applies to such as watches, earrings, and clothes. </p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Hard-Background-Removal.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$1.49 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Complex</p>
                            <span class="tooltip"><p class="tooltip-text">Complex service includes curves, anchor points, complex body shapes, and holes. Also, it includes diagonal-shaped products such as jewelry, trees, fabrics, and more.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Complex-Background-Removal.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$7 <span>/Per photo</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service_item" data-value="Multi Clipping Path">
                <div class="service_item_details">
                    <h2>Multi Clipping Path</h2>
                    <p>The multi-clipping path includes working in multiple layers, and this work is more time-consuming, from simple to complex clipping path. Our clipping work is proven excellent. So, here are our pricing categories under the multi-clipping path.</p>
                    <div class="included_service_item">
                        <p>What's Included</p>
                        <ul>
                            <li>Fast Turnaround</li>
                            <li>Budget-friendly</li>
                            <li>Quick Response 24/7</li>
                            <li>High-quality Images</li>
                        </ul>
                    </div>
                </div>
                <div class="services_complexity_items">
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Simple</p>
                            <span class="tooltip"><p class="tooltip-text">Simple multi-clipping path means the editing includes only 2-5 layers of work with straight curves. The service applies to rectangular, small curved shaped, and round images.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Simple-Multi-Clipping-Path.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$2 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Medium</p>
                            <span class="tooltip"><p class="tooltip-text">Medium multi-clipping path includes 6-8 layers while editing multiple paths, holes, and more anchor points. The service applies to watches, rings, foods, and motor parts. </p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Hard-Multi-Clipping-Path.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$4 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Complex</p>
                            <span class="tooltip"><p class="tooltip-text">Complex path defines more layers here for editing, paths, holes in images with complex, compound shapes, and designs curves. Service applies to jewelry, furniture, and more.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Complex-Multi-Clipping-Path.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$10 <span>/Per photo</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service_item" data-value="Shadow Making">
                <div class="service_item_details">
                    <h2>Shadow Making</h2>
                    <p>Keeping natural shadows and removing other parts of the photo is challenging. So, you can take our service for the shadow-making service. So, here is our pricing structure for shadow-making.</p>
                    <div class="included_service_item">
                        <p>What's Included</p>
                        <ul>
                            <li>Fast Turnaround</li>
                            <li>Budget-friendly</li>
                            <li>Quick Response 24/7</li>
                            <li>High-quality Images</li>
                        </ul>
                    </div>
                </div>
                <div class="services_complexity_items">
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Simple</p>
                            <span class="tooltip"><p class="tooltip-text">Simple shadow making defines keeping natural shadow and removing background using Photoshop filter. Keeping shadows gives the focused subject a natural, realistic look.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Simple-Shadow-Making.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$0.3 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Medium</p>
                            <span class="tooltip"><p class="tooltip-text">Medium shadow makes the natural shadow while using the Photoshop brush tool according to the subject. The shadow makes the light source natural and makes the subject look 3D.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Hard-Shadow-Making.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$0.5 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Complex</p>
                            <span class="tooltip"><p class="tooltip-text">Complex shadow works on keeping both the reflection and shadow of the subjects. Complex shadow-making service makes the subject looks more natural and realistic here.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Complex-Shadow-Making.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$0.7 <span>/Per photo</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service_item" data-value="Image Masking">
                <div class="service_item_details">
                    <h2>Image Masking</h2>
                    <p>When you need to edit out the image background and you require providing a realistic feel to your image, then our service would be an immense help. Here is our pricing structure for the image masking service.</p>
                    <div class="included_service_item">
                        <p>What's Included</p>
                        <ul>
                            <li>Fast Turnaround</li>
                            <li>Budget-friendly</li>
                            <li>Quick Response 24/7</li>
                            <li>High-quality Images</li>
                        </ul>
                    </div>
                </div>
                <div class="services_complexity_items">
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Simple</p>
                            <span class="tooltip"><p class="tooltip-text">Simple image masking requires one applicable focused image and the editing here is easier. The subject color and background color are solid and different here.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Simple-Image-Masking.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$0.6 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Medium</p>
                            <span class="tooltip"><p class="tooltip-text">Medium image masking is for the several subjects in the image. Subject color and background color get solid here, and editing is more difficult than one subject task.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Hard-Image-masking.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$1.2 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Complex</p>
                            <span class="tooltip"><p class="tooltip-text">Complex image masking is a multiple-focused subject. The background color and image color become identical. This is quite a time-consuming task to do.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Complex-image-masking.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$2.5 <span>/Per photo</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service_item" data-value="Photo Retouching">
                <div class="service_item_details">
                    <h2>Photo Retouching</h2>
                    <p>Need to edit your face’s blemishes or wrinkles? Our photo retouching service provides better editing service for the photo retouching service. Here are some of our pricing structures for photo retouching.</p>
                    <div class="included_service_item">
                        <p>What's Included</p>
                        <ul>
                            <li>Fast Turnaround</li>
                            <li>Budget-friendly</li>
                            <li>Quick Response 24/7</li>
                            <li>High-quality Images</li>
                        </ul>
                    </div>
                </div>
                <div class="services_complexity_items">
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Simple</p>
                            <span class="tooltip"><p class="tooltip-text">Simple photo retouching only removes the focused subject’s pimples and wrinkles. This is the right option for you if you need little or little retouching work.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Simple-Photo-Retouching.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$0.6 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Medium</p>
                            <span class="tooltip"><p class="tooltip-text">If your images require more retouching work, this is the right option. This retouching option removes pimples and wrinkles and adjusts lighting, exposures, and color tone.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Hard-Photo-Retouching.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$1.99 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Complex</p>
                            <span class="tooltip"><p class="tooltip-text">This retouching option removes pimples and wrinkles, adjusts lighting, and exposures, fixes color tone, and makes the image more realistic and beautiful.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Complex-Photo-Retouching.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$3.99 <span>/Per photo</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service_item" data-value="Ghost Mannequin">
                <div class="service_item_details">
                    <h2>Ghost Mannequin</h2>
                    <p>When you need to edit your product’s neck joint or remove the image’s dome, our editing service can easily do the joint neck service. Here are our pricing structures for the mannequin service.</p>
                    <div class="included_service_item">
                        <p>What's Included</p>
                        <ul>
                            <li>Fast Turnaround</li>
                            <li>Budget-friendly</li>
                            <li>Quick Response 24/7</li>
                            <li>High-quality Images</li>
                        </ul>
                    </div>
                </div>
                <div class="services_complexity_items">
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Simple</p>
                            <span class="tooltip"><p class="tooltip-text">This simple neck joint service includes adding only the neck part and removing the domes. This is important for e-commerce websites for their clean product display.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Simple-Ghost-Mannequin.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$0.6 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Medium</p>
                            <span class="tooltip"><p class="tooltip-text">We fit the neck at the right point here, which requires extra work. We do the symmetric on left-right and top-bottom to make the image worthwhile for the e-commerce website.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Hard-Ghost-Mannequin.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$0.99 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Complex</p>
                            <span class="tooltip"><p class="tooltip-text">Complex service fits the back and front parts of the focused subject. In addition, placing the subject on the right points while providing a realistic 3D feel to the image.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Complex-Ghost-Mannequin.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$1.5 <span>/Per photo</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service_item" data-value="Color Correction">
                <div class="service_item_details">
                    <h2>Color Correction</h2>
                    <p>If you are in need of color correction work on your images, here is our color correction service and what it features with different categories of pricing. Choose the one you need.</p>
                    <div class="included_service_item">
                        <p>What's Included</p>
                        <ul>
                            <li>Fast Turnaround</li>
                            <li>Budget-friendly</li>
                            <li>Quick Response 24/7</li>
                            <li>High-quality Images</li>
                        </ul>
                    </div>
                </div>
                <div class="services_complexity_items">
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Simple</p>
                            <span class="tooltip"><p class="tooltip-text">Simple work includes only a single color correction and one texture. It’s for single-color images. We need to know your custom color and do the rest of the work.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Simple-Color-Correction.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$0.99 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Medium</p>
                            <span class="tooltip"><p class="tooltip-text">Medium correction requires multiple color correction that includes multiple textures, and we work with mix colored images. Let us know the custom color; we will do the rest.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Hard-Color-Correction.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$1.99 <span>/Per photo</span></p>
                        </div>
                    </div>
                    <div class="complexity_item">
                        <div class="complexity_title">
                            <p>Complex</p>
                            <span class="tooltip"><p class="tooltip-text">Complex color correction fixes the various exposures on the product. It requires removing dull color from the subject’s skin and gives it a real feel. It’s time-consuming.</p></span>
                        </div>
                        <div class="complexity_img">
                            <img src="https://clippingpathstudio.com/wp-content/uploads/2020/06/Complex-color-correction.jpg" />
                        </div>
                        <div class="complexity_price">
                            <p>$2.5 <span>/Per photo</span></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="calculator_container">
            <div class="calculator-row services-row">
                <p>Services</p>
                <span>
                    <select name="service" class="" required="">
                    <option data-turnaround_time="12|24|48" data-simple="0.406|0.29|0.232"
                            data-medium="2.086|1.49|1.192" data-complex="9.8|7|5.6"
                            value="Clipping Path">Clipping Path
                    </option>
                    <option data-turnaround_time="12|24|48" data-simple="0.406|0.29|0.232"
                            data-medium="2.086|1.49|1.192" data-complex="9.8|7|5.6"
                            value="Background Removal">Background Removal
                    </option>
                    <option data-turnaround_time="12|24|48" data-simple="2.8|2|1.6"
                            data-medium="5.6|4|3.2" data-complex="14|10|8"
                            value="Multi Clipping Path">Multi Clipping Path
                    </option>
                    <option data-turnaround_time="12|24|48" data-simple="0.42|0.3|0.24"
                            data-medium="0.7|0.5|0.4" data-complex="0.98|0.7|0.56"
                            value="Shadow Making">Shadow Making
                    </option>
                    <option data-turnaround_time="12|24|48" data-simple="0.84|0.6|0.48"
                            data-medium="1.68|1.2|0.96" data-complex="3.5|2.5|2"
                            value="Image Masking">Image Masking
                    </option>
                    <option data-turnaround_time="12|24|48" data-simple="0.84|0.6|0.48"
                            data-medium="2.786|1.99|1.592" data-complex="5.586|3.99|3.192"
                            value="Photo Retouching">Photo Retouching
                    </option>
                    <option data-turnaround_time="12|24|48" data-simple="0.84|0.6|0.48"
                            data-medium="1.386|0.99|0.792" data-complex="2.1|1.5|1.2"
                            value="Ghost Mannequin">Ghost Mannequin
                    </option>
                    <option data-turnaround_time="12|24|48" data-simple="1.386|0.99|0.792"
                            data-medium="2.786|1.99|1.592" data-complex="3.5|2.5|2"
                            value="Color Correction">Color Correction
                    </option>
                </select>
                </span>
            </div>

            <div class="calculator-row turnaround-time-row">
                <p>Turnaround Time</p>
                <div class="turnaround">
                    <div class="turnaround_12">
                        <input type="radio" id="twelve" name="turnaround" value="12">
                        <label for="twelve">12 hours</label>
                    </div>
                    <div class="turnaround_24">
                        <input type="radio" id="twentyfour" name="turnaround" value="24" checked="checked">
                        <label for="twentyfour">24 hours</label>
                    </div>
                    <div class="turnaround_48">
                        <input type="radio" id="fourtyeight" name="turnaround" value="48">
                        <label for="fourtyeight">48 hours</label>
                    </div>
                </div>
            </div>

            <div class="calculator-row complexity-row">
                <p>Level of Complexity</p>
                <div class="complexity_range">
                    <label for="complexity_range">Simple</label>
                    <input type="range" id="complexity_range" name="complexity_range" min="1" max="3" steps="1" value="1">
                </div>
            </div>

            <div class="calculator-row quantity-row">
                <p>Quantity</p>
                <span class="minus">-</span>
                <input type="number" name="quantity" value="1" class="" required="" min="1">
                <span class="plus">+</span>
            </div>

            <div class="calculator-row results-row">
                <div class="price-estimation">
                    <p></p>
                </div>
                <div class="get-started-btn">
                    <a href="/free-trial/">Start free trial</a>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('price_calculator', 'priceCalculator');

// Custom field for users
function extra_user_profile_fields( $user ) {
    ?>
    <h3><?php _e("Extra profile information", "blank"); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="author_position"><?php _e("Author Title"); ?></label></th>
            <td>
                <input type="text" name="author_position" id="author_position" value="<?php echo esc_attr( get_the_author_meta( 'author_position', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description"><?php _e("Please enter your title."); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="author_fb"><?php _e("Facebook Profile"); ?></label></th>
            <td>
                <input type="url" name="author_fb" id="author_fb" value="<?php echo esc_attr( get_the_author_meta( 'author_fb', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description"><?php _e("Please enter your facebook profile link."); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="author_twitter"><?php _e("Twitter Profile"); ?></label></th>
            <td>
                <input type="url" name="author_twitter" id="author_twitter" value="<?php echo esc_attr( get_the_author_meta( 'author_twitter', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description"><?php _e("Please enter your twitter profile link."); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="author_linkedin"><?php _e("Linkedin Profile"); ?></label></th>
            <td>
                <input type="url" name="author_linkedin" id="author_linkedin" value="<?php echo esc_attr( get_the_author_meta( 'author_linkedin', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description"><?php _e("Please enter your linkedin profile link."); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="author_instagram"><?php _e("Instagram Profile"); ?></label></th>
            <td>
                <input type="url" name="author_instagram" id="author_instagram" value="<?php echo esc_attr( get_the_author_meta( 'author_instagram', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description"><?php _e("Please enter your instagram profile link."); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="author_pinterest"><?php _e("Pinterest Profile"); ?></label></th>
            <td>
                <input type="url" name="author_pinterest" id="author_pinterest" value="<?php echo esc_attr( get_the_author_meta( 'author_pinterest', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description"><?php _e("Please enter your pinterest profile link."); ?></span>
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) ){
		return false;
	}
	update_user_meta( $user_id, 'author_position', $_POST['author_position'] );
	update_user_meta( $user_id, 'author_fb', $_POST['author_fb'] );
	update_user_meta( $user_id, 'author_twitter', $_POST['author_twitter'] );
	update_user_meta( $user_id, 'author_linkedin', $_POST['author_linkedin'] );
	update_user_meta( $user_id, 'author_instagram', $_POST['author_instagram'] );
	update_user_meta( $user_id, 'author_pinterest', $_POST['author_pinterest'] );
}
add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

// Author data
function get_author_details () {
	ob_start();
    $authorTitle = get_the_author_meta( 'author_position', get_the_author_meta('ID') );
	$author_fb = get_the_author_meta( 'author_fb', get_the_author_meta('ID') );
	$author_twitter = get_the_author_meta( 'author_twitter', get_the_author_meta('ID') );
	$author_linkedin = get_the_author_meta( 'author_linkedin', get_the_author_meta('ID') );
	$author_instagram = get_the_author_meta( 'author_instagram', get_the_author_meta('ID') );
	$author_pinterest = get_the_author_meta( 'author_pinterest', get_the_author_meta('ID') );
	$author_fname = get_the_author_meta('user_firstname');
	$author_lname = get_the_author_meta('user_lastname');
	if (!empty($author_fname)|| !empty($author_lname)){
		$author_name = $author_fname.' '.$author_lname;
	}else {
		$author_name = get_the_author_meta('display_name');
	}
	echo "<div class='author_details_box'>";
	echo "<div class='author_details_left_column'>";
	echo "<div class='author_image'>".get_avatar(get_the_author_meta('ID'))."</div>";
	echo "<div class='author_socials'>";
	echo "<div class='also_on'><span>I'm also on</span></div>";
	if (!empty($author_fb)) {
		echo "<span class='et-social-icon et-social-facebook'><a href='".$author_fb."' class='icon' target='_blank' rel='noopener'></a></span>";
	}
	if (!empty($author_twitter)) {
		echo "<span class='et-social-icon et-social-twitter'><a href='".$author_twitter."' class='icon' target='_blank' rel='noopener'></a></span>";
	}
	if (!empty($author_linkedin)) {
		echo "<span class='et-social-icon et-social-linkedin'><a href='".$author_linkedin."' class='icon' target='_blank' rel='noopener'></a></span>";
	}
	if (!empty($author_instagram)) {
		echo "<span class='et-social-icon et-social-instagram'><a href='".$author_instagram."' class='icon' target='_blank' rel='noopener'></a></span>";
	}
	if (!empty($author_pinterest)) {
		echo "<span class='et-social-icon et-social-pinterest'><a href='".$author_pinterest."' class='icon' target='_blank' rel='noopener'></a></span>";
	}
	echo "</div>";
	echo "</div>";
	echo "<div class='author_details_right_column'>";
	echo "<div class='author_name_title'>";
	echo "<h3>".$author_name."</h3>";
	echo "<span> | </span><p>".$authorTitle."</p>";
	echo "</div>";
	echo "<div class='author_short_desc'>";
	echo "<p>".get_the_author_meta('user_description')."</p>";
	echo "<p class='read_more'><a href='".get_author_posts_url( get_the_author_meta('ID') )."'>Read more from ".$author_name."</a></p>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
	return ob_get_clean();
}
add_shortcode('author_details' , 'get_author_details');

// Single Author Page
function get_author_details_s() {
	ob_start();
	$authorTitle = get_the_author_meta( 'author_position', get_the_author_meta('ID') );
	$author_fb = get_the_author_meta( 'author_fb', get_the_author_meta('ID') );
	$author_twitter = get_the_author_meta( 'author_twitter', get_the_author_meta('ID') );
	$author_linkedin = get_the_author_meta( 'author_linkedin', get_the_author_meta('ID') );
	$author_instagram = get_the_author_meta( 'author_instagram', get_the_author_meta('ID') );
	$author_pinterest = get_the_author_meta( 'author_pinterest', get_the_author_meta('ID') );
	$author_fname = get_the_author_meta('user_firstname');
	$author_lname = get_the_author_meta('user_lastname');
	if (!empty($author_fname)|| !empty($author_lname)){
		$author_name = $author_fname.' '.$author_lname;
	}else {
		$author_name = get_the_author_meta('display_name');
	}
    ?>
    <div class="author_details_box">
        <div class="author_details_left_column">
            <div class="author_image">
                <?php echo get_avatar(get_the_author_meta('ID')); ?>
            </div>
        </div>
        <div class="author_details_right_column">
            <div class="author_name_title">
                <h3><?php echo $author_name; ?></h3>
                <span>.</span>
                <p><?php echo $authorTitle; ?></p>
            </div>
            <div class="author_short_desc">
                <p><?php echo get_the_author_meta('user_description'); ?></p>
            </div>
            <div class="author_socials">
		        <?php
		        if (!empty($author_fb)) {
			        echo "<span class='et-social-icon et-social-facebook'><a href='".$author_fb."' class='icon' target='_blank' rel='noopener'></a></span>";
		        }
		        if (!empty($author_twitter)) {
			        echo "<span class='et-social-icon et-social-twitter'><a href='".$author_twitter."' class='icon' target='_blank' rel='noopener'></a></span>";
		        }
		        if (!empty($author_linkedin)) {
			        echo "<span class='et-social-icon et-social-linkedin'><a href='".$author_linkedin."' class='icon' target='_blank' rel='noopener'></a></span>";
		        }
		        if (!empty($author_instagram)) {
			        echo "<span class='et-social-icon et-social-instagram'><a href='".$author_instagram."' class='icon' target='_blank' rel='noopener'></a></span>";
		        }
		        if (!empty($author_pinterest)) {
			        echo "<span class='et-social-icon et-social-pinterest'><a href='".$author_pinterest."' class='icon' target='_blank' rel='noopener'></a></span>";
		        }
		        ?>
            </div>
        </div>
    </div>
    <?php
	return ob_get_clean();
}
add_shortcode('author_details_s' , 'get_author_details_s');

// Post's breadcrumb
function getPostBreadcrumb():string{
    ob_start();
	$categories = get_the_category();
	if ( ! empty( $categories ) ) {
        ?>
        <div class="post_breadcrumb">
            <span><a href="/blog" title="Blog Home">Blog Home</a></span>
            <span class="breadcrumb_separator"></span>
            <span><a href="<?php echo esc_url( get_category_link( $categories[0]->term_id )); ?>" title="<?php echo esc_html( $categories[0]->name ); ?>"><?php echo esc_html( $categories[0]->name ); ?></a></span>
        </div>
        <?php
	}
    return ob_get_clean();
}
add_shortcode('post_breadcrumb' , 'getPostBreadcrumb');

add_filter( 'rank_math/sitemap/enable_caching', '__return_false');

// function disable_contact_form_submission($form) {
//     // Check if it's the specific form you want to disable
//     if ($form->id() == 73029) {
//         // Prevent sending email and processing the form
//         add_filter('wpcf7_mail_components', '__return_false');
//     }
//     return $form;
// }
// add_action('wpcf7_before_send_mail', 'disable_contact_form_submission');

//Add progress-ba in single post
function enqueue_progress_bar_script() {
    // Check if it's a single post page
    if (is_single()) {
        $is_logged_in = is_user_logged_in();
        if ($is_logged_in) {
            echo '<div class="progress-container user_loging">
            <div class="progress-bar" id="progress-bar"></div>
          </div>';
        }else{
            
          echo '<div class="progress-container">
            <div class="progress-bar" id="progress-bar"></div>
          </div>';
        }
        
        
    }
}

add_action('wp_head', 'enqueue_progress_bar_script');

// Schedule contact form email
add_action( 'wpcf7_before_send_mail', 'custom_modify_recipient_based_on_time' );
function custom_modify_recipient_based_on_time( $contact_form ) {
    $submission = WPCF7_Submission::get_instance();

    if ( $submission ) {
        $posted_data = $submission->get_posted_data();

        // Get the current time
        $time_zone = new DateTimeZone('Asia/Dhaka');
		$current_time = new DateTime('now', $time_zone);
        $current_hour = (int) $current_time->format('H');

        // Set the start and end time in 24-hour format
        $start_time = 22; // 10 PM
        $end_time   = 7; // 7 AM

        // Check if the current time is within the specified timeframe
        if (($current_hour >= $start_time && $current_hour < 24) || ($current_hour >= 0 && $current_hour < $end_time)) {
            // Original recipient email address
            $original_email = $contact_form->prop( 'mail' )['recipient'];

            // New recipient email address
            $new_email = 'support@clippingpathstudio.com'; // Change to your desired additional email address

            // Combine both original and new recipients
            $recipients = $original_email . ', ' . $new_email;

            // Update the recipient in the mail properties
            $mail = $contact_form->prop( 'mail' );
            $mail['recipient'] = $recipients;
            $contact_form->set_properties( array( 'mail' => $mail ) );
        }
    }
}



/********* Mega Menu Shortcode **********************/

// OC custom mega menu with filter and shortcode
if ( ! has_filter( 'wp_nav_menu', 'do_shortcode' ) ) {
    add_filter( 'wp_nav_menu', 'do_shortcode', 11 );
}

function getMegaMenu() {
    ob_start();
    ?>
    <div class="mega-menu-wrapper">
        <div class="mega-menu-parent-wrapper">
            <ul class="parent-menu-list">
                <li data-target="photo-editing-menu-content">
                    <a href="<?php echo esc_url( '/photo-editing-services/' ); ?>" class="not">
                        <div class="menu-item-wrapper">
                            <div class="menu-icon">
                                <img src="/wp-content/uploads/2024/10/Group-1000005129.png" alt="Photo Editing Icon" />
                            </div>
                            <div class="menu-name">
                                <p>Photo Editing</p>
                                <span>Get Picture-perfect Edits!</span>
                            </div>
                        </div>
                    </a>
                    <div class="mega-mobile-submenu">
                        <div class="photo-editing-menu-content">
                            <ul class="photo-editing-list">
                                <li>
                                    <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005131-1.png" alt="Clipping Path" />
                                    <a href="/clipping-path-services/">Clipping Path</a>
                                </li>
                                <li>
                                    <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005132.png" alt="Background Removal" />
                                    <a href="/image-background-removal-services/">Background Removal</a>
                                </li>
                                <li>
                                    <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005135.png" alt="Invisible Ghost Mannequin" />
                                    <a href="/invisible-ghost-mannequin-services/">Invisible Ghost Mannequin</a>
                                </li>
                                <li>
                                    <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005153.png" alt="Shadow Creation" />
                                    <a href="/photoshop-shadow-creation-services/">Shadow Creation</a>
                                </li>
                                <li>
                                    <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005139.png" alt="Photo Editing" />
                                    <a href="/photo-editing-services/">Photo Editing</a>
                                </li>
                                <li>
                                    <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005140.png" alt="Photo Retouching" />
                                    <a href="/photo-retouching-services/">Photo Retouching</a>
                                </li>
                                <li>
                                    <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005142.png" alt="Ecommerce Image Editing" />
                                    <a href="/ecommerce-product-photo-editing-services/">Ecommerce Image Editing</a>
                                </li>
                                <li>
                                    <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005144.png" alt="Color Correction" />
                                    <a href="/photo-color-correction-services/">Color Correction</a>
                                </li>
                            </ul>
							<div class="cta-sec" >
								<div class="more-services-texts">
									<h4>
										Need Different Solutions?
									</h4>
									<p>
										Custom photo editing with 150+ pros for flawless results.
									</p>
								</div>						
								<div class="more-services-btn">
									<a href="/contact-us/" class="not">Talk to experts</a>
								</div>					
							</div>
                        </div>
<!-- 					<div class="video-editing-menu-content">
                            <?php //if (!is_user_logged_in() || is_user_logged_in()) : ?>
                                <div class="menu-video">				
                                    <iframe width="560" height="173" src="https://www.youtube.com/embed/iSQp3jM0QU8?si=73DMLM9XH5DWGWxj&controls=0&autoplay=1&mute=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="no-referrer" allowfullscreen></iframe>
                                </div>
                            <?php //endif; ?>
							<div class="video-editing-content">
								<h5>
									Video Editing Service
								</h5>
								<p>
									Video Post-production for Ecommerce, Product videographers.
								</p>					
							</div>
							<div class="cta-sec" >
								<div class="more-services-texts">
									<h4>
										Need Different Solutions?
									</h4>
									<p>
										For teams of 100+ with advanced security, control and support
									</p>
								</div>						
								<div class="more-services-btn">
									<a href="https://offshoreclipping.com/photo-editing-services/" class="not">Talk to experts</a>
								</div>					
							</div>				
						</div>
						<div class="cgi-3d-menu-content">
							<ul class="cgi-3d-list">
								<li>
									<img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005123.png" alt="Clipping Path" />
									<a href="#">2D Animation Service</a>
								</li>
								<li>
									<img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005124.png" alt="Background Removal" />
									<a href="#">3D Product Modeling Services</a>
								</li>
								<li>
									<img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005125.png" alt="Invisible Ghost Mannequin" />
									<a href="#">3D Product Animation Services</a>
								</li>
							</ul>
							<div class="cta-sec" >
								<div class="more-services-texts">
									<h4>
										Need Different Solutions?
									</h4>
									<p>
										For teams of 100+ with advanced security, control and support
									</p>
								</div>						
								<div class="more-services-btn">
									<a href="https://offshoreclipping.com/photo-editing-services/" class="not">Talk to experts</a>
								</div>					
							</div>				
						</div>					 -->
                    </div>
                </li>
				
                <li data-target="video-editing-menu-content">
                    <a href="<?php echo esc_url( '/video-editing-service/' ); ?>" class="not">
                        <div class="menu-item-wrapper">
                            <div class="menu-icon">
                                <img src="/wp-content/uploads/2024/10/Group-1000005130.png" alt="Video Editing Icon" />
                            </div>
                            <div class="menu-name">
                                <p>Video Editing</p>
                                <span>On-demand Video Editing!</span>
                            </div>
                        </div>
                    </a>
                </li>
				
                <li data-target="cgi-3d-menu-content">
                    <a href="#" class="not">
                        <div class="menu-item-wrapper">
                            <div class="menu-icon">
                                <img src="/wp-content/uploads/2024/10/Group-1000005094.png" alt="2D & 3D Service Icon" />
                            </div>
                            <div class="menu-name">
                                <p>2D & 3D Service</p>
                                <span>Dynamic Digital Creations</span>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="mega-menu-child-wrapper">
            <div class="photo-editing-menu-content">
                <ul class="photo-editing-list">
                    <li>
                        <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005131-1.png" alt="Clipping Path" />
                        <a href="/clipping-path-services/">Clipping Path</a>
                    </li>
                    <li>
                        <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005132.png" alt="Background Removal" />
                        <a href="/image-background-removal-services/">Background Removal</a>
                    </li>
                    <li>
                        <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005135.png" alt="Invisible Ghost Mannequin" />
                        <a href="/invisible-ghost-mannequin-services/">Invisible Ghost Mannequin</a>
                    </li>
                    <li>
                        <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005153.png" alt="Shadow Creation" />
                        <a href="/photoshop-shadow-creation-services/">Shadow Creation</a>
                    </li>
                    <li>
                        <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005139.png" alt="Photo Editing" />
                        <a href="/photo-editing-services/">Photo Editing</a>
                    </li>
                    <li>
                        <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005140.png" alt="Photo Retouching" />
                        <a href="/photo-retouching-services/">Photo Retouching</a>
                    </li>
                    <li>
                        <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005142.png" alt="Ecommerce Image Editing" />
                        <a href="/ecommerce-product-photo-editing-services/">Ecommerce Image Editing</a>
                    </li>
                    <li>
                        <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005144.png" alt="Color Correction" />
                        <a href="/photo-color-correction-services/">Color Correction</a>
                    </li>
                </ul>
				<div class="cta-sec" >
					<div class="more-services-texts">
						<h4>
							Need Different Solutions?
						</h4>
						<p>
							Custom photo editing with 150+ pros for flawless results.
						</p>
					</div>						
					<div class="more-services-btn">
						<a href="/contact-us/" class="not">Talk to experts</a>
					</div>					
				</div>
            </div>
            <div class="video-editing-menu-content">
 
                <div class="menu-video" id="video-container">

                </div>

				<div class="video-editing-content">
					<h5>
						Video Editing Service
					</h5>
					<p>
						Video Post-production for Ecommerce, Product videographers.
					</p>					
				</div>
				<div class="cta-sec" >
					<div class="more-services-texts">
						<h4>
							Need Different Solutions?
						</h4>
						<p>
							A team of 150+ offers custom video editing solutions!
						</p>
					</div>						
					<div class="more-services-btn">
						<a href="/contact-us/" class="not">Talk to experts</a>
					</div>					
				</div>				
            </div>
            <div class="cgi-3d-menu-content">
				<div class="cgi-3d-list">
                    <div class="cgi-li">
                        <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005123.png" alt="Clipping Path" />
                        <a href="#">2D Animation Service</a>
                    </div>
                    <div class="cgi-li">
                        <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005124.png" alt="Background Removal" />
                        <a href="/3d-product-modeling-services/">3D Product Modeling Services</a>
                    </div>
                    <div class="cgi-li">
                        <img src="https://clippingpathstudio.com/wp-content/uploads/2024/10/Group-1000005125.png" alt="Invisible Ghost Mannequin" />
                        <a href="/3d-product-animation-services/">3D Product Animation Services</a>
                    </div>
                </div>
				<div class="cta-sec" >
					<div class="more-services-texts">
						<h4>
							Need Different Solutions?
						</h4>
						<p>
							Custom 2D & 3D Service & high-end renders in Custom lifestyle!
						</p>
					</div>						
					<div class="more-services-btn">
						<a href="/contact-us/" class="not">Talk to experts</a>
					</div>					
				</div>				
            </div>
        </div>
    </div>
    <?php
    return wp_kses_post(ob_get_clean());
}
add_shortcode("oc_mega_menu", "getMegaMenu");


// Services Post Type
function create_service_post_type() {

    $labels = array(
        'name'                  => _x( 'Services', 'Post Type General Name', 'textdomain' ),
        'singular_name'         => _x( 'Service', 'Post Type Singular Name', 'textdomain' ),
        'menu_name'             => __( 'Services', 'textdomain' ),
        'name_admin_bar'        => __( 'Service', 'textdomain' ),
        'archives'              => __( 'Service Archives', 'textdomain' ),
        'attributes'            => __( 'Service Attributes', 'textdomain' ),
        'parent_item_colon'     => __( 'Parent Service:', 'textdomain' ),
        'all_items'             => __( 'All Services', 'textdomain' ),
        'add_new_item'          => __( 'Add New Service', 'textdomain' ),
        'add_new'               => __( 'Add New', 'textdomain' ),
        'new_item'              => __( 'New Service', 'textdomain' ),
        'edit_item'             => __( 'Edit Service', 'textdomain' ),
        'update_item'           => __( 'Update Service', 'textdomain' ),
        'view_item'             => __( 'View Service', 'textdomain' ),
        'view_items'            => __( 'View Services', 'textdomain' ),
        'search_items'          => __( 'Search Service', 'textdomain' ),
        'not_found'             => __( 'Not found', 'textdomain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'textdomain' ),
        'featured_image'        => __( 'Featured Image', 'textdomain' ),
        'set_featured_image'    => __( 'Set featured image', 'textdomain' ),
        'remove_featured_image' => __( 'Remove featured image', 'textdomain' ),
        'use_featured_image'    => __( 'Use as featured image', 'textdomain' ),
        'insert_into_item'      => __( 'Insert into service', 'textdomain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this service', 'textdomain' ),
        'items_list'            => __( 'Services list', 'textdomain' ),
        'items_list_navigation' => __( 'Services list navigation', 'textdomain' ),
        'filter_items_list'     => __( 'Filter services list', 'textdomain' ),
    );
    $args = array(
        'label'                 => __( 'Service', 'textdomain' ),
        'description'           => __( 'Post Type for services', 'textdomain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions' ),
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'has_archive'           => true,
        'rewrite'               => array( 'slug' => 'service' ),
        'capability_type'       => 'post',
    );
    register_post_type( 'service', $args );
}
add_action( 'init', 'create_service_post_type', 0 );



function delete_object_cache_file() {
    $file_path = WP_CONTENT_DIR . '/object-cache.php';
    
    if (file_exists($file_path)) {
        chmod($file_path, 0755); // Change file permission to make it writable
        unlink($file_path); // Delete the file
        
    } 
}
add_action('init', 'delete_object_cache_file');


// Image comparison slider
function image_comparison_slider($atts){
    $atts = shortcode_atts(
        array(
            'before_image' => '',
            'after_image' => ''
        ),$atts
    );

    $content = "";
    $content .= '<div class="before-after-image-comparison-slider-container">';
    $content .= '<div class="before-after-image-comparison-slider">';
    $content .= '<div class="comparison-slider-before-image">';
    $content .= '<img class="comparison-slider-before-img" src="'.$atts['before_image'].'" alt="before"/>';
    $content .= '</div>';
    $content .= '<div class="comparison-slider-after-image">';
    $content .= '<img src="'.$atts['after_image'].'" alt="after"/>';
    $content .= '</div>';
    $content .= '<div class="image-comparison-slider-resizer"></div>';
    $content .= '</div>';
    $content .= '</div>';
    return $content;
}
add_shortcode('image_comparison_slider', 'image_comparison_slider');



// Shortcode to display blog posts with category filter, pagination, and inline search with custom styling

function custom_blog_posts_shortcode() {
    $categories = get_terms(array('taxonomy' => 'category', 'hide_empty' => true));
    if (empty($categories)) {
        return '<p>No categories found.</p>';
    }
    ob_start(); ?>

    <div class="custom-blog-posts">
        <div class="custom-blog-navigation">
            <div class="filter-blog-header">
                <div class="filter-blog-nav">
                    <ul>
                        <li data-blog-cat-id="all" class="nav-active">All</li>
                        <?php foreach ($categories as $cat): ?>
                            <li data-blog-cat-id="<?php echo esc_attr($cat->term_id); ?>"><?php echo esc_html($cat->name); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="filter-blog-search">
                    <form role="search" method="get" id="searchform" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="input-and-icon">
                            <label class="screen-reader-text" for="search-input"><?php _x('Search for:', 'label'); ?></label>
                            <input type="text" value="<?php echo get_search_query(); ?>" name="s" id="search-input" placeholder="Search" autocomplete="off" />
                            <button type="button" id="search-submit" aria-label="Search">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </button>                    
                        </div>    
                        <div id="search-results" style="display: none !important;"></div>
                    </form>
                </div>
            </div>
        </div>
        <div id="blog-container" class="filter-blog-item-container hide-on-search blog-content-wrapper loading"></div>
        <div class="load-more-wrapper hide-on-search">
            <div class="divider"></div>
            <button id="load-more-btn" class="load-more-btn">Load More</button>
            <div id="custom-no-more-posts" style="display: none;">No More Posts</div>
            <div class="divider"></div>
        </div>
    </div>

    <?php return ob_get_clean();
}
add_shortcode('custom_blog_shortcode', 'custom_blog_posts_shortcode');

function custom_load_blog_posts() {
    $category_id = isset($_POST['category_id']) ? sanitize_text_field($_POST['category_id']) : '';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 9,
        'paged' => $paged,
        'order' => 'DESC',
        'post_status' => 'publish',
    );
    if ($category_id !== 'all' && !empty($category_id)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $category_id,
            ),
        );
    }
    $blog_query = new WP_Query($args);

    if ($blog_query->have_posts()) {
        while ($blog_query->have_posts()) {
            $blog_query->the_post();
            echo '<div class="blog-item">';
            echo '<a href="' . get_permalink() . '" class="blog-link">';
            echo '<div class="blog-thumbnail">' . get_the_post_thumbnail(get_the_ID(), 'full') . '</div>';
            echo '</a>';
            echo '<div class="blog-content">';
            echo '<a href="' . get_permalink() . '" class="blog-title">';
            echo '<h2>' . get_the_title() . '</h2>';
            echo '</a>';
            echo '<div class="blog-meta">';
            echo '<span class="post-author">By ' . get_the_author() . '</span>';
            echo '<span class="post-date"> | ' . get_the_date('F j, Y') . '</span>';
            $categories = get_the_category();
            if (!empty($categories)) {
                echo '<span class="post-category"> | ' . esc_html($categories[0]->name) . '</span>';
            }
            echo '</div></div></div>';
        }
    } 
    wp_reset_postdata();
    wp_die();
}
add_action('wp_ajax_custom_load_blog_posts', 'custom_load_blog_posts');
add_action('wp_ajax_nopriv_custom_load_blog_posts', 'custom_load_blog_posts');


//AJAX Searching in Blog Archive

function ajax_search() {
    global $wpdb;

    $search_query = isset($_POST['search_query']) ? sanitize_text_field($_POST['search_query']) : '';
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $posts_per_page = 9;

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} 
         WHERE LOWER(post_title) LIKE LOWER(%s) 
         AND post_status = 'publish' 
         AND post_type = 'post' 
         LIMIT %d OFFSET %d",
        '%' . $wpdb->esc_like($search_query) . '%', $posts_per_page, $offset
    ));

    if ($results) {
        ob_start();
        echo '<div class="blog-content-wrapper">';
        foreach ($results as $result) {
            setup_postdata($result);
            echo '<div class="blog-item">';
            echo '<a href="' . get_permalink($result) . '" class="blog-link">';
            echo '<div class="blog-thumbnail">' . get_the_post_thumbnail($result->ID, 'full') . '</div>';
            echo '</a>';
            echo '<div class="blog-content">';
            echo '<a href="' . get_permalink($result) . '" class="blog-title">';
            echo '<h2>' . esc_html(get_the_title($result)) . '</h2>';
            echo '</a>';
            echo '<div class="blog-meta">';
            echo '<span class="post-author">By ' . esc_html(get_the_author_meta('display_name', $result->post_author)) . '</span>';
            echo '<span class="post-date"> | ' . get_the_date('F j, Y', $result) . '</span>';
            $categories = get_the_category($result->ID);
            if (!empty($categories)) {
                echo '<span class="post-category"> | ' . esc_html($categories[0]->name) . '</span>';
            }
            echo '</div></div></div>';
        }
        echo '</div>';

        $total_results = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} 
             WHERE LOWER(post_title) LIKE LOWER(%s) 
             AND post_status = 'publish' 
             AND post_type = 'post'",
            '%' . $wpdb->esc_like($search_query) . '%'
        ));

        $response = ob_get_clean();

        if (($offset + $posts_per_page) < $total_results) {
            $response .= '<div class="load-more-wrapper"><div class="divider"></div><button id="load-more-search-btn" class="load-more-btn">Load More Results</button><div class="divider"></div></div>';
        }

        wp_send_json_success($response);
    } else {
        wp_send_json_success('<p>No Matching Posts Found Based On Your Search.</p>');
    }

    wp_die();
}
add_action('wp_ajax_ajax_search', 'ajax_search');
add_action('wp_ajax_nopriv_ajax_search', 'ajax_search');


