<?php
class Redpic_Related_Cache {
	private $cache_key = 'redpic_related_posts_cache';

	/**
	 * @param $post_id
	 * @param $cache_time
	 *
	 * @return array|bool
	 */
	public function get_cache($post_id, $cache_time) {
		$cache_data = get_post_meta( $post_id, $this->cache_key, true );
		if ( $cache_data && $this->cache_is_valid( $cache_data, $cache_time ) ) {
			return $cache_data['posts'];
		}
		return false;
	}

	private function cache_is_valid( $cache_data, $cache_time ) {
		if ( ! $cache_time ) {
			return false;
		}

		return ( time() - $cache_data['timestamp'] ) <= $cache_time;
	}

	public function save_in_cache($post_id, $cache_ids) {
		update_post_meta(
			$post_id,
			$this->cache_key,
			[
				'timestamp' => time(),
				'posts'     => $cache_ids,
			]
		);
	}
}
