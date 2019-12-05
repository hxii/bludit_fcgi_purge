<?php
    class fcgi_cache extends Plugin {

	public function init() {
		$this->dbFields = array(
			'purge_format'=>'@siteurl/purge/@posturl'
		);
	}

	public function form() {
		?>
		<p>The URL matching the below format will be accessed (i.e. GET request) when a page is either deleted or modified in order to clear the FCGI cache.</p>
		<div>
		  <label for="fcgi_purge_url">URL Format to use</label>
		  <input id="fcgi_purge_url" name="purge_format" type="text" value="<?php echo $this->getValue('purge_format'); ?>">
		  <small>You can use <code>@siteurl</code> for the site URL and <code>@posturl</code> for the post URL.</small>
		</div>
		<?php
	}

        public function afterPageModify() {
		$this->purge();
	}

	public function afterPageDelete() {
		$this->purge();
	}

	public function purge() {
		$format = $this->parse_format( $this->getValue( 'purge_format' ) );
		file_put_contents( 'test.txt', "format is $format");
		$purge = file_get_contents( $format );
	}

	private function parse_format( string $format ) {
		global $site;
		global $url;
		$current_page = preg_match( '/^edit-content\/(.*?)$/', $url->slug(), $matches );
		$dict = array(
			'@siteurl' => $site->url(),
			'@posturl' => $matches[1],
			);
		return strtr( $format, $dict );
	}

    }
?>
