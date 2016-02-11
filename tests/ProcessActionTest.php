<?php


//require_once dirname(__FILE__) . '/../vendor/autoload.php';
require_once dirname(__FILE__) . '/mockups/ImapMockup.php';

use Tomaj\ImapMailDownloader\Email;
use Tomaj\ImapMailDownloader\MailCriteria;
use Tomaj\ImapMailDownloader\Downloader;
use Tomaj\ImapMailDownloader\ProcessAction;
use Tomaj\ImapMailDownloader\ImapMockup;


class ImapMockForProcessActionTest extends \Tomaj\ImapMailDownloader\ImapMockup {

    public function imap_open($inbox, $username, $password){
        return 'mailbox-resource 1234';
    }


    public function imap_search($mailbox, $searchString){
        return array(1234567890);
    }

    public function imap_mail_move($mailbox, $emailIndex, $processedFolder){
        ProcessActionTest::$movedEmail = true;
        return true;
    }

    public function imap_delete($mailbox, $emailIndex){
        ProcessActionTest::$deletedEmail = true;
        return true;
    }
}

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


    protected function setUp(){
        $this->downloader = new Downloader('host',12,'username','password');
        $this->criteria = new MailCriteria();

        ImapMockup::setImplementation(new ImapMockForProcessActionTest());
    }

    protected function tearDown(){
        parent::tearDown();

        ImapMockup::setImplementation(null);
    }

    public function testNoAction(){

        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::delete();
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria,function(Email $email){
            return false;
        },0);

        $this->assertEquals(self::$movedEmail, false);
        $this->assertEquals(self::$deletedEmail, false);
        $this->assertEquals(self::$calledBack, false);
    }

    public function testDefaultDeleteAction(){

        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::delete();
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria,function(Email $email){
            return true;
        },0);

        $this->assertEquals(self::$movedEmail, false);
        $this->assertEquals(self::$deletedEmail, true);
        $this->assertEquals(self::$calledBack, false);
    }

    public function testDefaultMoveAction(){
        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::move('some/folder');
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria,function(Email $email){
            return true;
        },0);

        $this->assertEquals(self::$movedEmail, true);
        $this->assertEquals(self::$deletedEmail, false);
        $this->assertEquals(self::$calledBack, false);
    }

    public function testDefaultCallbackAction(){
        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::callback(function($mailbox, $emailIndex){
            self::$calledBack = ($mailbox == 'mailbox-resource 1234' && $emailIndex == 1234567890);
        });
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria,function(Email $email){
            return true;
        },0);

        $this->assertEquals(self::$movedEmail, false);
        $this->assertEquals(self::$deletedEmail, false);
        $this->assertEquals(self::$calledBack, true);
    }

    public function testEmailBasedOverrideByProcessActionTest(){

        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::delete();
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria,function(Email $email){
            return ProcessAction::move('some/folder');;
        },0);

        $this->assertEquals(self::$movedEmail, true);
        $this->assertEquals(self::$deletedEmail, false);
        $this->assertEquals(self::$calledBack, false);
    }

    public function testEmailBasedOverrideByCallableTest(){

        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::delete();
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria,function(Email $email){
            return function($mailbox, $emailIndex){
                self::$calledBack = ($mailbox == 'mailbox-resource 1234' && $emailIndex == 1234567890);
            };
        },0);

        $this->assertEquals(self::$movedEmail, false);
        $this->assertEquals(self::$deletedEmail, false);
        $this->assertEquals(self::$calledBack, true);
    }

    public function testEmailBasedOverrideByStringTest(){

        self::$movedEmail = false;
        self::$deletedEmail = false;
        self::$calledBack = false;

        // default action is deleting of messages
        $defaultProcessAction = ProcessAction::delete();
        $this->downloader->setDefaultProcessAction($defaultProcessAction);

        $this->downloader->fetch($this->criteria,function(Email $email){
            return ProcessAction::ACTION_MOVE;
        },0);

        $this->assertEquals(self::$movedEmail, true);
        $this->assertEquals(self::$deletedEmail, false);
        $this->assertEquals(self::$calledBack, false);
    }
}
