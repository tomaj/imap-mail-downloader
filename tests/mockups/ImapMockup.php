<?php

namespace Tomaj\ImapMailDownloader;

/**
 * Class ImapMockup
 * Serves as Mockup of imap function for unit testing
 * @package Tomaj\ImapMailDownloader
 */
class ImapMockup
{

    /**
     * @var ImapMockup
     */
    public static $implementation;

    /** Set currently active ImapMockup implementation
     * @param ImapMockup $implementation
     */
    public static function setImplementation($implementation)
    {
        self::$implementation = $implementation;
    }

    /**
     * @param $inbox
     * @param $username
     * @param $password
     * @return bool
     * @see imap_open()
     */
    public function imapOpen($inbox, $username, $password)
    {
        return true;
    }

    /**
     * @param $mailbox
     * @return bool
     * @see imap_close()
     */
    public function imapClose($mailbox)
    {
        return true;
    }

    /**
     * @return bool
     * @see imap_alerts()
     */
    public function imapAlerts()
    {
        return false;
    }

    /**
     * @return bool
     * @see imap_errors()
     */
    public function imapErrors()
    {
        return false;
    }

    /**
     * @param $mailbox
     * @param $host
     * @param $folder
     * @return array
     * @see imap_getmailboxes()
     */
    public function imapGetmailboxes($mailbox, $host, $folder)
    {
        return array(1);
    }

    /**
     * @param $mailbox
     * @param $folder
     * @return bool
     * @see imap_createmailbox()
     */
    public function imapCreatemailbox($mailbox, $folder)
    {
        return true;
    }

    /**
     * @param $mailbox
     * @param $searchString
     * @return array
     * @see imap_search()
     */
    public function imapSearch($mailbox, $searchString)
    {
        return array(1234567890);
    }

    /**
     * @param $mailbox
     * @param $emailIndex
     * @param $options
     * @return array
     * @see imap_fetch_overview()
     */
    public function imapFetchOverview($mailbox, $emailIndex, $options)
    {

        $data = new \stdClass;
        $data->from = 'from@asdsad.sk';
        $data->to = 'asdsad@adsad.sk';
        $data->date = '2014-01-02 14:34';
        $data->message_id = 'sa09uywqet09u3t';
        $data->references = 'asdas09uyfei9f';
        $data->in_reply_to = '135325325325';
        $data->size = 125;
        $data->uid = '236-0982369034856';
        $data->msgno = 4125;
        $data->recent = 1;
        $data->flagged = 0;
        $data->answered = 1;
        $data->deleted = 0;
        $data->seen = 1;
        $data->draft = 1;

        return array($data);
    }

    /**
     * @param $mailbox
     * @param $emailIndex
     * @param $options
     * @return string
     * @see imap_fetchheader()
     */
    public function imapFetchheader($mailbox, $emailIndex, $options)
    {
        return '1234567890 8yc81bch2zzxkjtyp8eraqziaou';
    }

    /**
     * @param $mailbox
     * @param $emailIndex
     * @return string
     * @see imap_body()
     */
    public function imapBody($mailbox, $emailIndex)
    {
        return 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg';
    }

    /**
     * @param $mailbox
     * @param $emailIndex
     * @param $processedFolder
     * @return bool
     * @see imap_mail_move()
     */
    public function imapMailMove($mailbox, $emailIndex, $processedFolder)
    {
        return true;
    }

    /**
     * @param $mailbox
     * @param $emailIndex
     * @return bool
     * @see imap_delete()
     */
    public function imapDelete($mailbox, $emailIndex)
    {
        return true;
    }
}


/**
 * Function call override and forwarder
 * @param $inbox
 * @param $username
 * @param $password
 * @return bool
 * @see imap_open()
 */
function imap_open($inbox, $username, $password)
{
    return ImapMockup::$implementation->imapOpen($inbox, $username, $password);
}

/**
 * Function call override and forwarder
 * @param $mailbox
 * @return bool
 * @see imap_close()
 */
function imap_close($mailbox)
{
    return ImapMockup::$implementation->imapClose($mailbox);
}

/**
 * Function call override and forwarder
 * @return bool
 * @see imap_alerts()
 */
function imap_alerts()
{
    return ImapMockup::$implementation->imapAlerts();
}

/**
 * Function call override and forwarder
 * @return bool
 * @see imap_errors()
 */
function imap_errors()
{
    return ImapMockup::$implementation->imapErrors();
}

/**
 * Function call override and forwarder
 * @param $mailbox
 * @param $host
 * @param $folder
 * @return array
 * @see imap_getmailboxes()
 */
function imap_getmailboxes($mailbox, $host, $folder)
{
    return ImapMockup::$implementation->imapGetmailboxes($mailbox, $host, $folder);
}

/**
 * Function call override and forwarder
 * @param $mailbox
 * @param $folder
 * @return bool
 * @see imap_createmailbox()
 */
function imap_createmailbox($mailbox, $folder)
{
    return ImapMockup::$implementation->imapCreatemailbox($mailbox, $folder);
}

/**
 * Function call override and forwarder
 * @param $mailbox
 * @param $searchString
 * @return array
 * @see imap_search()
 */
function imap_search($mailbox, $searchString)
{
    return ImapMockup::$implementation->imapSearch($mailbox, $searchString);
}

/**
 * Function call override and forwarder
 * @param $mailbox
 * @param $emailIndex
 * @param $options
 * @return array
 * @see imap_fetch_overview()
 */
function imap_fetch_overview($mailbox, $emailIndex, $options)
{
    return ImapMockup::$implementation->imapFetchOverview($mailbox, $emailIndex, $options);
}

/**
 * Function call override and forwarder
 * @param $mailbox
 * @param $emailIndex
 * @param $options
 * @return string
 * @see imap_fetchheader()
 */
function imap_fetchheader($mailbox, $emailIndex, $options)
{
    return ImapMockup::$implementation->imapFetchheader($mailbox, $emailIndex, $options);
}

/**
 * Function call override and forwarder
 * @param $mailbox
 * @param $emailIndex
 * @return string
 * @see imap_body()
 */
function imap_body($mailbox, $emailIndex)
{
    return ImapMockup::$implementation->imapBody($mailbox, $emailIndex);
}

/**
 * Function call override and forwarder
 * @param $mailbox
 * @param $emailIndex
 * @param $processedFolder
 * @return bool
 * @see imap_mail_move()
 */
function imap_mail_move($mailbox, $emailIndex, $processedFolder)
{
    return ImapMockup::$implementation->imapMailMove($mailbox, $emailIndex, $processedFolder);
}

/**
 * Function call override and forwarder
 * @param $mailbox
 * @param $emailIndex
 * @return bool
 * @see imap_delete()
 */
function imap_delete($mailbox, $emailIndex)
{
    return ImapMockup::$implementation->imapDelete($mailbox, $emailIndex);
}
