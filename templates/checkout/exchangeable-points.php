<?php if ( isset( $exchangeable_points ) && isset( $has_cashback ) ) : ?>
	<div class="exchangeable-points">
		<p>
			<?php esc_html_e( 'You can use your cashback points to pay this order.', 'kiskadi' ); ?>
			<?php
					/* translators: %s cashback amount */
				echo wp_kses( sprintf( __( 'Cashback total: %s', 'kiskadi' ), wc_price( $exchangeable_points->available_discount() ) ), wp_kses_allowed_html() );
			?>
		</p>
		<p>
			<input type="hidden" id="kiskadi_cashback_amount" name="kiskadi_cashback_amount" class="kiskadi_cashback_amount" value="<?php echo esc_attr( $exchangeable_points->available_discount() ); ?>" />
			<input type="checkbox" id="kiskadi_exchangeable_points" name="kiskadi_exchangeable_points" class="kiskadi_exchangeable_points" <?php echo ( true === $has_cashback ) ? 'checked' : ''; ?> />
			<label for="kiskadi_exchangeable_points"><?php esc_html_e( 'Use my cashback.', 'kiskadi' ); ?></label>
		</p>
	</div>
<?php endif; ?>
