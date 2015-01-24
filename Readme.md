IMAP MAIL Downloader
====================

Library for fetching inbox mails and processing them.

[![Build Status](https://secure.travis-ci.org/tomaj/imap-mail-downloader.png)](http://travis-ci.org/tomaj/imap-mail-downloader)
[![Coverage Status](https://coveralls.io/repos/tomaj/imap-mail-downloader/badge.png?branch=master)](https://coveralls.io/r/tomaj/imap-mail-downloader?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/54c400a90a18c30671000006/badge.svg?style=flat)](https://www.versioneye.com/user/projects/54c400a90a18c30671000006)

[![Latest Stable Version](https://poser.pugx.org/tomaj/imap-mail-downloader/v/stable.svg)](https://packagist.org/packages/tomaj/imap-mail-downloader)
[![Latest Unstable Version](https://poser.pugx.org/tomaj/imap-mail-downloader/v/unstable.svg)](https://packagist.org/packages/tomaj/imap-mail-downloader)
[![License](https://poser.pugx.org/tomaj/imap-mail-downloader/license.svg)](https://packagist.org/packages/tomaj/imap-mail-downloader)


Instalation
-----------

Install package via composer:

``` bash
$ composer require tomaj/imap-mail-downloader
```

Usage
-----

Basic usage in php:

``` php
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
