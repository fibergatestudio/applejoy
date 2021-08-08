/*
Phone mask
*/
let count_error = 0;
window.addEventListener('input', function (e) {
	$('div.error-phone').hide()
	if (e.target.classList.contains('standard-phone')){
		if (e.target.value[0] !== '+' && e.target.value.length == 1) {
			e.target.value = '+380'
		}
		else if(e.target.value.length > 16){
			if (e.target.value.length == 17) {
				e.target.value = e.target.value.slice(0, -1)
			}
			else{
				e.target.value = '+380'
			}
		}
		else if(e.target.value.length < 5) e.target.value = '+380'
		else{
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
$('#fast-order').submit(function(e) {
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
			if(json['success']){
				document.getElementById('Modal-buy-one-click').hidden = true;
				$('button[data-target="#Modal-order-done"]').click();
			}
		}
	});
});


/*
Modal
*/
$('.btn-buy-one-click, .wrapper-btn .btn-2').click((e)=>{
	$('#fast-order [name="product_id"]').val($(e.target).attr('data-product-id'))
	$('button[data-target="#Modal-buy-one-click"]').click()
})

$('.continue-buy').click((e)=>{
	document.getElementById('Modal-order-done').hidden = true;
	$('.modal-backdrop.fade').removeClass('show');
})

$('.item-block-function.basket a').click((e)=>{
	e.preventDefault()
	$('button[data-target="#Modal-mini-cart"]').click()
})

$(document).ready(function() {
	upload_minicart();

});

function upload_minicart(){
	$.ajax({
		url: 'index.php?route=checkout/cart/minicart',
		success: function(response) {

			rewrite_html_minicart(response);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function rewrite_html_minicart(response){
	var minicart_div = $("#Modal-mini-cart");
	var minicart = minicart_div.find(".block-wrapper-card").eq(0);
	 minicart.html(response);
}

function mini_remove(cart_id, quantity){
	cart.remove(cart_id);
	var main_count = $('#cart_quantity').text();
	$('#cart_quantity').text(parseInt(main_count) - quantity);
	//alert("Quantity: " + quantity);
	setTimeout(upload_minicart, 5);
}
