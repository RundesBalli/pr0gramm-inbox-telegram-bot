# pr0gramm-telegram-inbox-bot
Bot zum Auslesen der Inbox auf https://pr0gramm.com

## Abhängigkeiten
Damit der Bot funktioniert muss der [pr0gramm-apiCall](https://github.com/RundesBalli/pr0gramm-apiCall) eingebunden werden.  
Näheres dazu in der Konfigurationsdatei.

## Einrichtung
Zu erst muss die `config.template.php` in `config.php` kopiert oder umbenannt werden.  
Die darin erforderlichen Konfigurationsanpassungen sind ausführlich in den Kommentaren beschrieben.

## Anwendung
Damit der Bot minütlich die Inbox abfragt muss er in `crontab -e` eingetragen werden:  
`* * * * * php /pfad/zum/bot.php`
