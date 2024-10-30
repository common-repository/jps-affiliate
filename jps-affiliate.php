<?php
/*
Plugin Name: JPSアフィリエイト
Description: 「Advanced Custom Fields」を使用して、カエレバのようにアフィリエイトを表示させるプラグイン。
Author: Japan PC Service Co., Ltd.
Version: 0.1
License: GPLv2 or later
*/

$jpsaffiliate = new JPS_Affiliate;
$jpsaffiliate->init();

class JPS_Affiliate
{
	const VERSION = '0.1';
	
	private $url;
	private $options;
	private $plugin_prefix = 'jpsaffiliate';
	private $plugin_slug = 'jps-affiliate';
	private $plugin_name = 'JPSアフィリエイト';
	
	function __construct() {
		$path = __FILE__;
		$this->url = plugins_url( '', $path );
		
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}
	
	function init() {
		add_shortcode( $this->plugin_slug, array( $this, 'shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_footer', array( $this, 'enqueue_scripts' ) );
	}
	
	/**
	 * CSSファイルをエンキューする。
	 */
	function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug, $this->url . '/' . $this->plugin_slug . '.css', array(), self::VERSION );
	}
	
	/**
	 * JSファイルをエンキューする。
	 */
	function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug, $this->url . '/' . $this->plugin_slug . '.js', array( 'jquery' ), self::VERSION, true );
	}
	
	/**
	 * 管理メニューに項目を追加する。
	 */
	function add_page() {
		add_options_page(
			$this->plugin_name,
			$this->plugin_name,
			'manage_options',
			$this->plugin_prefix,
			array( $this, 'options_page' )
		);
	}
	
	/**
	 * 設定画面を作成する。
	 */
	function options_page() {
		if ( isset( $_POST['submit'] ) ) {
			check_admin_referer( $this->plugin_prefix . '_action', 'jpsaffiliate_nonce' );
			
			$OPTION = array(
				'amazon_access_key_id' => sanitize_text_field( wp_unslash( $_POST[$this->plugin_prefix . '[amazon_access_key_id]'] ) ),
				'amazon_secret_access_key' => sanitize_text_field( wp_unslash( $_POST[$this->plugin_prefix . '[amazon_secret_access_key]'] ) ),
				'amazon_id' => sanitize_text_field( wp_unslash( $_POST[$this->plugin_prefix . '[amazon_id]'] ) ),
				'moshimo_a_id' => ( int ) wp_unslash( $_POST[$this->plugin_prefix . '[moshimo_a_id]'] ),
				'valuecommerce_sid' => ( int ) wp_unslash( $_POST[$this->plugin_prefix . '[valuecommerce_sid]'] ),
				'valuecommerce_pid' => ( int ) wp_unslash( $_POST[$this->plugin_prefix . '[valuecommerce_pid]'] ),
			);
			update_option( $this->plugin_prefix, $OPTION );
		}
		?>
		<div class="wrap">
			<h2><?php echo $this->plugin_name; ?></h2>
			<h3>IDの設定</h3>
			<p>IDはそれぞれのサイトで広告コードを作成し、作成されたコードから取得してください。<br>AmazonアソシエイトのIDは必須です。</p>
			<form method="post" action="options.php">
				<?php
				wp_nonce_field( $this->plugin_prefix . '_action', 'jpsaffiliate_nonce' );
				settings_fields( $this->plugin_prefix . '_group' );
				do_settings_sections( $this->plugin_prefix );
				submit_button();
				?>
			</form>
			
			<h3>Advanced Custom Fieldsの設定</h3>
			<p>投稿ページでショートコードを作成するには「Advanced Custom Fields」が必要です。<br>下記の設定でカスタムフィールドを追加してください。</p>
			<div class="postbox">
				<table class="widefat">
					<thead>
						<tr>
							<th>フィールドラベル</th>
							<th>フィールド名</th>
							<th>フィールドタイプ</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>商品名</th>
							<td>jpsaffiliate_title</td>
							<td>テキスト</td>
						</tr>
						<tr>
							<th>キャッチコピー</th>
							<td>jpsaffiliate_description</td>
							<td>テキストエリア</td>
						</tr>
						<tr>
							<th>検索キーワード</th>
							<td>jpsaffiliate_keyword</td>
							<td>テキスト</td>
						</tr>
						<tr>
							<th>ASIN（Amazon）</th>
							<td>jpsaffiliate_asin</td>
							<td>テキスト</td>
						</tr>
						<tr>
							<th>挿入用コード</th>
							<td>jpsaffiliate_shortcode</td>
							<td>テキストエリア</td>
						</tr>
					</tbody>
				</table>
			</div><!-- /.postbox-->
		</div><!-- /.wrap -->
		<?php
	}
	
	/**
	 * オプション設定画面を初期化する。
	 */
	function page_init() {
		register_setting(
			$this->plugin_prefix . '_group',
			$this->plugin_prefix,
			array($this, 'sanitize_callback' )
		);
		
		add_settings_section(
			'affiliate_id',
			'',
			'',
			$this->plugin_prefix
		);

		add_settings_field(
			'amazon_access_key_id',
			'Amazonアソシエイト（ACCESS_KEY_ID）',
			array( $this, 'set_amazon_access_key_id' ),
			$this->plugin_prefix,
			'affiliate_id'
		);
		add_settings_field(
			'amazon_secret_access_key',
			'Amazonアソシエイト（SECRET_ACCESS_KEY）',
			array( $this, 'set_amazon_secret_access_key' ),
			$this->plugin_prefix,
			'affiliate_id'
		);
		add_settings_field(
			'amazon_id',
			'Amazonアソシエイト（ID）',
			array( $this, 'set_amazon_id' ),
			$this->plugin_prefix,
			'affiliate_id'
		);
		add_settings_field(
			'moshimo_a_id',
			'もしもアフィリエイト（a_id）',
			array( $this, 'set_moshimo_a_id' ),
			$this->plugin_prefix,
			'affiliate_id'
		);
		add_settings_field(
			'valuecommerce_sid',
			'バリューコマース（sid）',
			array( $this, 'set_valuecommerce_sid' ),
			$this->plugin_prefix,
			'affiliate_id'
		);
		add_settings_field(
			'valuecommerce_pid',
			'バリューコマース（pid）',
			array( $this, 'set_valuecommerce_pid' ),
			$this->plugin_prefix,
			'affiliate_id'
		);
	}
	
	/**
	 * テキストフィールド（Amazonアソシエイト ACCESS_KEY_ID）
	 */
	function set_amazon_access_key_id() {
		$this->options = get_option( $this->plugin_prefix );
		$plugin_prefix = 'amazon_access_key_id';
		$option_value = isset( $this->options[$plugin_prefix] ) ? $this->options[$plugin_prefix] : '';

		echo '<input type="text" name="' . $this->plugin_prefix . '[' . $plugin_prefix . ']" value="' . esc_attr( $option_value ) . '" placeholder="XXXXXXXXXXXXXXXXXXXX">';
	}
	
	/**
	 * テキストフィールド（Amazonアソシエイト SECRET_ACCESS_KEY）
	 */
	function set_amazon_secret_access_key() {
		$this->options = get_option( $this->plugin_prefix );
		$plugin_prefix = 'amazon_secret_access_key';
		$option_value = isset( $this->options[$plugin_prefix] ) ? $this->options[$plugin_prefix] : '';

		echo '<input type="text" name="' . $this->plugin_prefix . '[' . $plugin_prefix . ']" value="' . esc_attr( $option_value ) . '" placeholder="XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">';
	}
	
	/**
	 * テキストフィールド（Amazonアソシエイト ID）
	 */
	function set_amazon_id() {
		$this->options = get_option( $this->plugin_prefix );
		$plugin_prefix = 'amazon_id';
		$option_value = isset( $this->options[$plugin_prefix] ) ? $this->options[$plugin_prefix] : '';

		echo '<input type="text" name="' . $this->plugin_prefix . '[' . $plugin_prefix . ']" value="' . esc_attr( $option_value ) . '" placeholder="XXXX-22">';
	}

	/**
	 * テキストフィールド（もしもアフィリエイト a_id）
	 */
	function set_moshimo_a_id() {
		$this->options = get_option( $this->plugin_prefix );
		$plugin_prefix = 'moshimo_a_id';
		$option_value = isset( $this->options[$plugin_prefix] ) ? $this->options[$plugin_prefix] : '';

		echo '<input type="text" name="' . $this->plugin_prefix . '[' . $plugin_prefix . ']" value="' . esc_attr( $option_value ) . '" placeholder="123456">';
	}

	/**
	 * テキストフィールド（バリューコマース sid）
	 */
	function set_valuecommerce_sid() {
		$this->options = get_option( $this->plugin_prefix );
		$plugin_prefix = 'valuecommerce_sid';
		$option_value = isset( $this->options[$plugin_prefix] ) ? $this->options[$plugin_prefix] : '';

		echo '<input type="text" name="' . $this->plugin_prefix . '[' . $plugin_prefix . ']" value="' . esc_attr( $option_value ) . '" placeholder="1234567">';
	}

	/**
	 * テキストフィールド（バリューコマース pid）
	 */
	function set_valuecommerce_pid() {
		$this->options = get_option( $this->plugin_prefix );
		$plugin_prefix = 'valuecommerce_pid';
		$option_value = isset( $this->options[$plugin_prefix] ) ? $this->options[$plugin_prefix] : '';

		echo '<input type="text" name="' . $this->plugin_prefix . '[' . $plugin_prefix . ']" value="' . esc_attr( $option_value ) . '" placeholder="123456789">';
	}

	/**
	 * 入力内容をチェックする。
	 *
	 * @param string $input
	 * @return string
	 */
	function sanitize_callback( $input ) {
		$new_input = array();
		
		if ( $input ) {
			if ( preg_match( '/^[A-Z0-9]*$/', $input['amazon_access_key_id'] ) ) {
				$new_input['amazon_access_key_id'] = $input['amazon_access_key_id'];
			} else {
				add_settings_error( 'amazon_access_key_id', 'amazon_access_key_id', 'AmazonアソシエイトのACCESS_KEY_IDが不正です。' );
			}
			
			if ( preg_match( '/^[!-~]*$/', $input['amazon_secret_access_key'] ) ) {
				$new_input['amazon_secret_access_key'] = $input['amazon_secret_access_key'];
			} else {
				add_settings_error( 'amazon_secret_access_key', 'amazon_secret_access_key', 'AmazonアソシエイトのSECRET_ACCESS_KEYが不正です。' );
			}
			
			if ( preg_match( '/^[a-zA-Z0-9]+-[0-9]{2}$/', $input['amazon_id'] ) ) {
				$new_input['amazon_id'] = $input['amazon_id'];
			} else {
				add_settings_error( 'amazon_id', 'amazon_id', 'AmazonアソシエイトのIDが不正です。' );
			}
			
			if ( preg_match( '/^[0-9]{6}$/', $input['moshimo_a_id'] ) ) {
				$new_input['moshimo_a_id'] = $input['moshimo_a_id'];
			} elseif ( !empty( $input['moshimo_a_id'] ) ) {
				add_settings_error( 'moshimo_a_id', 'moshimo_a_id', 'もしもアフィリエイトのIDが不正です。' );
			}
			
			if ( preg_match( '/^[0-9]{7}$/', $input['valuecommerce_sid'] ) ) {
				$new_input['valuecommerce_sid'] = $input['valuecommerce_sid'];
			} elseif ( !empty( $input['valuecommerce_sid'] ) ) {
				add_settings_error( 'valuecommerce_sid', 'valuecommerce_sid', 'バリューコマースのsidが不正です。' );
			}
			
			if ( preg_match( '/^[0-9]{9}$/', $input['valuecommerce_pid'] ) ) {
				$new_input['valuecommerce_pid'] = $input['valuecommerce_pid'];
			} elseif ( !empty( $input['valuecommerce_pid'] ) ) {
				add_settings_error( 'valuecommerce_pid', 'valuecommerce_pid', 'バリューコマースのpidが不正です。' );
			}
		}
		
		return $new_input;
	}	
	
	/**
	 * ASINから画像を取得する。
	 *
	 * @param string $amazon_id
	 * @param string $asin
	 * @return string
	 */
	function get_amazon_img( $amazon_id, $asin ) {
		$access_url = 'http://ecs.amazonaws.jp/onca/xml';
		$params = array(); 
		$params['Service']       = 'AWSECommerceService';
		$params['Version']       = '2011-08-02';
		$params['Operation']     = 'ItemLookup';
		$params['ItemId']        = $asin;
		$params['IdType']        = 'ASIN';
		$params['AssociateTag']  = $amazon_id;
		$params['ResponseGroup'] = 'ItemAttributes,Images';
		$params['Timestamp']     = gmdate('Y-m-d\TH:i:s\Z');
		 
		ksort( $params );
		 
		$canonical_string = 'AWSAccessKeyId=' . $this->options['amazon_access_key_id'];
		foreach ( $params as $k => $v ) {
			$canonical_string .= '&' . $this->urlencode_RFC3986( $k ) . '=' . $this->urlencode_RFC3986( $v );
		}
		 
		$parsed_url = parse_url( $access_url );
		$string_to_sign = "GET\n{$parsed_url['host']}\n{$parsed_url['path']}\n{$canonical_string}";
		 
		$signature = base64_encode(
			hash_hmac( 'sha256', $string_to_sign, $this->options['amazon_secret_access_key'], true )
		);
		 
		$url = $access_url . '?' . $canonical_string . '&Signature=' . $this->urlencode_RFC3986( $signature );
		 
		// Amazonへレスポンス
		$response = wp_remote_request(
			$url,
			array(
				'timeout' => 30,
			)
		);
		 
        $body = wp_remote_retrieve_body( $response );
         
		$parsed_xml = null;
		
		// レスポンスを配列で取得
		if ( isset( $body ) ) {
			$parsed_xml = @simplexml_load_string( $body );
		}
		 
		// Amazonへのレスポンスが正常に行われていたら
		if ( 'True' == (string) @$parsed_xml->Items->Request->IsValid ) {
			foreach ( $parsed_xml->Items->Item as $current ) {
				$img_url = $current->MediumImage->URL;  
			}
			
			return $img_url;
		}
	}
	
	/**
	 * RFC3986形式でURLエンコードする。
	 *
	 * @param string $str
	 * @return string
	 */
	function urlencode_RFC3986 ( $str ) {
		return str_replace( '%7E', '~', rawurlencode( $str ) );
	}
	
	/**
	 * ショートコードを作成する。
	 *
	 * @param string $atts
	 * @return string
	 */
	function shortcode( $atts ) {
		$this->options = get_option( $this->plugin_prefix );
		
		extract(
			shortcode_atts(
				array(
					'title' => '',
					'description' => '',
					'keyword' => '',
					'asin' => '',
					'amazon_id' => isset( $this->options['amazon_id'] ) ? esc_attr( $this->options['amazon_id'] ) : '',
					'moshimo_a_id' => isset( $this->options['moshimo_a_id'] ) ? esc_attr( $this->options['moshimo_a_id'] ) : '',
					'valuecommerce_sid' => isset( $this->options['valuecommerce_sid'] ) ? esc_attr( $this->options['valuecommerce_sid'] ) : '',
					'valuecommerce_pid' => isset( $this->options['valuecommerce_pid'] ) ? esc_attr( $this->options['valuecommerce_pid'] ) : '',
				),
				$atts
			)
		);
		
		// 商品名
		$title = esc_html( $title );
		
		// 画像のalt
		$alt = esc_attr( $title );
		
		// キャッチコピー
		$description = esc_html( $description );
		
		// 検索キーワード
		$keyword = urlencode( $keyword );
		$keyword = preg_replace('/(%)([A-Z0-9]{2})/', '%25$2', $keyword);
		
		// 商品画像
		$img = esc_url( $this->get_amazon_img( $amazon_id, $asin ) );
		
		// AmazonのURL
		$amazon_url = 'http://www.amazon.co.jp/exec/obidos/ASIN/' . $asin . '/' . $amazon_id . '/';
		$amazon_url = esc_url( $amazon_url );
		$amazon_tag = '<li class="amazon"><a href="' . $amazon_url . '" target="_blank">Amazon</a></li>';
		
		// 楽天市場のURL
		if ( $moshimo_a_id ) {
			$rakuten_url = '//af.moshimo.com/af/c/click?a_id='. $moshimo_a_id . '&p_id=54&pc_id=54&pl_id=616&url=http%3A%2F%2Fsearch.rakuten.co.jp%2Fsearch%2Fmall%2F' . $keyword . '%2F';
			$rakuten_url = esc_url( $rakuten_url );
			$rakuten_tag = '<li class="rakuten"><a href="' . $rakuten_url . '" target="_blank">楽天市場</a></li>';
		} else {
			$rakuten_tag = '';	
		}
		
		// Yahoo!ショッピングのURL
		if ( $valuecommerce_sid && $valuecommerce_pid ) {
			$yahoo_url = '//ck.jp.ap.valuecommerce.com/servlet/referral?sid=' . $valuecommerce_sid . '&pid=' . $valuecommerce_pid . '&vc_url=http%3A%2F%2Fsearch.shopping.yahoo.co.jp%2Fsearch%3Fp%3D' . $keyword;
			$yahoo_url = esc_url( $yahoo_url );
			$yahoo_tag = '<li class="yahoo"><a href="' . $yahoo_url . '" target="_blank">Yahoo!ショッピング</a></li>';
		} else {
			$yahoo_tag = '';
		}
		
		$tags = <<< _EOL_
<div class="{$this->plugin_prefix}">
	<div class="affiliate-img"><a href="{$amazon_url}" target="_blank"><img src="{$img}" alt="{$alt}"></a></div>
	<div class="affiliate-desc">
		<p class="affiliate-title"><a href="{$amazon_url}" target="_blank">{$title}</a></p>
		<p class="affiliate-description">{$description}</p>
		<ul class="affiliate-link">
			{$amazon_tag}
			{$rakuten_tag}
			{$yahoo_tag}
		</ul>
	</div><!-- /.affiliate-desc -->
</div><!-- /.{$this->plugin_prefix} -->
_EOL_;
		
		if ( $title && $amazon_id && $img !== null) {
			return $tags;
		} else {
			return false;	
		}
	}
}

function jpsaffiliate_uninstall() {
	delete_option( 'jpsaffiliate' );
}

if ( function_exists( 'register_uninstall_hook' ) ) {
	register_uninstall_hook( __FILE__,  'jpsaffiliate_uninstall' );
}

