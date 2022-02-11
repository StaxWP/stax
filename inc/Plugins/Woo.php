<?php

namespace Stax\Plugins;

class Woo {
	public function __construct() {

		add_filter( 'stax_css_files', [$this, 'add_css_file'] );
	}

	public function add_css_file( $files ) {

		$files['stax-woo'] = [
			'file' => 'assets/css/woocommerce.css',
			'global' => true,
		];

		return $files;

	}
}
