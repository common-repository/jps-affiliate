=== JPSアフィリエイト ===
Contributors:
Donate link:
Tags:
Requires at least: 4.9.8
Tested up to: 4.9.8
Stable tag: 0.1
License: GPLv2 or later

「Advanced Custom Fields」で作成したカスタムフィールドを使用して、カエレバのようなアフィリエイトリンクを表示させるショートコードを作成します。

== Installation ==

1. `jps-affiliate`を`/wp-content/plugins/`ディレクトリにアップロードします。
2. プラグインメニューからプラグインを有効化します。
3. `設定` > `JPSアフィリエイト`に各アフィリエイトのIDを設定します。
4. `Advanced Custom Fields`でカスタムフィールドを作成します。

フィールドラベル  フィールド名                フィールドタイプ
`商品名`          `jpsaffiliate_title`        `テキスト`
`キャッチコピー`  `jpsaffiliate_description`  `テキストエリア`
`検索キーワード`  `jpsaffiliate_keyword`      `テキスト`
`ASIN（Amazon）`  `jpsaffiliate_asin`         `テキスト`
`挿入用コード`    `jpsaffiliate_shortcode`    `テキストエリア`

== Changelog ==

= 0.1 =
* 最初のリリース

== Upgrade Notice ==

