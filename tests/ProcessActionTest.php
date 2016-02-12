<?php

namespace Tomaj\ImapMailDownloader;

require_once dirname(__FILE__) . '/mockups/ImapMockupForProcessActionTest.php';

class ProcessActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Downloader
     */
    protected $downloader;

    /**
     * @var MailCriteria
     */
    protected $criteria;

    public static $movedEmail;
    public static $deletedEmail;
    public static $calledBack;


    protected function setUp()
    {
        $this->downloader = new Downloader('host', 12, 'username', 'password');
        $this->criteria = new MailCriteria();

        ImapMockup::setImplementation(new ImapMockupForProcessActionTest());
    }

    protected function tearDown()
    {
        parent::tearDown();

        ImapMockup::setImplementation(null);
    }

    public function testNoAction()
    {

        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::delete();
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria, function (Email $email) {
            return false;
        }, 0);

        $this->assertEquals(self::$movedEmail, false);
        $this->assertEquals(self::$deletedEmail, false);
        $this->assertEquals(self::$calledBack, false);
    }

    public function testDefaultDeleteAction()
    {

        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::delete();
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria, function (Email $email) {
            return true;
        }, 0);

        $this->assertEquals(self::$movedEmail, false);
        $this->assertEquals(self::$deletedEmail, true);
        $this->assertEquals(self::$calledBack, false);
    }

    public function testDefaultMoveAction()
    {
        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::move('some/folder');
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria, function (Email $email) {
            return true;
        }, 0);

        $this->assertEquals(self::$movedEmail, true);
        $this->assertEquals(self::$deletedEmail, false);
        $this->assertEquals(self::$calledBack, false);
    }

    public function testDefaultCallbackAction()
    {
        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::callback(function ($mailbox, $emailIndex) {
            ProcessActionTest::$calledBack = ($mailbox == 'mailbox-resource 1234' && $emailIndex == 1234567890);
        });
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria, function (Email $email) {
            return true;
        }, 0);

        $this->assertEquals(self::$movedEmail, false);
        $this->assertEquals(self::$deletedEmail, false);
        $this->assertEquals(self::$calledBack, true);
    }

    public function testEmailBasedOverrideByProcessActionTest()
    {

        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::delete();
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria, function (Email $email) {
            return ProcessAction::move('some/folder');
        }, 0);

        $this->assertEquals(self::$movedEmail, true);
        $this->assertEquals(self::$deletedEmail, false);
        $this->assertEquals(self::$calledBack, false);
    }

    public function testEmailBasedOverrideByCallableTest()
    {

        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::delete();
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria, function (Email $email) {
            return function ($mailbox, $emailIndex) {
                ProcessActionTest::$calledBack = ($mailbox == 'mailbox-resource 1234' && $emailIndex == 1234567890);
            };
        }, 0);

        $this->assertEquals(self::$movedEmail, false);
        $this->assertEquals(self::$deletedEmail, false);
        $this->assertEquals(self::$calledBack, true);
    }

    public function testEmailBasedOverrideByStringTest()
    {

        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::delete();
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria, function (Email $email) {
            return ProcessAction::ACTION_MOVE;
        }, 0);

        $this->assertEquals(self::$movedEmail, true);
        $this->assertEquals(self::$deletedEmail, false);
        $this->assertEquals(self::$calledBack, false);
    }
}
