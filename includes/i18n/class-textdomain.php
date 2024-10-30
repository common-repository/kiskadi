<?php

namespace Kiskadi\I18n;

class Textdomain {

	public function load_plugin_textdomain() : void {
		$domain          = 'kiskadi';
		$plugin_rel_path = 'kiskadi/languages';

		load_plugin_textdomain( $domain, false, $plugin_rel_path );
	}
}
