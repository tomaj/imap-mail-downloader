<?php

namespace Tomaj\ImapMailDownloader;

require_once dirname(__FILE__) . '/mockups/ImapMockupForImapFolderTest.php';

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

    protected function setUp()
    {
        $this->downloader = new Downloader('host', 12, 'username', 'password');
        $this->criteria = new MailCriteria();
        ImapMockup::setImplementation(new ImapMockupForImapFolderTest());
    }

    protected function tearDown()
    {
        parent::tearDown();
        ImapMockup::setImplementation(null);
    }

    public function testFolderExistsAlready()
    {
        self::$folderExists = true;
        self::$folderWasCreated = false;

        $this->downloader->fetch($this->criteria, function (Email $email) {
            return true; // process
        }, 0);

        $this->assertFalse(self::$folderWasCreated);
    }

    /**
     * @throws \Exception
     * @expectedException Exception
     */
    public function testAutomakeFolderDisabled()
    {
        self::$folderExists = false;
        self::$folderWasCreated = false;

        $this->downloader->setProcessedFoldersAutomake(false);

        $this->downloader->fetch($this->criteria, function (Email $email) {
            return true; // process
        }, 0);

    }

    public function testAutomakeFolderEnabled()
    {
        self::$folderExists = false;
        self::$folderWasCreated = false;

        $this->downloader->setProcessedFoldersAutomake(true);
        $this->downloader->fetch($this->criteria, function (Email $email) {
            return true; // process
        }, 0);

        $this->assertTrue(self::$folderWasCreated);
    }
}
