/*

Mobile category

 */

$('.mobile-banner-hover').on('click', function(){
	window.location.href = $(this).attr('rel');
});

/*

Category link

 */

$('.category-active-parent').on('click', function(e){
	var href = $(this).attr('href');

	window.location.href = href;
});


/*

Phone mask

*/

let count_error = 0;

window.addEventListener('input', function (e) {

	$('div.error-phone').hide()

	if (e.target.classList.contains('standard-phone')) {

		if (e.target.value[0] !== '+' && e.target.value.length == 1) {

			e.target.value = '+380'

		}

		else if (e.target.value.length > 16) {

			if (e.target.value.length == 17) {

				e.target.value = e.target.value.slice(0, -1)

			}

			else {

				e.target.value = '+380'

			}

		}

		else if (e.target.value.length < 5) e.target.value = '+380'

		else {

			var regexp = /^\+380[0-9]+$/i;

			if (!regexp.test(e.target.value) && count_error == 0) {

				for (var i = e.target.value.length - 1; i >= 0; i--) {

					if (!regexp.test(e.target.value)) {

						e.target.value = e.target.value.slice(0, -1)

					}

					else break;

				}

				count_error = 1;

				$('div.error-phone').show()

			} else if (!regexp.test(e.target.value) && count_error == 1 && e.target.value.length > 1) {

				for (var i = e.target.value.length - 1; i >= 0; i--) {

					if (!regexp.test(e.target.value)) {

						e.target.value = e.target.value.slice(0, -1)

					}

					else break;

				}

				$('div.error-phone').show()

			} else if (e.target.value.length < 2 || regexp.test(e.target.value)) {

				count_error = 0;

			}

		}

	}

}, false);





/*

Buy One Click

*/

$('#fast-order').submit(function (e) {

	e.preventDefault();

	if (!document.getElementById('checken-yes').checked) {

		$('div.error-checkbox').show()

		return 0

	}

	$('div.error-phone').hide()

	$('div.error-checkbox').hide()

	$.ajax({

		url: '/index.php?route=product/product/fastBuyPhone',

		type: 'POST',

		data: $(this).serialize(),

		dataType: 'json',

		success: function (json) {

			if (json.error) {

				console.log(json)

			}

			if (json['success']) {

				document.getElementById('Modal-buy-one-click').hidden = true;

				$('button[data-target="#Modal-order-done"]').click();

			}

		}

	});

});





/*

Modal

*/

$('.btn-buy-one-click, .wrapper-btn .btn-2').click((e) => {

	$('#fast-order [name="product_id"]').val($(e.target).attr('data-product-id'))

	$('button[data-target="#Modal-buy-one-click"]').click()

})



$('.continue-buy').click((e) => {

	document.getElementById('Modal-order-done').hidden = true;

	$('.modal-backdrop.fade').removeClass('show');

})



$('.item-block-function.basket a').click((e) => {

	e.preventDefault();

	var main_count = $('#cart_quantity').text();

	if (parseInt(main_count) == 0) {

		$('button[data-target="#Modal-empty-basket"]').click();

	} else {

		$('button[data-target="#Modal-mini-cart"]').click();

	}

})



$(document).ready(function () {

	upload_minicart();



});



function upload_minicart() {

	$.ajax({

		url: '/index.php?route=checkout/cart/minicart',

		success: function (response) {



			rewrite_html_minicart(response);

		},

		error: function (xhr, ajaxOptions, thrownError) {

			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

		}

	});

}

