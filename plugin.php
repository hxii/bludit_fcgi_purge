<?php
	class fcgi_cache extends Plugin {

		public function init() {
			$this->dbFields = array(
				'purge_format'   => '@siteurl/purge/@posturl',
				'purge_tags'     => true,
				'purge_category' => true,
				'purge_index'    => false,
			);
		}

		public function form() {
			?>
			<p>The URL matching the below format will be accessed (i.e. GET request) when a page is either deleted or modified in order to clear the FCGI cache.</p>
			<div>
			  <label for="fcgi_purge_url">URL Format to use</label>
			  <input id="fcgi_purge_url" name="purge_format" type="text" value="<?php echo $this->getValue( 'purge_format' ); ?>">
			  <small>You can use <code>@siteurl</code> for the site URL and <code>@posturl</code> for the post URL.</small>
			</div>
			<div>
			  <label for="purge_tags">Purge associated tags</label>
			  <select name="purge_tags">
			    <option value="true" <?= ( $this->getValue('purge_tags') ? 'selected' : '' ) ?>>Enabled</option>
			    <option value="false" <?= ( ! $this->getValue('purge_tags') ? 'selected' : '' ) ?>>Disabled</option>
			  </select>
			</div>
			<div>
			  <label for="purge_category">Purge associated category</label>
			  <select name="purge_category">
			    <option value="true" <?= ( $this->getValue('purge_category') ? 'selected' : '' ) ?>>Enabled</option>
			    <option value="false" <?= ( ! $this->getValue('purge_category') ? 'selected' : '' ) ?>>Disabled</option>
			  </select>
			</div>
			<div>
			  <label for="purge_category">Purge index</label>
			  <select name="purge_index">
			    <option value="true" <?= ( $this->getValue('purge_index') ? 'selected' : '' ) ?>>Enabled</option>
			    <option value="false" <?= ( ! $this->getValue('purge_index') ? 'selected' : '' ) ?>>Disabled</option>
			  </select>
			</div>
			<?php
		}

		public function afterPageModify() {
			$page       = $_POST[ 'key' ];
			$parent     = $_POST[ 'parent' ];
			$tags       = ( ! empty( $_POST[ 'tags' ] ) ) ? explode( ',', $_POST[ 'tags' ] ) : '';
			$category   = $_POST[ 'category' ];
			$this->purge( $page );
			if ( $this->getValue( 'purge_tags' ) && ! empty( $tags ) ) {
				foreach ( $tags as $tag ) {
					$this->purge( $tag );
				}
			}
			if ( $this->getValue( 'purge_category' ) && ! empty( $category ) ) {
				$this->purge( $category );
			}
			if ( $this->getValue( 'purge_index' ) ) {
				$this->purge( '' );
			}
			if ( ! empty( $parent ) ) {
				$this->purge( $parent );
			}
		}

		public function afterPageDelete() {
			$this->purge();
		}

		public function purge( $key ) {
			$format = $this->parse_format( $this->getValue( 'purge_format' ), $key );
			$purge = file_get_contents( $format );
		}

		private function parse_format( string $format, string $key ) {
			global $site;
			$dict = array(
				'@siteurl' => $site->url(),
				'@posturl' => $key,
				);
			return strtr( $format, $dict );
		}

	}
?>
