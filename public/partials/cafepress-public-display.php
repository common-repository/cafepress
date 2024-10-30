<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="cafepress-bmc-container">
    <div class="cafepress-bmc-content">
        <div class="cafepress-bmc-header">
            <div class="cafepress-bmc-header-heading">
                <div class="cafepress-bmc-header-title">Review Your Cart</div>
                <div class="cafepress-bmc-header-modal-close">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.1518 4.31359L4.22676 4.22676C4.50161 3.9519 4.93172 3.92691 5.2348 4.1518L5.32163 4.22676L12 10.9048L18.6784 4.22676C18.9807 3.92441 19.4709 3.92441 19.7732 4.22676C20.0756 4.5291 20.0756 5.01929 19.7732 5.32163L13.0952 12L19.7732 18.6784C20.0481 18.9532 20.0731 19.3833 19.8482 19.6864L19.7732 19.7732C19.4984 20.0481 19.0683 20.0731 18.7652 19.8482L18.6784 19.7732L12 13.0952L5.32163 19.7732C5.01929 20.0756 4.5291 20.0756 4.22676 19.7732C3.92441 19.4709 3.92441 18.9807 4.22676 18.6784L10.9048 12L4.22676 5.32163C3.9519 5.04678 3.92691 4.61667 4.1518 4.31359L4.22676 4.22676L4.1518 4.31359Z" fill="currentColor"></path>
                    </svg>    
                </div>
            </div>        
        </div>
        <div class="cafepress-bmc-cci-wrap">
            <div id="cafepress-bmc-cci-has-items" class="cafepress-bmc-cci-has-items" <?php echo WC()->cart->is_empty() ? 'style="display:none"' : '' ;?>>
                <input type="hidden" id="cafepress_minicart_ajax_nonce" name="cafepress_minicart_ajax_nonce" value="<?php echo esc_attr(wp_create_nonce( "cafepress_update_mini_cart" )); ?>">
                <?php if (!WC()->cart->is_empty()) { 
                
                    $items = WC()->cart->get_cart_contents();
                    
                    foreach ($items as $itemKey => $itemVal) {
                        ?>
                        <div id="cafepress-bmc-cci-cart-item_<?php echo esc_attr($itemVal['product_id']); ?>" class="cafepress-bmc-cci-cart-item" data-product-id="<?php echo esc_attr($itemVal['product_id']); ?>" data-key="<?php echo esc_attr($itemVal['key']); ?>">
                            <?php
                            $product = wc_get_product($itemVal['data']->get_id());
                            $product_id = apply_filters('woocommerce_cart_item_product_id', $itemVal['product_id'], $itemVal, $itemKey);
                            ?>
                            <a href="<?php echo esc_url($product->get_name()); ?>" class="cafepress-bmc-cci-cart-item-image">
                                <?php echo wp_kses($product->get_image('thumbnail'), self::$allowed_image_html); ?>
                            </a>

                            <div class="cafepress-bmc-cci-cart-item-desc">
                                <div class="cafepress-bmc-cci-cart-item-data-field">
                                    <a href="<?php echo esc_url($product->get_name()); ?>">
                                        <?php echo esc_html($product->get_name()); ?>
                                    </a>
                                    <div class="cafepress-bmc-cci-line-item">
                                        <div class="cafepress-bmc-ccil-quantity-selector">                
                                            <div class="cafepress-bmc-ccil-quantity-button cafepress-bmc-ccil-quantity-down" data-action="down" data-key="<?php echo esc_attr($itemVal['key']); ?>">
                                                <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 20 20">
                                                    <path fill="currentColor" d="M17.543 11.029H2.1A1.032 1.032 0 0 1 1.071 10c0-.566.463-1.029 1.029-1.029h15.443c.566 0 1.029.463 1.029 1.029 0 .566-.463 1.029-1.029 1.029z"></path>
                                                </svg>                
                                            </div>
                                            <input class="cafepress-bmc-ccil-quantity__input" name="cafepress-bmc-ccil-quantity__input" type="text" aria-label="Quantity" inputmode="numeric" step="1" min="0" max="" data-key="<?php echo esc_attr($itemVal['key']); ?>" pattern="[0-9]*" value="<?php echo esc_attr($itemVal['quantity']); ?>">
                                            <div class="cafepress-bmc-ccil-quantity-button cafepress-bmc-ccil-quantity-up" data-action="up" data-key="<?php echo esc_attr($itemVal['key']); ?>">
                                                <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 20 20">
                                                    <path fill="currentColor" d="M17.409 8.929h-6.695V2.258c0-.566-.506-1.029-1.071-1.029s-1.071.463-1.071 1.029v6.671H1.967C1.401 8.929.938 9.435.938 10s.463 1.071 1.029 1.071h6.605V17.7c0 .566.506 1.029 1.071 1.029s1.071-.463 1.071-1.029v-6.629h6.695c.566 0 1.029-.506 1.029-1.071s-.463-1.071-1.029-1.071z"></path>
                                                </svg> 
                                            </div>
                                        </div>                
                                        <div class="cafepress-bmc-cci-remove-item" data-product-id="<?php echo esc_attr($itemVal['product_id']); ?>" data-key="<?php echo esc_attr($itemVal['key']); ?>">
                                            <svg width="10" height="10" viewBox="0 0 24 24" class="cafepress-bmc-cci-icon-close" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.1518 4.31359L4.22676 4.22676C4.50161 3.9519 4.93172 3.92691 5.2348 4.1518L5.32163 4.22676L12 10.9048L18.6784 4.22676C18.9807 3.92441 19.4709 3.92441 19.7732 4.22676C20.0756 4.5291 20.0756 5.01929 19.7732 5.32163L13.0952 12L19.7732 18.6784C20.0481 18.9532 20.0731 19.3833 19.8482 19.6864L19.7732 19.7732C19.4984 20.0481 19.0683 20.0731 18.7652 19.8482L18.6784 19.7732L12 13.0952L5.32163 19.7732C5.01929 20.0756 4.5291 20.0756 4.22676 19.7732C3.92441 19.4709 3.92441 18.9807 4.22676 18.6784L10.9048 12L4.22676 5.32163C3.9519 5.04678 3.92691 4.61667 4.1518 4.31359L4.22676 4.22676L4.1518 4.31359Z" fill="currentColor"></path>
                                            </svg>                
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cafepress-bmc-cci-cart-item-misc">
                                <div class="cafepress-bmc-cci-cart-price">
                                    <?php echo wp_kses(WC()->cart->get_product_price($product), self::$allowed_price_html); ?>    
                                </div>
                            </div>
                        </div>
                        <?php
                    } // product foreach loop ends
                    ?>
                <?php } ?>
            </div>
            <div id="cafepress-bmc-cec" class="cafepress-bmc-cec" style="display: <?php echo WC()->cart->is_empty() ? 'block' : 'none' ;?>">
                <div class="cafepress-bmc-cec-zero-state">
                    <div class="cafepress-bmc-cec-zero-state-title">Cart is empty.</div>
                    <div class="cafepress-bmc-cec-zero-state-text">Fill your cart with amazing items</div>
                    <a href="<?php echo esc_url(wc_get_page_permalink( 'shop' )) ?>" class="cafepress-bmc-cec-empty-button">Shop Now</a>
                </div>
            </div>
        </div>
        <div id="cafepress-bmc-bp" class="cafepress-bmc-bp" <?php echo WC()->cart->is_empty() ? 'style="display:none"' : '' ;?>>
            <!-- Summary -->
            <div class="cafepress-bmc-bp-buy-summary">
                <div class="cafepress-bmc-bp-cart-summary-row">
                    <?php
                    $get_totals = WC()->cart->get_totals();
                    $cart_total = $get_totals['subtotal'];
                    $cart_discount = $get_totals['discount_total'];
                    $final_subtotal = $cart_total - $cart_discount;
                    ?>
                    <div class='cafepress-bmc-bp-total-label'>Sub total</div>

                    <div class="cafepress-bmc-bp-subtotal-amount">
                        <?php echo wp_kses(WC()->cart->get_cart_subtotal(), self::$allowed_price_html); ?>
                    </div>
                </div>
            </div>
        
            <div class="cafepress-bmc-bp_checkout-button">
                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>">Checkout</a>
            </div>  
        </div>
    </div>
    <div class="cafepress-bmc-cart-button">
        <div class="items-count" id="cafepress-items-count"><?php echo esc_html(WC()->cart->cart_contents_count); ?></div>
        <div class="launcher-icon launcher-default-open-icon open">
            <span></span>
        </div>
        <div class="launcher-icon launcher-minimize-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                <path d="M8 12L16 20L24 12" stroke="white" stroke-width="2.67" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </div>
    </div>
</div>