<?php

$username = $_GET['user'];

//Funktion für XML Erzeugung via API
function create_xml_for_user($username) { 
require 'config.php';
header('Content-Type: application/rss+xml');


// URL der API mit dem angegebenen Benutzernamen
$api_url = $site_url . '/api.php?type=posts_data&user=' . $username . '&limit=LIMIT';

// Aufruf der API und Speichern der Antwort in einer Variablen
$response = file_get_contents($api_url);

// Entpacken der Antwort in ein Array
$posts_data = json_decode($response, true);

// Überprüfung, ob der Aufruf erfolgreich war
if ($posts_data['api_status'] == 'success') {
  // Initialisieren des RSS-Feeds
  $rssFeed = new SimpleXMLElement('<rss></rss>');
  $rssFeed->addAttribute('version', '2.0');

  $channel = $rssFeed->addChild('channel');
  $channel->addChild('title', 'Blogposts von ' . $username);
  $channel->addChild('link', $site_url . $username);
  $channel->addChild('description', 'RSS-Feed mit den neuesten Blogposts von ' . $username);

// Hinzufügen der Blogposts zum RSS-Feed
  foreach ($posts_data['items'] as $post) {
    $newItem = $channel->addChild('item');
    $guid = $newItem->addChild('guid', $post['publisher_data']['url']);
    $guid->addAttribute('isPermaLink', 'false');

// Titel des Blogposts
    if (!empty($post['post_data']['post_text'])) {
      $post_title = $post['post_data']['post_text'];
    } else if (!empty($post['post_data']['post_file'])) {
      $post_title = 'Bildpost';
    } else if (!empty($post['post_data']['post_soundcloud'])) {
      $post_title = 'Soundcloud-Audioclip';
    } else if (!empty($post['post_data']['post_youtube'])) {
      $post_title = 'YouTube-Video';
    } else if (!empty($post['post_data']['post_vine'])) {
      $post_title = 'Vine-Video';
    } else if (!empty($post['post_data']['post_map'])) {
      $post_title = 'Karte';
    }

    $newItem->addChild('title', $post_title);
   $newItem->addChild('link', $post['publisher_data']['url']);
    $newItem->addChild('description', $post_title);
    $newItem->addChild('pubDate', date('D, d M Y H:i:s O', $post['post_data']['post_time']));
	

// Falls post_file vorhanden ist, wird diese als Enclosure hinzugefügt
	if (!empty($post['post_data']['post_file'])) {
	  $enclosure = $newItem->addChild('enclosure');
	  $enclosure->addAttribute('url', $post['post_data']['post_file']);
	  $enclosure->addAttribute('type', 'image/jpeg,image/png');
	  // Hier könntest du auch "image/png" oder einen anderen Dateityp angeben, je nachdem, welcher Dateityp verwendet wird.
$enclosure->addAttribute('length', 12345);
    } else {
// Falls keine post_file vorhanden ist, wird das profilbild des Publishers als Enclosure hinzugefügt
      $enclosure = $newItem->addChild('enclosure');
      $enclosure->addAttribute('url', $post['publisher_data']['profile_picture']);
	  $enclosure->addAttribute('type', 'image/jpeg,image/png');
	   // Hier könntest du auch "image/png" oder einen anderen Dateityp angeben, je nachdem, welcher Dateityp verwendet wird.
$enclosure->addAttribute('length', 12345);
    
  }

// Speicherung der XML-Datei im Verzeichnis xml
  $directory = dirname(__FILE__) . "/xml/";
  $file = fopen($directory . $username . ".xml", "w");
   fwrite($file, $rssFeed->asXML());
   fclose($file);
   }

 } else {
   // Fehlermeldung, falls der Aufruf der API nicht erfolgreich war
   echo 'Beim Aufruf der API ist ein Fehler aufgetreten, dies kann daran liegen das, dass Profil leer ist oder gesperrt.';
 }
}

// Ausgabe des Links zur XML-Datei
echo '<a href="/xml/' . $username . '.xml">Link zum Feed</a></br>';

if (isset($_GET['user'])) {
  create_xml_for_user($_GET['user']);
}

 ?>
