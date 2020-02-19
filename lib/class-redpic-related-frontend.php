<?php
class Redpic_Related_Frontend {
	private $settings;
	private $post;
	public function __construct() {
		$this->settings = get_option( REDPIC_RELATED_SLUG );
		if ( $this->settings['enabled'] ) {
			add_shortcode('redpic_related', function ($attrs) {
				$params = shortcode_atts( array('template' => ''), $attrs);
				$template = $params['template'] ? $params['template'] : $this->settings['templates'];
				return $this->related_form($template);
			});
			add_filter('the_content', [$this, 'render_content']);
		}
	}

	public function related() {
		require_once __DIR__ . '/class-redpic-related-db.php';
		$db_object = new Redpic_Related_Db();
		$db_result = $db_object->get_related();
		return $db_result;
	}

	public function render_content($content) {
		$this->post = get_post();
		if ($this->settings['enabled'] && $this->check_page()) {
			$content = $content . $this->related_form($this->settings['templates']);
		}
		return $content;
	}

	public function related_form($template) {
		$related = $this->related();
		$rel_item = [];
		foreach ($related as $rel) {
			array_push($rel_item, array(
				'thumbnail' => get_the_post_thumbnail_url($rel->ID, $this->settings['thumbnail']),
				'link' => get_permalink($rel->ID),
				'text' => $this->get_description($rel),
				'author' => $this->get_author($rel->ID),
				'date' => $this->get_date($rel->ID)
			));
		}
		ob_start();
		if( $template === 'standard.php' ) {
			require RELATED_DIR . 'templates/standard.php';
		} else {
			if( $this->check_theme_template_file_exist($template) ) {
				require get_template_directory() . '/' . $template;
			}
		}
		$form = ob_get_contents();
		ob_end_clean();
		return $form;
	}


	private function check_theme_template_file_exist($filename) {
		return file_exists(get_template_directory() . '/' . $filename);
	}

	private function get_description($rel) {
		if ( ((int) $this->settings['short_desc']) > 0) {
			return mb_substr(strip_tags($rel->post_content), 0, $this->settings['short_desc']) . '...';
		}
		return '';
	}

	private function check_page() {
		$result = false;
		if (array_key_exists('single', $this->settings['add_to'])) {
			if ( is_single($this->post) ) {
				$result = true;
			}
		}
		if (array_key_exists('page', $this->settings['add_to'])) {
			if ( is_page($this->post) ) {
				$result = true;
			}
		}
		return $result;
	}

	private function get_date($id) {
		$rel_post = get_post($id);
		return $rel_post->post_date;
	}

	private function get_author($id) {
		$rel_post = get_post($id);
		return $rel_post->post_author;
	}
}
