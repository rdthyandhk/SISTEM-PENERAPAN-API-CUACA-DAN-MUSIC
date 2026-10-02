<?php require 'config.php'; need_login();
header('Content-Type: application/json');
if(($_SERVER['HTTP_X_CSRF']??'')!==$_SESSION['csrf']){http_response_code(403);exit('{"error":"csrf"}');}
$d=json_decode(file_get_contents('php://input'),true)?:[];$u=$_SESSION['uid'];
switch($_GET['a']??''){
 case 'stats':
  $s=$db->prepare('SELECT
    (SELECT COUNT(*) FROM favorites WHERE user_id=?) AS favorites,
    (SELECT COUNT(DISTINCT kota) FROM history WHERE user_id=?) AS cities');
  $s->execute([$u,$u]);echo json_encode($s->fetch());break;
 case 'fav_list':
  $s=$db->prepare('SELECT track_id,title,artist,art,preview FROM favorites WHERE user_id=? ORDER BY id DESC');
  $s->execute([$u]);echo json_encode($s->fetchAll());break;
 case 'fav_add':
  $db->prepare('INSERT IGNORE INTO favorites(user_id,track_id,title,artist,art,preview) VALUES(?,?,?,?,?,?)')
     ->execute([$u,(int)$d['track_id'],mb_substr($d['title'],0,200),mb_substr($d['artist'],0,200),$d['art'],$d['preview']]);
  echo '{"ok":1}';break;
 case 'fav_del':
  $db->prepare('DELETE FROM favorites WHERE user_id=? AND track_id=?')->execute([$u,(int)$d['track_id']]);
  echo '{"ok":1}';break;
 case 'hist_add':
  $db->prepare('INSERT INTO history(user_id,kota) VALUES(?,?)')->execute([$u,mb_substr(trim($d['kota']),0,100)]);
  echo '{"ok":1}';break;
 case 'hist_list':
  $s=$db->prepare('SELECT kota FROM history WHERE user_id=? GROUP BY kota ORDER BY MAX(id) DESC LIMIT 6');
  $s->execute([$u]);echo json_encode($s->fetchAll(PDO::FETCH_COLUMN));break;
 default: http_response_code(404);echo '{"error":"not found"}';
}