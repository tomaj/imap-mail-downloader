IMAP MAIL Downloader
====================

Library for fetching inbox mails and processing them.


Instalation
-----------

Install package via composer:

```
$ composer require tomaj/imap-mail-downloader
```

Usage
-----

Basic usage in php:

```
use Tomaj\ImapMailDownloader\Downloader;
use Tomaj\ImapMailDownloader\MailCriteria;
use Tomaj\ImapMailDownloader\Email;

$downloader = new Downloader('*imap host*', *port*, '*username*', '*password*');

$criteria = new MailCriteria();
$criteria->setFrom('some@email.com');
$downloader->fetch($criteria, function(Email $email) {
	print_r($email);
	return true;
});
```

You can return false in callback function. In this case this email will be fetched also in next time. For processing emails you will need to create folder **INBOX/processed**.
There is possiblity to setup criteria for fetching emails with *MailCriteria*. More information in source code.

Library is extremelly simple. Usefull for processing some notification emails. For complex usecases you will need to use native php *imap_* functions.