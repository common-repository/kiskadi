<?php

namespace Kiskadi;

use Kiskadi\Database\Migration\Hook\Migration_Hook;
use Kiskadi\I18n\Hook\I18n_Hook;
use Kiskadi\Integration\WooCommerce\Admin\Kiskadi_Integration_Admin;
use Kiskadi\Integration\WooCommerce\Checkout\Hook\Checkout_Hook;
use Kiskadi\Integration\WooCommerce\Order_Status;

class Kiskadi {

	/** @var string */
	public $version = '1.3.0';

	/** @var self */
	protected static $instance = null;

	private function __construct() {
		add_filter( 'woocommerce_integrations', array( $this, 'include_integration' ) );
		add_action( 'woocommerce_order_status_changed', array( ( new Order_Status() ), 'change_order_status' ), 10, 3 );

		Checkout_Hook::instance()->init();
		Checkout_Hook::instance()->init();
		I18n_Hook::instance()->init();
		Migration_Hook::instance()->init();
	}

	public static function instance() : self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @param array<string> $integrations Default integrations.
	 *
	 * @return array<string> $integrations
	 */
	public static function include_integration( $integrations ) {
		$integrations[] = Kiskadi_Integration_Admin::class;

		return $integrations;
	}

	public function plugin_path() : string {
		return plugin_dir_path( dirname( __FILE__ ) );
	}

	public function plugin_url() : string {
		return plugin_dir_url( dirname( __FILE__ ) );
	}

	public function get_templates_path() : string {
		return $this->plugin_path() . 'templates/';
	}

	/**
	 * @param array<mixed> $args
	 * @return string|void
	 */
	public function get_template_file( string $template_name, $args = array(), bool $return = false ) {
		if ( $return ) {
			return wc_get_template_html(
				$template_name,
				$args,
				'',
				self::instance()->get_templates_path()
			);
		}

		wc_get_template(
			$template_name,
			$args,
			'',
			self::instance()->get_templates_path()
		);
	}
}
