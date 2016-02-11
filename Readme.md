IMAP MAIL Downloader
====================

Library for fetching inbox mails and processing them.

[![Build Status](https://secure.travis-ci.org/tomaj/imap-mail-downloader.png)](http://travis-ci.org/tomaj/imap-mail-downloader)
[![Code Climate](https://codeclimate.com/github/tomaj/imap-mail-downloader/badges/gpa.svg)](https://codeclimate.com/github/tomaj/imap-mail-downloader)
[![Test Coverage](https://codeclimate.com/github/tomaj/imap-mail-downloader/badges/coverage.svg)](https://codeclimate.com/github/tomaj/imap-mail-downloader/coverage)
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

Some extended features:

``` php

// define what you consider your inbox folder..
$downloader->setInboxFolder('INDBOX');

// if processed folder does not exist, automatically try to create it
$downloader->setProcessedFoldersAutomake(true);


//// define what data to get for email
//  FETCH_OVERVIEW      just basic header information (see imap_fetchoverview)
//  FETCH_BODY          email body as string
//  FETCH_HEADERS       complete email headers as string

$fetchParts = Downloader::FETCH_OVERVIEW | Downloader::FETCH_BODY | Downloader::FETCH_HEADERS;
$downloader->fetch($criteria, function(Email $email) {

    // FETCH_OVERVIEW
    print_r($email->getFrom());
    print_r($email->getSubject());
    ...

    print_r($email->getBody()); // FETCH_BODY

	print_r($email->getHeaders()); // FETCH_HEADERS

	print_r($email->getSource()); // FETCH_BODY | FETCH_HEADERS
});



//// use more custom processing

/// set default action
//      ProcessAction::move('your-folder');
//      ProcessAction::delete();
//      ProcessAction::callback(function($mailbox, $emailIndex){ ... });

$defaultProcessAction = ProcessAction::move('INBOX/processed');
$downloader->setDefaultProcessAction($defaultProcessAction);

// override default process action based on email contents
$downloader->fetch($criteria, function(Email $email) {
    ...
    return FALSE; // do not process

    return TRUE; // perform default process action

    return new ProcessAction::delete(); // override default process action based on email
    
    return new ProcessAction::callback(function($mailbox, $emailIndex){
        //do your own customized processing
        imap_delete($mailbox, $emailIndex);
        ...
    });
});

```

Library is extremelly simple. Usefull for processing some notification emails. For complex usecases you will need to use native php *imap_* functions.
