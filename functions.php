<?php
/**
 * functions.php
 * 
 * Funktionen für den pr0gramm Inbox Bot
 */

/**
 * Funktion zum exportieren von URLs aus einem Text.
 * 
 * @param string Der Text aus dem die URLs exportiert werden sollen
 * 
 * @return array/NULL Wenn gültige URLs vorhanden sind wird ein Array zurückgegeben, sonst NULL.
 */
function getURLs($text) {
  $regex = '/(^|\s)((https?:\/\/)?([\w-]+)(\.[a-z-]{2,})(\.[a-z-]{2,})?(\/\S*)?)/i';
  preg_match_all($regex, $text, $matches);
  $urls = array();
  foreach($matches[0] as $key => $value) {
    $url = trim($value);
    $dnsurl = parse_url($url, PHP_URL_HOST);
    if(!empty($dnsurl)) {
      if(checkdnsrr($dnsurl, "A")) {
        $urls[] = $url;
      }
    }
  }
  if(empty($urls)) {
    return NULL;
  } else {
    return $urls;
  }
}

/**
 * Funktion zum Senden von Nachrichten an einen Telegram Clients.
 * 
 * @param string  Der zu sendende Text
 * @param string  Die Chat-ID an die der Text gesendet werden soll
 * @param boolean Wenn die Benachrichtigung des Clients nicht erfolgen soll, dann TRUE.
 * 
 * @return boolean Bei Erfolg TRUE, im Fehlerfall FALSE.
*/
function SendMessageToTelegram($text = NULL, $chat_id = NULL, $disableNotification = FALSE) {
  if($text == NULL OR $chat_id == NULL) {
    return FALSE;
  }
  
  global $apiToken;
  global $bindTo;
  global $telegamUseragent;
  
  $postdata = array(
  'chat_id' => $chat_id,
  'text' => $text,
  'parse_mode' => 'Markdown',
  'disable_notification' => $disableNotification,
  'disable_web_page_preview' => TRUE
  );
  $data = http_build_query($postdata);
  
  $ch = curl_init();
  curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'https://api.telegram.org/bot'.$apiToken.'/sendMessage',
    CURLOPT_USERAGENT => $telegamUseragent,
    CURLOPT_INTERFACE => $bindTo,
    CURLOPT_POST => 1,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_POSTFIELDS => $data
  ));
  $response = curl_exec($ch);
  $errno = curl_errno($ch);
  $errstr = curl_error($ch);
  if($errno != 0) {
    return FALSE;
  }
  $http_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  if($http_code != 200) {
    return FALSE;
  }
  curl_close($ch);
  return TRUE;
}
?>
