<?php
class Redpic_Related_Db {
	private $db;
	private $post;
	private $settings;

	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		$this->post     = get_post();
		$this->settings = get_option( REDPIC_RELATED_SLUG );
	}

	public function get_related() {
		if ( ! $this->post ) {
			return [];
		}
		if ( $this->settings['cache']) {
			require_once __DIR__ . '/class-redpic-related-cache.php';
			$cache_handler = new Redpic_Related_Cache();
			$cache         = $cache_handler->get_cache( $this->post->ID, $this->settings['cache_time'] );
			if ( $cache ) {
				$sql    = "SELECT `p`.`ID`, `p`.`post_title`, `p`.`post_content` FROM `{$this->db->prefix}posts` AS `p`
					WHERE `p`.ID IN (";
				$sql    .= implode( ',', $cache );
				$sql    .= ')';
				$result = $this->db->get_results( $sql );

				return $result;
			}
		}
		$sql = "SELECT `p`.`ID`, `p`.`post_title`, `p`.`post_content` FROM `{$this->db->prefix}posts` AS `p`
					INNER JOIN `{$this->db->prefix}term_relationships` AS `tr` ON `p`.`ID` = `tr`.`object_id`
					INNER JOIN `{$this->db->prefix}term_taxonomy` AS `tt` ON `tt`.`term_taxonomy_id` = `tr`.`term_taxonomy_id`
					WHERE
						`p`.`post_status` = \"publish\"
                        AND (`tt`.`taxonomy` = \"category\" OR `tt`.`taxonomy` = \"post_tag\")
                        AND `p`.ID <> {$this->post->ID}";

		$sql .= $this->newer_then();
		switch ( $this->settings['source'] ) {
			case 'categories':
				$categories = get_the_category( $this->post->ID );

				if ( ! count( $categories ) || !$categories ) {
					return __return_empty_array();
				}

				$sql .= $this->get_term_sql( $categories );
				break;
			case 'tags':
				$tags = get_the_tags( $this->post->ID );

				if ( ! count( $tags ) || !$tags ) {
					return __return_empty_array();
				}

				$sql .= $this->get_term_sql( $tags );
				break;
			case 'context':
				$relevance_sort = $this->settings['order'] === 'relevance' ? ' IN BOOLEAN MODE' : '';
				$sql .= " AND MATCH (`p`.`post_title`,`p`.`post_content`) AGAINST ('" . esc_sql( $this->post->post_title ) . "'" . $relevance_sort . ")";
				break;
			default:
				return __return_empty_array();
		}
		$sql .= $this->order();
		$sql .= sprintf( ' LIMIT %s', (int) $this->settings['amount'] );

		$result = $this->db->get_results( $sql );
		if ($this->settings['cache']) {
			$cache_handler->save_in_cache( $this->post->ID, array_map( function ( $res ) {
				return $res->ID;
			}, $result ) );
		}
		return $result;
	}

	private function newer_then() {
		$result = '';
		if(!empty($this->settings['newer']) && (int) $this->settings['newer'] > 0) {
			$date = new DateTime();
			$date->modify( '-'. $this->settings['newer'] . ' day' );
			$result = ' AND `p`.post_date >= "' . $date->format( 'Y-m-d H:i:s' ) . '"';
		}
		return $result;
	}

	private function asc_desc() {
		if( $this->settings['asc_desc'] === 'asc' ) {
			return ' ASC';
		}
		return ' DESC';
	}

	private function order() {
		$result = '';
		if(!empty($this->settings['order'])) {
			if($this->settings['order'] === 'random') {
				$result = ' ORDER BY RAND()' . $this->asc_desc();
			}
			if($this->settings['order'] === 'date') {
				$result = ' ORDER BY post_date' . $this->asc_desc();
			}
		}
		return $result;
	}

	private function get_term_sql( $objects ) {
		$ids = [];
		foreach ( $objects as $object ) {
			$ids[] = $object->term_id;
		}

		return sprintf(
			' AND `tt`.`term_id` IN (%s)',
			implode( ',', $ids )
		);
	}
}
