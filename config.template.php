<?php
/**
 * config.php
 * 
 * Konfiguration für den pr0gramm Inbox Bot
 */

/**
 * Einbinden des apiCalls.
 * Download: https://github.com/RundesBalli/pr0gramm-apiCall
 * 
 * Beispielwert: /home/user/apiCall/apiCall.php
 * 
 * @param string
 */
require_once("");

/**
 * Das API Token welches man vom Telegram Bot "@BotFather" bei der Registrierung seines Bots bekommt.
 * 
 * Beispielwert: 000000000:AAAAAAAAAAAAAAAAAAAA-_0000000000
 * 
 * @var string
 */
$apiToken = "";

/**
 * Chat ID, an die der Bot senden soll.
 * Herausfinden kann man die Chat-ID zum Beispiel mit dem https://t.me/jsondumpbot
 * Zu finden ist die ID unter message->from->id
 * 
 * Beispielwert: 1234567890
 * kann auch mit negativem Vorzeichen sein!
 * 
 * @var string
 */
$chat_id = "";

/**
 * Der Useragent der an Telegram gesendet wird.
 * 
 * Beispielwert: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:66.0) Gecko/20100101 Firefox/66.0
 * oder          Heinrichs lustige Datenkrake
 * 
 * @var string
 */
$telegamUseragent = "";

/**
 * Alternativnutzer, an den die Nachricht geschickt wird.
 * 
 * Falls Telegram nicht erreichbar ist oder die Funktion fehlschlägt, dann wird an diesen User
 * die Nachricht weitergeleitet.
 * 
 * Beispielwert: RundesBalli
 * 
 * ACHTUNG: Darf nicht der selbe User wie im apiCall sein!
 * 
 * @var string
 */
$alternativeUser = "";
?>
