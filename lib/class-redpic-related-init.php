<?php
class Redpic_Related_Init {
	private $options;

	public function __construct() {
		$this->options = array(
			'enabled' => '1',
			'cache' => '',
			'cache_time' => '86400',
			'short_desc' => '0',
			'newer' => '0',
			'amount' => '5',
			'add_to' => array(
				'single' => 'single'
			),
			'no_posts' => '',
			'order' => 'random',
			'source' => 'context',
			'asc_desc' => 'asc',
			'templates' => 'standard.php',
			'thumbnail_size' => 'thumbnail',
			'css' => ''
		);
	}

	public function activation() {
		update_option( REDPIC_RELATED_SLUG, $this->options );
	}

	public function deactivation() {
		delete_option( REDPIC_RELATED_SLUG );
	}

}
