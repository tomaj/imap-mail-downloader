<?php
namespace Tomaj\ImapMailDownloader;

ini_set('error_reporting',E_ALL | E_STRICT);
ini_set('display_errors','On');

//require_once dirname(__FILE__) . '/../vendor/autoload.php';



class ImapMockup
{

    public static $implementation;

    public static function setImplementation($implementation){
        self::$implementation = $implementation;
    }


    public function imap_open($inbox, $username, $password){
        return true;
    }
    public function imap_close($mailbox){
        return true;
    }
    function imap_alerts(){
        return FALSE;
    }
    public function imap_errors(){
        return FALSE;
    }

    public function imap_getmailboxes($mailbox, $host, $folder){
        return array(1);
    }

    public function imap_createmailbox($mailbox, $folder){
        return true;
    }

    function imap_search($mailbox, $searchString){
        return array(1234567890);
    }

    function imap_fetch_overview($mailbox, $emailIndex, $options){

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

    function imap_fetchheader($mailbox, $emailIndex, $options){
        return '1234567890 8yc81bch2zzxkjtyp8eraqziaou';
    }

    function imap_body($mailbox, $emailIndex){
        return 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg';
    }

    function imap_mail_move($mailbox, $emailIndex, $processedFolder){
        return true;
    }

    function imap_delete($mailbox, $emailIndex){
        return true;
    }
}


function imap_open($inbox, $username, $password){
    return ImapMockup::$implementation->imap_open($inbox, $username, $password);
}
function imap_close($mailbox){
    return ImapMockup::$implementation->imap_close($mailbox);
}

function imap_alerts(){
    return ImapMockup::$implementation->imap_alerts();
}

function imap_errors(){
    return ImapMockup::$implementation->imap_errors();
}

function imap_getmailboxes($mailbox, $host, $folder){
    return ImapMockup::$implementation->imap_getmailboxes($mailbox, $host, $folder);
}

function imap_createmailbox($mailbox, $folder){
    return ImapMockup::$implementation->imap_createmailbox($mailbox, $folder);
}

function imap_search($mailbox, $searchString){
    return ImapMockup::$implementation->imap_search($mailbox, $searchString);
}

function imap_fetch_overview($mailbox, $emailIndex, $options){
    return ImapMockup::$implementation->imap_fetch_overview($mailbox, $emailIndex, $options);
}

function imap_fetchheader($mailbox, $emailIndex, $options){
    return ImapMockup::$implementation->imap_fetchheader($mailbox, $emailIndex, $options);
}

function imap_body($mailbox, $emailIndex){
    return ImapMockup::$implementation->imap_body($mailbox, $emailIndex);
}

function imap_mail_move($mailbox, $emailIndex, $processedFolder){
    return ImapMockup::$implementation->imap_mail_move($mailbox, $emailIndex, $processedFolder);
}

function imap_delete($mailbox, $emailIndex){
    return ImapMockup::$implementation->imap_delete($mailbox, $emailIndex);
}