function upload_product_in_cart() {
	$.ajax({
		url: '/index.php?route=checkout/cart/minicart',
		success: function (response) {
			var add_cart_div = $("#Modal-product-in-cart");
			var add_to_cart = add_cart_div.find(".block-wrapper-card").eq(0);
			add_to_cart.html(response);
		},

		error: function (xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function rewrite_html_minicart(response) {

	var minicart_div = $("#Modal-mini-cart");

	var minicart = minicart_div.find(".block-wrapper-card").eq(0);

	minicart.html(response);

}



function mini_remove(cart_id, quantity) {

	cart.remove(cart_id);

	// If Cart exists products 1 or more
	if (parseInt($('#cart_quantity').text()) >= 1) {
		$('#cart_quantity').text(parseInt($('#cart_quantity').text()) - quantity);
		setTimeout(upload_minicart, 5);
		setTimeout(upload_product_in_cart, 5);
	}

	if (parseInt($('#cart_quantity').text()) <= 0) {
		if ($('#Modal-product-in-cart').hasClass('show')) {
			//$('#Modal-product-in-cart').modal('hide');
			// ========>>>
			$('#Modal-product-in-cart').on('hidden.bs.modal', function (e) {
				$('body').removeClass('modal-open');
				$('.modal-backdrop').remove();
			})
		}
	}

}



$('.plus').on('click', function (e) {

	var elem = $(this);

	var div_add = elem.closest(".add-product");

	var id_cart = div_add.attr('data-key');

	var product_id = $(this).attr('data-prdctid');

	var unit_price = $(this).attr('data-unit_price');

	var quantity = $('#quantity-' + product_id).val();

	quantity = parseInt(quantity, 10) + 1;

	var main_count = $('#cart_quantity').text();

	$('#cart_quantity').text(parseInt(main_count) + 1);

	one_update(id_cart, quantity);

	var special_price = $(this).attr('data-special');

	var main_total = $('#main-total').text();

	var current_txt = main_total.replace(/[0-9]|\,+/g, '');

	main_total = main_total.replace(/\D/g, '');

	var full_summ = $('#full-summ').text();

	full_summ = full_summ.replace(/\D/g, '');

	$('#full-summ').text((parseInt(full_summ) + parseInt(unit_price)) + current_txt);

	var total = 0;

	if (special_price != '') {

		total = parseInt(special_price) * quantity;

		$('#span_total' + product_id).text(total);

		main_total = parseInt(main_total) + parseInt(special_price);

		$('#main-total').text(main_total + current_txt);

		var discount_sum = $('#main-discount').text();

		discount_sum = discount_sum.replace(/\D/g, '');

		var discount_item = $(this).attr('data-discount');

		discount_sum = parseInt(discount_sum) + parseInt(discount_item);

		$('#main-discount').text(discount_sum + current_txt);

	} else {

		main_total = parseInt(main_total) + parseInt(unit_price);

		total = parseInt(unit_price) * quantity;

		$('#span_total' + product_id).text(total);

		$('#main-total').text(main_total + current_txt);

	}

	$('#quantity-' + product_id).val(quantity);

	$('#span-quantity' + product_id).text(quantity);

});

$('.minus').on('click', function (e) {

	var elem = $(this);

	var product_id = elem.attr('data-prdctid');

	var unit_price = elem.attr('data-unit_price');

	var quantity = $('#quantity-' + product_id).val();

	quantity = parseInt(quantity, 10) - 1;

	var div_add = elem.closest(".add-product");

	var id_cart = div_add.attr('data-key');

	var main_count = $('#cart_quantity').text();

	$('#cart_quantity').text(parseInt(main_count) - 1);

	if (quantity == 0) {

		cart.remove(id_cart);

		return false;

	}

	one_update(id_cart, quantity);

	var special_price = elem.attr('data-special');

	var main_total = $('#main-total').text();

	var current_txt = main_total.replace(/[0-9]|\,+/g, '');

	main_total = main_total.replace(/\D/g, '');

	var full_summ = $('#full-summ').text();

	full_summ = full_summ.replace(/\D/g, '');

	$('#full-summ').text((parseInt(full_summ) - parseInt(unit_price)) + current_txt);

	var total = 0;

	if (special_price != '') {

		total = parseInt(special_price) * quantity;

		$('#span_total' + product_id).text(total);

		main_total = parseInt(main_total) - parseInt(special_price);

		$('#main-total').text(main_total + current_txt);

		var discount_sum = $('#main-discount').text();

		discount_sum = discount_sum.replace(/\D/g, '');

		var discount_item = $(this).attr('data-discount');

		discount_sum = parseInt(discount_sum) - parseInt(discount_item);

		$('#main-discount').text(discount_sum + current_txt);

	} else {

		main_total = parseInt(main_total) + parseInt(unit_price);

		total = parseInt(unit_price) * quantity;

		$('#span_total' + product_id).text(total);

		$('#main-total').text(main_total + current_txt);

	}

	$('#quantity-' + product_id).val(quantity);

	$('#span-quantity' + product_id).text(quantity);

});



function one_update(key, quantity) {

	$.ajax({

		url: '/index.php?route=checkout/cart/one_update',

		type: 'post',

		data: 'key=' + key + '&quantity=' + (typeof (quantity) != 'undefined' ? quantity : 1),

		success: function (json) {

			console.log(json);

		},

		error: function (xhr, ajaxOptions, thrownError) {

			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

		}

	});

}



function product_add_to_cart() {

	$.ajax({

		url: '/index.php?route=checkout/cart/minicart',

		success: function (response) {

			var add_cart_div = $("#Modal-product-in-cart");

			var add_to_cart = add_cart_div.find(".block-wrapper-card").eq(0);

			add_to_cart.html(response);

			setTimeout(() => $('button[data-target="#Modal-product-in-cart"]').click(), 1000);

		},

		error: function (xhr, ajaxOptions, thrownError) {

			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

		}

	});

}



$('#register-form').submit(function (e) {

	e.preventDefault();

	$.ajax({

		url: '/index.php?route=account/register/handler',

		type: 'POST',

		data: $(this).serialize(),

		success: function (json) {

			if (json.errors) {

				change_errors(json.errors);

			}

			if (json['success']) {

				window.location.href = json['success'];

			}

		},

		error: function (xhr, ajaxOptions, thrownError) {

			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

		}

	});

});



function change_errors(errors) {

	var form_register = $('#register');

	var input = form_register.find("input");

	var paras = form_register.find('.text-info-custom');

	for (var j = 0; j < paras.length; j++) {

		paras[j].parentNode.removeChild(paras[j]);

	}

	for (var z = 0; z < input.length; z++) {

		var name_inpt = input[z].getAttribute('name');

		if (name_inpt == 'firstname') {

			var err_firstname = errors.firstname;

			if (errors.firstname) {

				input[z].insertAdjacentHTML("beforeBegin", '<span class="text-info-custom">' + errors.firstname + '</span>');

			}

		}

		if (name_inpt == 'lastname') {

			var err_lastname = errors.lastname;

			if (err_lastname) {

				input[z].insertAdjacentHTML("beforeBegin", '<span class="text-info-custom">' + err_lastname + '</span>');

			}

		}

		if (name_inpt == 'telephone') {

			var error_telephone = errors.lastname;

			if (error_telephone) {

				input[z].insertAdjacentHTML("beforeBegin", '<span class="text-info-custom">' + error_telephone + '</span>');

			}

		}

		if (name_inpt == 'email') {

			var err_email = errors.email;

			if (!err_email) {

				err_email = errors.warning;

			}

			if (err_email) {

				input[z].insertAdjacentHTML("beforeBegin", '<span class="text-info-custom">' + err_email + '</span>');

			}

		}

		if (name_inpt == 'password') {

			if (errors.password) {

				input[z].insertAdjacentHTML("beforeBegin", '<span class="text-info-custom">' + errors.password + '</span>');

			}

		}

		if (name_inpt == 'confirm') {

			if (errors.confirm) {

				input[z].insertAdjacentHTML("beforeBegin", '<span class="text-info-custom">' + errors.confirm + '</span>');

			}

		}

		if (name_inpt == 'agree') {

			if (errors.agree) {

				var elt = input[z].closest('div');

				elt.insertAdjacentHTML("beforeBegin", '<span class="text-info-custom">' + errors.agree + '</span>');

			}

		}

	}

}



$('#form-login').submit(function (e) {

	e.preventDefault();

	var form_login = $(this);

	var span_err = form_login.find('.text-info-custom');

	for (var j = 0; j < span_err.length; j++) {

		span_err[j].parentNode.removeChild(span_err[j]);

	}

	$.ajax({

		url: '/index.php?route=account/login/handler',

		type: 'POST',

		data: $(this).serialize(),

		success: function (json) {

			//console.log(json);

			if (json.errors) {

				form_login.prepend('<span class="text-info-custom">' + json.errors.warning + '</span>');

				//change_errors(json.errors);

			}

			if (json['success']) {

				window.location.href = json['success'];

			}

		},

		error: function (xhr, ajaxOptions, thrownError) {

			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

		}

	});

});


function show_wishlist() {
	$.ajax({
		url: '/index.php?route=account/account/wishlist_modal',
		success: function (response) {
			if (response == '0') {
				$('button[data-target="#Modal-login"]').click();
				return false;
			}
			var vishlist_modal = $("#Modal-product-in-vishlist");
			var vishlist_modal = vishlist_modal.find(".block-wrapper-card").eq(0);
			vishlist_modal.html(response);
			setTimeout(() => $('button[data-target="#Modal-product-in-vishlist"]').click(), 1000);
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}

	});
}

function del_from_wishlist_bi_id(id_product) {
	$.ajax({
		url: '/index.php?route=account/wishlist/remove',
		type: 'post',
		data: 'product_id=' + id_product,
		dataType: 'json',
		success: function (json) {
			change_class_remowe_wishlist(id_product);
			if (json == '1') {
				show_emty_wishlist();
				return false;
			}
			$('#wish-' + id_product).remove();
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function show_emty_wishlist() {
	$.ajax({
		url: '/index.php?route=account/account/empty_wishlist_modal',
		success: function (response) {
			var vishlist_modal = $("#Modal-product-in-vishlist");
			var vishlist_modal = vishlist_modal.find(".block-wrapper-card").eq(0);
			vishlist_modal.html(response);
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function change_class_remowe_wishlist(product_id) {
	$('.icon-like').each(function (i, elem) {
		if ($(this).hasClass("active") && $(this).attr('data-id') == product_id) {
			elem.classList.remove("active");
			return false;
		}
	});
}

function del_wish_from_account(like){
	var product_id = like.getAttribute('data-id');
	var li_wish = like.closest("li");
	var ul_wish = like.closest("ul");
	$.ajax({
		url: 'index.php?route=account/account/del_wish_from_account',
		type: 'post',
		data: 'product_id=' + product_id,
		success: function (response) {
			li_wish.parentNode.removeChild(li_wish);
			if(response == '1') {
				return false;
			}
			ul_wish.insertAdjacentHTML('afterend', response);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}
