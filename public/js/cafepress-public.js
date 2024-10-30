(function( $ ) {
	'use strict';

	
	$(function() {
		$("#cafepress-menu-container .cafepress-menu-tab .tablinks").click(function(e) {
			openTab(e, $(this).data("cat-slug"));
        });

		function openTab(evt, cat_slug) {
			var i, tabcontent, tablinks;
			tabcontent = document.getElementsByClassName("cafepress-menu-tabcontent");
			for (i = 0; i < tabcontent.length; i++) {
				tabcontent[i].style.display = "none";
			}
			tablinks = document.getElementsByClassName("tablinks");
			for (i = 0; i < tablinks.length; i++) {
				tablinks[i].className = tablinks[i].className.replace(" active", "");
			}
			document.getElementById(cat_slug).style.display = "block";
			evt.currentTarget.className += " active";
		}

		var $cartContainer = $('#cafepress-bmc-container .cafepress-bmc-content');

		$("#cafepress-menu-container .cafepress-menu-add-cart-button").click(function (e) {
			e.preventDefault();
	
			var $thisbutton = $(this),
				product_qty = 1,
				product_id = $(this).data('product-id');
	
			var data = {
				action: 'cafepress_add_to_cart',
				product_id: product_id,
				quantity: product_qty,
				security: $('#cafepress_menu_ajax_nonce').val(),
				cuisine_table_id: $('#cafepress-cuisine_table_id').val()
			};
	
			$(document.body).trigger('adding_to_cart', [$thisbutton, data]);
	
			$.ajax({
				type: 'POST',
				url: cafepress_js_data.ajax_url,
				dataType: 'json',
				data: data,
				success: function (response) {
					if (response.error) {
						alert(response.error);
						return;
					}

					if(response.new_item) {
						var is_new_product = true;
						$(".cafepress-bmc-cci-cart-item").each(function() {
							if ($(this).data('product-id') == response.new_item.product.id) {
                                is_new_product = false;
								return false;
                            }
						});

						
						if (is_new_product) {
							$('#cafepress-bmc-cec').hide();
							$('#cafepress-bmc-cci-has-items').show();
							$('#cafepress-bmc-bp').show();
							
                            var appendItem = prepareNewItem(response.new_item);

                        	$('#cafepress-bmc-cci-has-items').append(appendItem);

							var new_cart_item = $('#cafepress-bmc-cci-cart-item_' + response.new_item.product.id);

							new_cart_item.find('.cafepress-bmc-ccil-quantity-down').click(function(e) {
								e.preventDefault();
								doCartItemQtyDown($(this));
							});

							new_cart_item.find('.cafepress-bmc-ccil-quantity-up').click(function(e) {
								e.preventDefault();
								doCartItemQtyUp($(this));
							});

							new_cart_item.find('.cafepress-bmc-cci-remove-item').click(function(e) {
								e.preventDefault();
								doRemoveCartItem($(this));
							});

							new_cart_item.find('.cafepress-bmc-ccil-quantity__input').change(function(e) {
								e.preventDefault();
								doCartItemQtyChange($(this));
							});
					
                        } else {
							var $item_qty = $('#cafepress-bmc-cci-cart-item_' + response.new_item.product.id + ' .cafepress-bmc-ccil-quantity__input');
							var $update_qty =parseInt($item_qty.val()) + 1;
							$item_qty.val($update_qty);
						}
					}

					$("#cafepress-bmc-container .cafepress-bmc-bp .cafepress-bmc-bp-subtotal-amount").html(response.total);
					$("#cafepress-bmc-container #cafepress-items-count").html(response.total_qty);

					var $defaultIcon = $('#cafepress-bmc-container .cafepress-bmc-cart-button .launcher-default-open-icon');
					if ($defaultIcon.hasClass('open')) {
						$('#cafepress-bmc-container .cafepress-bmc-cart-button').trigger('click');
					}
				},
				error: function(error) {
					console.log(error);
				}
			});
	
			return false;
		});

		function prepareNewItem(new_item) {
			var newItem = $('<div id="cafepress-bmc-cci-cart-item_' + new_item.product.id + '" class="cafepress-bmc-cci-cart-item"  data-product-id="' + new_item.product.id + '" data-key="' + new_item.key + '"></div>');
            newItem.append('<a href="' + new_item.product.name + '" class="cafepress-bmc-cci-cart-item-image">' + new_item.product.image + '</a>');
			
			var item_desc = $('<div class="cafepress-bmc-cci-cart-item-desc"></div>');
			
			var item_field = $('<div class="cafepress-bmc-cci-cart-item-data-field"></div>');
			item_field.append('<a href="' + new_item.product.name + '">' + new_item.product.name + '</a>');

			var line_item = $('<div class="cafepress-bmc-cci-line-item"></div>');

			var line_item_qty = $('<div class="cafepress-bmc-ccil-quantity-selector"></div>');
			
			var qty_minus = $('<div class="cafepress-bmc-ccil-quantity-button cafepress-bmc-ccil-quantity-down" data-action="down" data-key="' + new_item.key + '"></div>');
			qty_minus.append('<svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 20 20">' +
								'<path fill="currentColor" d="M17.543 11.029H2.1A1.032 1.032 0 0 1 1.071 10c0-.566.463-1.029 1.029-1.029h15.443c.566 0 1.029.463 1.029 1.029 0 .566-.463 1.029-1.029 1.029z"></path>' +
                            '</svg>');
			line_item_qty.append(qty_minus);

			line_item_qty.append('<input class="cafepress-bmc-ccil-quantity__input" name="cafepress-bmc-ccil-quantity__input" type="text" aria-label="Quantity" inputmode="numeric" step="1" min="0" max="" data-key="' + new_item.key + '" pattern="[0-9]*" value="' + new_item.qty + '">');

			var qty_plus = $('<div class="cafepress-bmc-ccil-quantity-button cafepress-bmc-ccil-quantity-up" data-action="up" data-key="' + new_item.key + '"></div>');
			qty_plus.append('<svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 20 20">' +
                                '<path fill="currentColor" d="M17.409 8.929h-6.695V2.258c0-.566-.506-1.029-1.071-1.029s-1.071.463-1.071 1.029v6.671H1.967C1.401 8.929.938 9.435.938 10s.463 1.071 1.029 1.071h6.605V17.7c0 .566.506 1.029 1.071 1.029s1.071-.463 1.071-1.029v-6.629h6.695c.566 0 1.029-.506 1.029-1.071s-.463-1.071-1.029-1.071z"></path>' +
                            '</svg>');
			line_item_qty.append(qty_plus);
			
			line_item.append(line_item_qty);

			var line_item_remove = $('<div class="cafepress-bmc-cci-remove-item" data-product-id="' + new_item.product.id +'" data-key="' + new_item.key + '">');
			line_item_remove.append('<svg width="10" height="10" viewBox="0 0 24 24" class="cafepress-bmc-cci-icon-close" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                                        '<path d="M4.1518 4.31359L4.22676 4.22676C4.50161 3.9519 4.93172 3.92691 5.2348 4.1518L5.32163 4.22676L12 10.9048L18.6784 4.22676C18.9807 3.92441 19.4709 3.92441 19.7732 4.22676C20.0756 4.5291 20.0756 5.01929 19.7732 5.32163L13.0952 12L19.7732 18.6784C20.0481 18.9532 20.0731 19.3833 19.8482 19.6864L19.7732 19.7732C19.4984 20.0481 19.0683 20.0731 18.7652 19.8482L18.6784 19.7732L12 13.0952L5.32163 19.7732C5.01929 20.0756 4.5291 20.0756 4.22676 19.7732C3.92441 19.4709 3.92441 18.9807 4.22676 18.6784L10.9048 12L4.22676 5.32163C3.9519 5.04678 3.92691 4.61667 4.1518 4.31359L4.22676 4.22676L4.1518 4.31359Z" fill="currentColor"></path>' +
                                    '</svg>');   
			line_item.append(line_item_remove);

			item_field.append(line_item);

			item_desc.append(item_field);

			var item_misc = $('<div class="cafepress-bmc-cci-cart-item-misc"></div>');
			item_misc.append('<div class="cafepress-bmc-cci-cart-price">' + new_item.price + '</div>');

			newItem.append(item_desc);
			newItem.append(item_misc);

            return newItem;
		}

        $('#cafepress-bmc-container .cafepress-bmc-cart-button').click(function() {
            $cartContainer.fadeToggle('slow', function() {
                // Animation complete.
            });

			var $defaultIcon = $('#cafepress-bmc-container .cafepress-bmc-cart-button .launcher-default-open-icon');
			var $minimizeIcon = $('#cafepress-bmc-container .cafepress-bmc-cart-button .launcher-minimize-icon');

			if ($defaultIcon.hasClass('open')) {
				$defaultIcon.removeClass('open');
				$defaultIcon.animate({ opacity: 0 }, 300);
            	$minimizeIcon.animate({ opacity: 1 }, 300);
				$minimizeIcon.addClass('open');
			} else {
				$defaultIcon.addClass('open');
				$defaultIcon.animate({ opacity: 1 }, 300);
            	$minimizeIcon.animate({ opacity: 0 }, 300);
				$minimizeIcon.removeClass('open');
			}
        });

		 $('#cafepress-bmc-container .cafepress-bmc-header-modal-close').click(function() {
			$('#cafepress-bmc-container .cafepress-bmc-cart-button').trigger('click');
		 });

		$('#cafepress-bmc-container .cafepress-bmc-cci-remove-item').click(function(e) {
			e.preventDefault();
			doRemoveCartItem($(this));
		});

		$('#cafepress-bmc-container .cafepress-bmc-ccil-quantity-up').click(function(e) {
			e.preventDefault();
			doCartItemQtyUp($(this));
		});
		
		$('#cafepress-bmc-container .cafepress-bmc-ccil-quantity-down').click(function(e) {
			e.preventDefault();
			doCartItemQtyDown($(this));
		});

		$('#cafepress-bmc-container .cafepress-bmc-ccil-quantity__input').change(function(e) {
			e.preventDefault();
			doCartItemQtyChange($(this));
		});

		function doRemoveCartItem(button) {
			var product_id = button.data("product-id");
			var cart_item_key = button.data("key");
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: cafepress_js_data.ajax_url,
				data: {
					action: "remove_item_from_cart",
					product_id: product_id,
					security: $('#cafepress_minicart_ajax_nonce').val(),
					cart_item_key: cart_item_key
				},
				success: function (response) {
					if (!response || response.error) {
						alert('Failed to remove item from cart.');
						return;
					}
					
					$("#cafepress-bmc-container .cafepress-bmc-bp .cafepress-bmc-bp-subtotal-amount").html(response.total);
					$("#cafepress-bmc-container #cafepress-items-count").html(response.total_qty);
					
					var cartItem = $('#cafepress-bmc-cci-cart-item_' + product_id);
					cartItem.fadeOut('slow', function() {
						cartItem.remove();
						
					});

					if ( !response.total_qty ) {
						$('#cafepress-bmc-cci-has-items').hide();
						$('#cafepress-bmc-bp').hide();
						$('#cafepress-bmc-cec').show();
					}
				},
				error: function() {
					alert('An error occurred while trying to remove the item from cart.');
				}
			});

		}

		function doCartItemQtyDown(button) {
			var cart_item_key = button.data("key");

			let t = parseInt(button.next().val());
			let quality = t - 1;
			if (quality > 0) {
				button.next().attr('value', quality);
				button.next().val(quality)
				updateItem(quality, cart_item_key);
			}
		}

		function doCartItemQtyUp(button) {
			var cart_item_key = button.data("key");

			let t = parseInt(button.prev().val());
			let quality = t + 1;
			button.prev().attr('value', quality);
			button.prev().val(quality)
			updateItem(quality, cart_item_key);
		}

		function doCartItemQtyChange(input) {
			const cart_item_key = input.data("key");
			let quality = input.val();

			if (!$.isNumeric(quality)) {
				alert('Vui lòng nhập số!')
			} else if (quality == 0) {
				alert('Có phải bạn đang muốn xoá sản phẩm đã chọn?')
			} else if (quality < 1) {
				alert('Vui lòng nhập số lớn hơn 0')
			} else {
				input.val(quality)
				input.attr('value', quality);
				updateItem(quality, cart_item_key);
			}
		}

		function updateItem(quality, cart_item_key) {
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: cafepress_js_data.ajax_url,
				data: {
					action: "update_item_in_cart",
					quality: quality,
					security: $('#cafepress_minicart_ajax_nonce').val(),
					cart_item_key: cart_item_key
				},
				success: function (response) {
					if (!response || response.error)
						return;

					$("#cafepress-bmc-container .cafepress-bmc-bp .cafepress-bmc-bp-subtotal-amount").html(response.total);
					$("#cafepress-bmc-container #cafepress-items-count").html(response.total_qty);
		 
				}
			});
		};
	});
	
	$( window ).load(function() {
		$(window).resize(function() {
			var menuWidth = $('#cafepress-menu-container').width();

			var inner_width = '50%';
			if (menuWidth < 800) inner_width = '100%';

			$('#cafepress-menu-container .menu-items-inner-container').css('width', inner_width);

			$('#cafepress-menu-container .cafepress-menu-tabcontent:first').addClass('first');
		}).resize(); // Trigger the resize event initially
	});

})( jQuery );
