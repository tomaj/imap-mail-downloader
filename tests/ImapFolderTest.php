<?php

//require_once dirname(__FILE__) . '/../vendor/autoload.php';
require_once dirname(__FILE__) . '/mockups/ImapMockup.php';

use Tomaj\ImapMailDownloader\Email;
use Tomaj\ImapMailDownloader\ImapMockup;
use Tomaj\ImapMailDownloader\MailCriteria;
use Tomaj\ImapMailDownloader\Downloader;

/*
 * IMAP mock functions
 */

class ImapMockupForImapFolderTest extends ImapMockup {
    public function imap_getmailboxes($mailbox, $host, $folder){
        if (ImapFolderTest::$folderExists){
            return array(1);
        }
        return array();
    }

    public function imap_mail_move($mailbox, $emailIndex, $processedFolder){
        return ImapFolderTest::$folderExists;
    }
    public function imap_createmailbox($mailbox, $folder){
        ImapFolderTest::$folderExists = true;
        ImapFolderTest::$folderWasCreated = true;
        return true;
    }
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
        ImapMockup::setImplementation(new ImapMockupForImapFolderTest());
    }

//    protected function tearDown(){
//        parent::tearDown();
//        ImapMockup::setImplementation(null);
//    }

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
