<?php
/**
 * pr0gramm Inbox Bot
 * 
 * @author    RundesBalli <rundesballi@rundesballi.com>
 * @copyright 2019 RundesBalli
 * @version   2.0
 * @license   MIT-License
 */

/**
 * Einbinden der Konfigurationsdatei.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."config.php");

/**
 * Einbinden der Funktionsdatei.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."functions.php");


/**
 * Abfragen des Sync und feststellen, ob sich ein Element in der Inbox befindet, falls nicht wird das Script beendet.
 */
if(apiCall("https://pr0gramm.com/api/user/sync?offset=9999999")['inboxCount'] == 0) {
  die();
}

$response = apiCall("https://pr0gramm.com/api/inbox/unread");
$response['messages'] = array_reverse($response['messages']);
foreach($response['messages'] as $key => $message) {
  $message['message'] = str_replace("`", "", $message['message']);
  if($message['itemId'] === 0) { // Private Nachricht
    $text = "Neue Nachricht von [@".$message['name']."](https://pr0gramm.com/user/".$message['name'].")\n*".date("d.m.Y, H:i:s", $message['created'])."*\n-----------\n```\n".htmlspecialchars($message['message'], ENT_QUOTES)."\n```\n";
  } elseif($message['itemId'] !== 0) { // Kommentar / Erwähnung
    $text = "Neue(r) Kommentar/Erwähnung von [@".$message['name']."](https://pr0gramm.com/user/".$message['name'].")\n*".date("d.m.Y, H:i:s", $message['created'])."*\n-----------\n[Link zum Kommentar](https://pr0gramm.com/new/".$message['itemId'].":comment".$message['id'].")\n-----------\n```\n".htmlspecialchars($message['message'], ENT_QUOTES)."\n```\n";
  }
  $urls = getURLs($message['message']);
  if(is_array($urls) AND !empty($urls)) {
    $text.="\n*Links:*\n";
    $linkcount = 0;
    foreach($urls as $key => $value) {
      $linkcount++;
      $text.=$linkcount.": ".$value."\n";
    }
  }
  SendMessageToTelegram($text, $chat_id);
}
?>
