<?php
  require_once("./phpQuery-onefile.php");

  // データーベースに接続
  try {
    $db = new PDO('mysql:dbname=mounten;port=8889;host=127.0.0.1;charset=utf8','root','root');
  } catch (PDOException $e) {
    echo 'DB接続エラー: ' . $e->getMessage();
  }
  //lite test
      $db->exec("TRUNCATE table serch_result_handlebars");

      // チェーン・リアクションMTBハンドルバーの検索結果
      $ht = file_get_contents("https://www.chainreactioncycles.com/jp/ja/%E3%83%8F%E3%83%B3%E3%83%89%E3%83%AB%E3%83%90%E3%83%BC?f=2258");

      // 検索結果の最大ページ数をカウント
      $pagination = count(phpQuery::newDocument($ht)->find("div")->find(".pagination")->find("a"));

      for($j=1;$j<=$pagination;$j++){
        $html = file_get_contents("https://www.chainreactioncycles.com/jp/ja/%E3%83%8F%E3%83%B3%E3%83%89%E3%83%AB%E3%83%90%E3%83%BC?f=2258&page=$j");

        // 検索結果のアイテム数をカウント
        $b = count(phpQuery::newDocument($html)->find("div")->find(".products_details_container"));

          for($i=0;$i<$b;$i++){
              $name[] = phpQuery::newDocument($html)->find("div")->find(".products_details_container:eq($i)")->find(".description")->find("a")->text();
              $price[] = phpQuery::newDocument($html)->find("div")->find(".products_details_container:eq($i)")->find(".fromamt")->text();
              $image[] = phpQuery::newDocument($html)->find("div")->find(".products_details_container:eq($i)")->find('img')->attr('src');
              $link[] = phpQuery::newDocument($html)->find("div")->find(".products_details_container:eq($i)")->find('a')->attr('href');
          }
        }
          // データーベースにインサート
          foreach (array_map(null, $name, $price,$image,$link) as [$name_replace, $price_replace, $image_replace, $link_replace]){
            $name_replace = str_replace(array("\r\n","\r","\n"), '', $name_replace);
            $price_replace = str_replace(array("\r\n","\r","\n"), '', $price_replace);
            $image_replace = str_replace(array("\r\n","\r","\n"), '', $image_replace);
            $link_replace = str_replace(array("\r\n","\r","\n"), '', $link_replace);
            $hoge ="INSERT INTO serch_result_handlebars (name, price, image_url, click_url, site_name) VALUES ('"."$name_replace"."', '"."$price_replace"."', '"."$image_replace"."', '"."$link_replace"."', 'chainReactionCycles')";
            $db->exec($hoge);
          }

?>
