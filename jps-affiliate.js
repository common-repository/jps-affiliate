// アフィリエイト用ショートコード作成
jQuery(function($) {
	// 商品名
	var jpsaffiliate_title = '#acf-field-jpsaffiliate_title';
	
	// キャッチコピー
	var jpsaffiliate_description = '#acf-field-jpsaffiliate_description';
	
	// 検索キーワード
	var jpsaffiliate_keyword = '#acf-field-jpsaffiliate_keyword';
	
	// ASIN（Amazon）
	var jpsaffiliate_asin = '#acf-field-jpsaffiliate_asin';
	
	// ショートコード表示用フィールド
	var jpsaffiliate_shortcode_field = '#acf-field-jpsaffiliate_shortcode';
	
	// ショートコード
	var jpsaffiliate_shortcode;
	
	// セレクタを配列に格納
	var jpsaffiliate_selector = [];
	jpsaffiliate_selector.push(jpsaffiliate_title);
	jpsaffiliate_selector.push(jpsaffiliate_description);
	jpsaffiliate_selector.push(jpsaffiliate_keyword);
	jpsaffiliate_selector.push(jpsaffiliate_asin);
	
	// ショートコード表示用フィールドを全選択可能にする
	$(jpsaffiliate_shortcode_field).attr('onclick', 'this.select();');
	
	// ショートコードを組み立てて表示
	$(jpsaffiliate_selector.join()).on('keyup', function() {
		jpsaffiliate_shortcode = '[jps-affiliate title="' +
		$(jpsaffiliate_title).val() +
		'" description="' +
		$(jpsaffiliate_description).val() +
		'" keyword="' +
		$(jpsaffiliate_keyword).val() +
		'" asin="' +
		$(jpsaffiliate_asin).val() +
		'"]';
						
		$(jpsaffiliate_shortcode_field).text(jpsaffiliate_shortcode);
	});
});