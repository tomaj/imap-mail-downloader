<?php

namespace Tomaj\ImapMailDownloader;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Tomaj\ImapMailDownloader\Email;
use Tomaj\ImapMailDownloader\MailCriteria;
use Tomaj\ImapMailDownloader\Downloader;

/*
 * IMAP mock functions
 */

function imap_open($inbox, $username, $password){
    return true;
}
function imap_close($mailbox){
    return true;
}

function imap_alerts(){
    return FALSE;
}

function imap_errors(){
    return FALSE;
}

function imap_getmailboxes($mailbox, $host, $folder){
    if (ImapFolderTest::$folderExists){
        return array(1);
    }
    return array();
}

function imap_search($mailbox, $searchString){
    return array(1);
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
    return ImapFolderTest::$folderExists;
}
function imap_createmailbox($mailbox, $folder){
    ImapFolderTest::$folderExists = true;
    ImapFolderTest::$folderWasCreated = true;
    return true;
}

class ImapFolderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Downloader
     */
    protected $downloader;

    /**
     * @var MailCriteria
     */
    protected $criteria;

    const FOLDER_NAME = "INBOX/processed";

    public static $folderExists;
    public static $folderWasCreated;

    protected function setUp(){
        $this->downloader = new Downloader('host',12,'username','password');
        $this->criteria = new MailCriteria();
    }

    public function testFolderExistsAlready(){
        self::$folderExists = true;
        self::$folderWasCreated = false;

        $this->downloader->fetch($this->criteria,function(Email $email){
            return true; // process
        },0);

        $this->assertFalse(self::$folderWasCreated);
    }

    /**
     * @throws \Exception
     * @expectedException Exception
     */
    public function testAutomakeFolderDisabled(){
        self::$folderExists = false;
        self::$folderWasCreated = false;

        $this->downloader->setProcessedFoldersAutomake(false);

        $this->downloader->fetch($this->criteria, function (Email $email) {
            return true; // process
        }, 0);

    }

    public function testAutomakeFolderEnabled(){
        self::$folderExists = false;
        self::$folderWasCreated = false;

        $this->downloader->setProcessedFoldersAutomake(true);
        $this->downloader->fetch($this->criteria, function (Email $email) {
            return true; // process
        }, 0);

        $this->assertTrue(self::$folderWasCreated);
    }
}
