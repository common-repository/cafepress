<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="cafepress-menu-container">
    <div class="cafepress-menu-tab">
 <?php
    $tab_index = 0;
    foreach ( $categories as $category ) {
?>
        <button class="tablinks <?php echo (!$tab_index) ? "active": "";?>" data-cat-slug="<?php echo esc_attr($category->slug); ?>"><?php echo esc_html($category->name); ?></button>
<?php
        $tab_index++;
    }
?>
    </div>
<?php
    wp_nonce_field('cafepress_add_to_cart', 'cafepress_menu_ajax_nonce'); 
    foreach ( $categories as $category ) {
?>
    <div id="<?php echo esc_attr($category->slug) ; ?>" class="cafepress-menu-tabcontent">
<?php
        $args = array(
            'post_type' => 'product',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $category->slug,
                ),
            ),
        );

        $products = new WP_Query( $args );

        if ( $products->have_posts() ) {
?>
        <section class="cafepress-menu-section">
            <input type="hidden" id="cafepress-cuisine_table_id" name="cuisine_table_id" value="<?php echo esc_attr($cuisine_table_id); ?>" />
            
<?php
            while ( $products->have_posts() ) {
                $products->the_post();
                $_product = new WC_Product( get_the_ID());
                $_product_image = '';
                if ( $_product->get_image_id() ) {
                    $_product_image = wp_get_attachment_image_url( $_product->get_image_id() );
                }

                if ( ! $_product_image ) {
                    $_product_image = wc_placeholder_img_src();
                }
?>
                <div class="menu-items-inner-container">
                    <div class="menu-items-img-holder">
                        <img decoding="async" src="<?php echo esc_url($_product_image); ?>" alt="menu-items-image" />
                    </div>
                    <div class="menu-items-content">
                        <div class="menu-items-upper-content">
                            <div class="menu-items-title">
                                <?php echo esc_attr(get_the_title()); ?>
                            </div>
                            <div class="menu-items-price">
                                <?php echo wp_kses($_product->get_price_html(), self::$allowed_price_html); ?>
                            </div>
                        </div>
                        <div class="menu-items-bottom-content">
                            <div class="menu-items-description">
                                <?php echo esc_attr(get_the_excerpt()); ?>
                            </div>
                        </div>

                        <button type="submit" class="cafepress-menu-add-cart-button" data-product-id="<?php echo esc_attr( $_product->get_id() ); ?>">Add to Cart</button>
                    </div>
                </div>
<?php
            }
?>
        </section>

<?php            
        } else {
?>
            <p>No products found in this category.</p>
<?php 		
        }
?>
    </div>
<?php
    }
?>

</div>
