jQuery(
	function ($) {
		let kiskadi_exchangeable_points = $( '#kiskadi_exchangeable_points' ).is( ':checked' );
		$( document ).on(
			'updated_checkout',
			function () {
				let billing_document = '';
				if($("#billing_persontype").length == 1){
					billing_document = ($('#billing_persontype').val() == 1)? $( '#billing_cpf' ).val() : $( '#billing_cnpj' ).val();
				}else{
					billing_document = ($('#billing_cpf').length == 1 && $('#billing_cpf').val().length > 1)? $( '#billing_cpf' ).val() : $( '#billing_cnpj' ).val();
				}
				let data = {
					action: 'kiskadi_cashback_available',
					billing_cpf: billing_document,
					kiskadi_exchangeable_points : kiskadi_exchangeable_points,
					wp_nonce: kiskadi_param.wp_nonce
				};

				$.post(
					kiskadi_param.ajax_url,
					data,
					function (response) {
						if ( response.success ) {
							$( '#payment .exchangeable-points' ).remove();
							$( '#payment' ).prepend( response.data.template );
						}
					}
				);
			}
		);

		$( document ).on(
			'blur',
			'#billing_cpf, #billing_cnpj',
			function (event) {
				event.stopImmediatePropagation();
				$( document.body ).trigger( 'update_checkout' );
			}
		);

		$( document ).on(
			'change',
			'.kiskadi_exchangeable_points',
			function (event) {
				event.stopImmediatePropagation();
				$( document.body ).trigger( 'update_checkout' );
				if($( '#kiskadi_exchangeable_points' ).is( ':checked' )){
					kiskadi_exchangeable_points = true;
				}else{
					kiskadi_exchangeable_points = false;
				}
			}
		);

	}
);