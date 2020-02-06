<?php
/**
 * pr0gramm Inbox Bot
 * 
 * @author    RundesBalli <rundesballi@rundesballi.com>
 * @copyright 2019 RundesBalli
 * @version   3.0
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
 * 
 * Erläuterung, warum nicht sofort /api/inbox/all abgefragt wird:
 * Die Response vom Sync ist nur ein Bruchteil so groß wie die Response von All.
 * Da das Script idR minütlich ausgeführt wird, spart das auf Dauer enorm Traffic ein.
 */
$inbox = apiCall("https://pr0gramm.com/api/user/sync?offset=9999999")['inbox'];
$inboxCount = $inbox['comments']+$inbox['mentions']+$inbox['messages']+$inbox['notifications']+$inbox['follows'];
if($inboxCount == 0) {
  die();
}

/**
 * Es ist wenigstens eine Nachricht vorhanden, also werden alle Nachrichten abgerufen und umgedreht,
 * damit die älteste Nachricht zuerst kommt.
 */
$response = apiCall("https://pr0gramm.com/api/inbox/all")['messages'];
$response = array_reverse($response);

/**
 * Durchgang der gesamten Response
 */
foreach($response as $key => $message) {
  /**
   * Da die Response auch Nachrichten zurückgibt, die bereits gelesen wurden, wird zuerst geprüft,
   * ob die Nachricht schon verarbeitet wurde.
   */
  if($message['read'] == 0) {
    /**
     * Innerhalb eines Code Feldes müssen bei Telegram die Zeichen ` und \ escaped werden.
     * @see https://core.telegram.org/bots/api#formatting-options
     */
    $message['message'] = str_replace("\\", "\\\\", $message['message']);
    $message['message'] = str_replace("`", "\`", $message['message']);
    
    if($message['type'] == 'message') {
      /**
       * Private Nachricht
       */
      $text = "Neue Nachricht von [@".$message['name']."](https://pr0gramm.com/user/".$message['name'].")\n".
      "*".date("d.m.Y, H:i:s", $message['created'])."*\n".
      "-----------\n".
      "```\n".
      htmlspecialchars($message['message'], ENT_QUOTES)."\n".
      "```\n";
    } elseif($message['type'] == 'comment') {
      /**
       * Kommentar
       */
      $text = "Neue(r) Kommentar/Erwähnung von [@".$message['name']."](https://pr0gramm.com/user/".$message['name'].")\n".
      "*".date("d.m.Y, H:i:s", $message['created'])."*\n".
      "-----------\n".
      "[Link zum Kommentar](https://pr0gramm.com/new/".$message['itemId'].":comment".$message['id'].")\n".
      "-----------\n".
      "```\n".
      htmlspecialchars($message['message'], ENT_QUOTES)."\n".
      "```\n";
    } elseif($message['type'] == 'follows') {
      /**
       * Stelz
       */
      $text = "Neuer Upload von [@".$message['name']."](https://pr0gramm.com/user/".$message['name'].")\n".
      "*".date("d.m.Y, H:i:s", $message['created'])."*\n".
      "-----------\n".
      "[Link zum Post](https://pr0gramm.com/new/".$message['itemId'].")\n";
    } elseif($message['type'] == 'notification') {
      /**
       * Systembenachrichtigung
       */
      $text = "Neue Systembenachrichtigung\n".
      "*".date("d.m.Y, H:i:s", $message['created'])."*\n".
      "-----------\n".
      "```\n".
      htmlspecialchars($message['message'], ENT_QUOTES)."\n".
      "```\n";
    } else {
      /**
       * Falls etwas neues implementiert wird, so wird die Ausgabe einfach als JSON ausgegeben.
       */
      $text = "Neue Nachricht eines unbekannten Typs.\n".
      "*".date("d.m.Y, H:i:s", $message['created'])."*\n".
      "-----------\n".
      "```\n".
      htmlspecialchars(json_encode($message), ENT_QUOTES)."\n".
      "```\n";
    }
    /**
     * Alle URLs aus der Nachricht exportieren und separat (klickbar) aufführen.
     */
    $urls = getURLs($message['message']);
    if(is_array($urls) AND !empty($urls)) {
      $text.="\n*Links:*\n";
      $linkcount = 0;
      foreach($urls as $urlkey => $value) {
        $linkcount++;
        $text.=$linkcount.": ".$value."\n";
      }
    }
    /**
     * Senden der Nachricht an Telegram. Sollte das Fehlschlagen wird an den alternativen User
     * weitergeleitet.
     */
    if(SendMessageToTelegram($text, $chat_id) === FALSE) {
      $pnData = array(
        "recipientName" => $alternativeUser,
        "_nonce" => $nonce,
        "comment" => $text
      );
      apiCall("https://pr0gramm.com/api/inbox/post", $pnData);
    }
  }
}
?>
