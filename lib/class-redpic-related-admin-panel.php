<?php
class Redpic_Related_Admin {

	private function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action(
			'admin_menu',
			function () {
				add_options_page(
					__( 'Redpic Related', 'redpic-related' ),
					__( 'Redpic Related', 'redpic-related' ),
					'manage_options',
					REDPIC_RELATED_SLUG,
					array( $this, 'page_settings' )
				);
			}
		);
		add_action('updated_option', function( $option_name ) {
			if( $option_name === 'redpic-related' ) {
				$options = get_option( REDPIC_RELATED_SLUG );
				file_put_contents( RELATED_DIR . '/css/style.css', $options['css'] );
			}
		}, 10, 3);
		add_action('added_option', function( $option_name ) {
			if( $option_name === 'redpic-related' ) {
				$options = get_option( REDPIC_RELATED_SLUG );
				file_put_contents( RELATED_DIR . '/css/style.css', $options['css'] );
			}
		}, 10, 2);
	}


	public static function init() {
		new self();
	}

	public function page_settings() {
		print '<div class="wrap"><h2>' . __( 'Redpic Related' ) . '</h2>';
		print '<form method="post" action="' . admin_url( 'options.php' ) . '">';
		print '<p>You can use shortcode - [redpic_related template="TEMPLATE_FILE"]</p>';
		settings_fields( REDPIC_RELATED_SLUG );
		do_settings_sections( REDPIC_RELATED_SLUG );
		submit_button();
		print '</form></div>';
	}

	public function register_settings() {
		register_setting(
			REDPIC_RELATED_SLUG,
			REDPIC_RELATED_SLUG
		);
		$this->register_section();
	}

	public function display_settings( $args ) {
		$options = get_option( REDPIC_RELATED_SLUG );
		if ( false === $options ) {
			require_once __DIR__ . '/class-redpic-related-init.php';
			$initialize = new Redpic_Related_Init();
			$initialize->activation();
		} else if ( !is_array( $options ) ) {
			$options = array();
		}
		switch ( $args['type'] ) {
			case 'checkbox':
				$checked = ( array_key_exists( $args['id'], $options ) && $options[ $args['id'] ] == 1 ) ? ' checked="checked"' : '';
				print '<label><input type="checkbox" id="' . $args['id'] . '" value="1" name="' . REDPIC_RELATED_SLUG . '[' . $args['id'] . ']"' . $checked . ' /></label> ';
				print $this->get_description( $args['desc']);
				break;
			case 'text':
				print '<label><input ' . $this->get_size_class($args['size']) . ' type="text" id="' . $args['id'] . '" value="' . $options[ $args['id'] ] . '" name="' . REDPIC_RELATED_SLUG . '[' . $args['id'] . ']"  /></label> ';
				print $this->get_description( $args['desc']);
				break;
			case 'number':
				print '<label><input ' . $this->get_size_class($args['size']) . ' type="number" step="1" max="999999" min="0" id="' . $args['id'] . '" value="' . $options[ $args['id'] ] . '" name="' . REDPIC_RELATED_SLUG . '[' . $args['id'] . ']"  /></label> ';
				print $this->get_description( $args['desc']);
				break;
			case 'textarea':
				print '<label><textarea ' . $this->get_size_class($args['size']) . ' cols="50" rows="5" id="' . $args['id'] . '" name="' . REDPIC_RELATED_SLUG . '[' . $args['id'] . ']"  >' . $options[ $args['id'] ] . '</textarea></label> ';
				print $this->get_description( $args['desc']);
				break;
			case 'radio':
				foreach ($args['choices'] as $choice) {
					print '<label>';
					print '<input type="radio" id="' . $args['id'] . '" value="' . $choice['value'] . '" name="' . REDPIC_RELATED_SLUG . '[' . $args['id'] . ']" ' . checked($choice['value'], $options[ $args['id'] ], false) . ' />';
					print '<span>' . $choice['label'] . '</span>';
					print '</label>';
					print '<br/>';
				}
				print $this->get_description( $args['desc']);
				break;
			case 'checkbox_array':
				foreach ($args['choices'] as $choice) {
					print '<label>';
					print '<input type="checkbox" id="' . $args['id'] . '" value="' . $choice['value'] . '" name="' . REDPIC_RELATED_SLUG . '[' . $args['id'] . '][' . $choice['value'] . ']" ' . checked(in_array($choice['value'], $options[ $args['id'] ]), 1, false) . ' />';
					print '<span>' . $choice['label'] . '</span>';
					print '</label>';
					print '<br/>';
				}
				print $this->get_description( $args['desc']);
				break;
			case 'select':
				print '<label>';
				print '<select ' . $args['disabled'] . ' id="' . $args['id'] . '" name="' . REDPIC_RELATED_SLUG . '[' . $args['id'] . ']">';
				foreach ($args['choices'] as $choice) {
					print '<option value="' . $choice['value'] . '"  ' . selected($choice['value'], $options[ $args['id'] ], false) . '>';
					print  $choice['label'];
					print '</option>';
				}
				print '</select>';
				print '</label>';
				print $this->get_description( $args['desc']);
				break;
		}
	}

	public function register_section() {
		$sections = array(
			array(
				'id' => 'settings',
				'title' => __('Settings'),
			),
			array(
				'id' => 'list',
				'title' => __('List Tuning'),
			),
			array(
				'id' => 'output',
				'title' => __('Output'),
			),
			array(
				'id' => 'thumbnail',
				'title' => __('Thumbnail'),
			),
			array(
				'id' => 'style',
				'title' => __('Style'),
			)
		);
		foreach ($sections as $section) {
			add_settings_section(
				REDPIC_RELATED_SLUG . '_' . $section['id'],
				$section['title'],
				'',
				REDPIC_RELATED_SLUG
			);
			$this->add_fields($section);
		}
	}

	public function add_fields($section) {
		$fields = array();
		switch ($section['id']) {
			case 'settings':
				$fields = array(
					array(
						'id' => 'enabled',
						'title' => __('Enabled'),
						'type' => 'checkbox',
						'desc' => __('On/off plugin')
					),
					array(
						'id' => 'source',
						'title' => __('Source'),
						'type' => 'select',
						'choices' => [
							[
								'label' => __('Categories'),
								'value' => 'categories'
							],
							[
								'label' => __('Tags'),
								'value' => 'tags'
							],
							[
								'label' => __('Context'),
								'value' => 'context'
							]
						],
						'disabled' => '',
						'size' => '',
						'desc' => 'Source of related posts.'
					),
					array(
						'id' => 'cache',
						'title' => __('Cache posts'),
						'type' => 'checkbox',
						'desc' => __('Enabling this will only cache the related posts. Use this if you only have the related posts called with the same set of parameters.')
					),
					array(
						'id' => 'cache_time',
						'title' => __('Cache time'),
						'type' => 'number',
						'size' => 'regular-text',
						'desc' => __('Timestamp time to cache related posts. Standard is 86400 - 1 day.')
					),
					array(
						'id' => 'add_to',
						'title' => __('Automatically add related to'),
						'type' => 'checkbox_array',
						'choices' => [
							[
								'label' => __('Posts'),
								'value' => 'single'
							],
							[
								'label' => __('Pages'),
								'value' => 'page'
							]
						],
						'size' => '',
						'desc' => 'Automatically add related to posts, pages. You can manually add related with shortcode'
					)
				);
				break;
			case 'list':
				$fields = array(
					array(
						'id' => 'amount',
						'title' => __('Amount of posts'),
						'type' => 'number',
						'size' => 'small-text',
						'desc' => __('Maximum number of posts that will be displayed in the list. This option is used if you do not specify the number of posts in the widget or shortcodes')
					),
					array(
						'id' => 'newer',
						'title' => __('Related posts should be newer than'),
						'type' => 'number',
						'size' => 'small-text',
						'desc' => __('This sets the cut-off period for which posts will be displayed. e.g. setting it to 365 will show related posts from the last year only. Set to 0 to disable limiting posts by date.')
					),
					array(
						'id' => 'order',
						'title' => __('Order'),
						'type' => 'radio',
						'choices' => [
							[
								'label' => __('By relevance'),
								'value' => 'relevance'
							],
							[
								'label' => __('Randomly'),
								'value' => 'random'
							],
							[
								'label' => __('By date'),
								'value' => 'date'
							]
						],
						'size' => '',
						'desc' => ''
					),
					array(
						'id' => 'asc_desc',
						'title' => __('Ascending/Descending'),
						'type' => 'radio',
						'choices' => [
							[
								'label' => __('ASC'),
								'value' => 'asc'
							],
							[
								'label' => __('DESC'),
								'value' => 'desc'
							]
						],
						'size' => '',
						'desc' => 'In which order give the result'
					)
				);
				break;
			case 'output':
				$fields = array(
					array(
						'id' => 'no_posts',
						'title' => __('No posts are found'),
						'type' => 'textarea',
						'size' => '',
						'desc' => __('Enter the custom text that will be displayed if no posts are found.')
					),
					array(
						'id' => 'short_desc',
						'title' => __('Short Description Length'),
						'type' => 'number',
						'size' => 'small-text',
						'desc' => __('Short part of content after the title of the post. If set 0, this description will not be displayed. Count in characters')
					),
					array(
						'id' => 'templates',
						'title' => __('Templates'),
						'type' => 'select',
						'choices' => $this->get_templates(),
						'size' => '',
						'disabled' => $this->check_theme_template_file_exist() ? '' : 'disabled',
						'desc' => 'Template of related posts rendered on frontend. To add a new template you need to create PHP file with the name of template in your theme folder.
						<p>Params: </p>
						<ul>
							<li>$rel_item[\'thumbnail\'] - URL link to thumbnail image</li>
							<li>$rel_item[\'link\'] - URL link to the post page</li>
							<li>$rel_item[\'text\'] - Short description of related post</li>
							<li>$rel_item[\'author\'] - Author of the post</li>
							<li>$rel_item[\'date\'] - Creation date of the post</li>
						</ul>'
					),
				);
				break;
			case 'thumbnail':
				$fields = array(
					array(
						'id' => 'thumbnail_size',
						'title' => __('Thumbnail size'),
						'type' => 'radio',
						'choices' => $this->list_thumbnail_sizes(),
						'size' => '',
						'desc' => 'You can choose from existing image sizes'
					)
				);
				break;
			case 'style':
				$fields = array(
					array(
						'id' => 'css',
						'title' => __('Css styles'),
						'type' => 'textarea',
						'size' => '',
						'desc' => __('Enter the custom css rules.')
					),
				);
				break;
		};
		foreach ($fields as $field) {
			add_settings_field($field['id'], $field['title'], [$this, 'display_settings'], REDPIC_RELATED_SLUG, REDPIC_RELATED_SLUG . '_' . $section['id'], $field);
		}
	}


	private function get_templates() {
		$redpic_widgets = [array(
			'label' => 'standard',
			'value' => 'standard.php'
		)];
		if ( $this->check_theme_template_file_exist() ) {
			$redpic_widgets[] = array(
				'label' => 'redpic_related_template',
				'value' => 'redpic_related_template.php'
			);
		}
		return $redpic_widgets;
	}

	private function check_theme_template_file_exist() {
		return file_exists(get_template_directory() . '/redpic_related_template.php');
	}

	private function get_description($desc) {
		if (empty($desc)) {
			return '';
		}
		return '<p class="description">' . $desc .'</p>';
	}

	function list_thumbnail_sizes() {
		global $_wp_additional_image_sizes;
		$sizes = array();
		$rSizes = array();
		foreach (get_intermediate_image_sizes() as $s) {
			$sizes[$s] = array(0, 0);
			if (in_array($s, array('thumbnail', 'medium', 'large'))) {
				$sizes[$s][0] = get_option($s . '_size_w');
				$sizes[$s][1] = get_option($s . '_size_h');
			} else {
				if (isset($_wp_additional_image_sizes) && isset($_wp_additional_image_sizes[$s]))
					$sizes[$s] = array($_wp_additional_image_sizes[$s]['width'], $_wp_additional_image_sizes[$s]['height'],);
			}
		}


		foreach ($sizes as $size => $atts) {
			array_push($rSizes, array(
				'label' => $size . ' ' . implode('x', $atts),
				'value' => $size
			));
		}
		return $rSizes;
	}

	private function get_size_class($size) {
		if (empty($size)) {
			return '';
		}
		return 'class="' . $size . '"';
	}
}
