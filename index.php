<?php
// パラメーター名取得
$gets = array_keys($_GET);

$sm_id = $gets[1] ?? null;

NicoIframe::render($sm_id);

/**
 * ニコニコ動画の貼り付け用のiframeをhttpsで頑張って表示させるやつ
 * @author 128Na(https://twitter.com/128Na) 
 * @since  PHP7 and higher.
 * @copyright WTFPL(http://www.wtfpl.net/txt/copying/)
 */
class NicoIframe
{
  // 表示したれ。
  public static function render($sm_id)
  {
    if (static::is_nicovideo($sm_id)) {
      $iframe = static::get_prepared_iframe($sm_id);
      echo $iframe;
    }
  }

  // iframeの中身を加工して表示する
  public static function get_prepared_iframe($sm_id)
  {
    $iframe = file_get_contents("http://ext.nicovideo.jp/thumb{$sm_id}");

    $iframe = static::thumb_replace($iframe);
    $iframe = static::https_replace($iframe);

    return $iframe;
  }

  //ニコニコ動画のID形式にマッチするか判定
  public static function is_nicovideo($sm_id)
  {
    // 動画ID命名則
    // http://dic.nicovideo.jp/a/id
    $reg = '/sm|nm|am|fz|ut|dm|ax|ca|cd|cw|fx|ig|na|om|sd|sk|yk|yo|za|zb|zc|zd|ze|nl|so\d+/';
    return preg_match($reg, $sm_id) === 1;
  }

  // サムネはhttps未対応なので画像取ってきてベース64エンコで張り付ける。
  protected static function thumb_replace($str)
  {
    $reg = '/http:\/\/tn-skr[^"]*/';
    if (preg_match($reg, $str, $matches) === 1) {
      $thumb = file_get_contents(array_shift($matches));
      $thumb = base64_encode($thumb);
      $str = preg_replace($reg, "data:image/jpeg;base64,{$thumb}", $str);
    }
    return $str;
  }

  // URLをすべてhttpsへ書き換え。対応していないサイトは知らん。
  protected static function https_replace($str)
  {
    return str_replace('http://', 'https://', $str);
  }
}