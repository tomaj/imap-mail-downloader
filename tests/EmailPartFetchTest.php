<?php

namespace Tomaj\ImapMailDownloader;

require_once dirname(__FILE__) . '/mockups/ImapMockupForEmailPartFetchTest.php';

class EmailPartFetchTest extends \PHPUnit_Framework_TestCase
{
    protected static $instance;

    protected $downloader;
    protected $criteria;

    protected function setUp()
    {
        $this->downloader = new Downloader('host', 12, 'username', 'password');
        $this->criteria = new MailCriteria();
        ImapMockup::setImplementation(new ImapMockupForEmailPartFetchTest());

        EmailPartFetchTest::$instance = $this;
    }

    protected function tearDown()
    {
        parent::tearDown();
        ImapMockup::setImplementation(null);
    }

    protected static function checkOverview(Email $email, $expectIsset)
    {
        if ($expectIsset) {
            EmailPartFetchTest::$instance->assertEquals($email->getFrom(), 'from@asdsad.sk');
            EmailPartFetchTest::$instance->assertEquals($email->getTo(), 'asdsad@adsad.sk');
            EmailPartFetchTest::$instance->assertEquals($email->getDate(), '2014-01-02 14:34');
            EmailPartFetchTest::$instance->assertEquals($email->getMessageId(), 'sa09uywqet09u3t');
            EmailPartFetchTest::$instance->assertEquals($email->getReferences(), 'asdas09uyfei9f');
            EmailPartFetchTest::$instance->assertEquals($email->getInReplyTo(), '135325325325');
            EmailPartFetchTest::$instance->assertEquals($email->getSize(), 125);
            EmailPartFetchTest::$instance->assertEquals($email->getUid(), '236-0982369034856');
            EmailPartFetchTest::$instance->assertEquals($email->getMsgNo(), 4125);
            EmailPartFetchTest::$instance->assertEquals($email->getRecent(), 1);
            EmailPartFetchTest::$instance->assertEquals($email->getFlagged(), 0);
            EmailPartFetchTest::$instance->assertEquals($email->getAnswered(), 1);
            EmailPartFetchTest::$instance->assertEquals($email->getDeleted(), 0);
            EmailPartFetchTest::$instance->assertEquals($email->getSeen(), 1);
            EmailPartFetchTest::$instance->assertEquals($email->getDraft(), 1);
        } else {
            EmailPartFetchTest::$instance->assertEquals($email->getFrom(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getTo(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getDate(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getMessageId(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getReferences(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getInReplyTo(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getSize(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getUid(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getMsgNo(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getRecent(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getFlagged(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getAnswered(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getDeleted(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getSeen(), null);
            EmailPartFetchTest::$instance->assertEquals($email->getDraft(), null);
        }
    }

    protected static function checkBody(Email $email, $expectIsset)
    {
        if ($expectIsset) {
            EmailPartFetchTest::$instance->assertEquals($email->getBody(), 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg');
        } else {
            EmailPartFetchTest::$instance->assertEquals($email->getBody(), null);
        }
    }

    protected function checkHeader(Email $email, $expectIsset)
    {
        if ($expectIsset) {
            EmailPartFetchTest::$instance->assertEquals($email->getHeaders(), '1234567890 8yc81bch2zzxkjtyp8eraqziaou');
        } else {
            EmailPartFetchTest::$instance->assertEquals($email->getHeaders(), null);
        }
    }

    public function testFetchNoParts()
    {
        $this->downloader->fetch($this->criteria, function (Email $email) {
            EmailPartFetchTest::checkOverview($email, false);
            EmailPartFetchTest::checkBody($email, false);
            EmailPartFetchTest::checkHeader($email, false);
        }, 0);
    }

    public function testFetchOverviewOnly()
    {
        $this->downloader->fetch($this->criteria, function (Email $email) {
            EmailPartFetchTest::checkOverview($email, true);
            EmailPartFetchTest::checkBody($email, false);
            EmailPartFetchTest::checkHeader($email, false);
        }, Downloader::FETCH_OVERVIEW);
    }

    public function testFetchHeaderOnly()
    {
        $this->downloader->fetch($this->criteria, function (Email $email) {
            EmailPartFetchTest::checkOverview($email, false);
            EmailPartFetchTest::checkBody($email, false);
            EmailPartFetchTest::checkHeader($email, true);
        }, Downloader::FETCH_HEADERS);
    }

    public function testFetchBodyOnly()
    {
        $this->downloader->fetch($this->criteria, function (Email $email) {
            EmailPartFetchTest::checkOverview($email, false);
            EmailPartFetchTest::checkBody($email, true);
            EmailPartFetchTest::checkHeader($email, false);
        }, Downloader::FETCH_BODY);
    }

    public function testFetchAll()
    {
        $this->downloader->fetch($this->criteria, function (Email $email) {
            EmailPartFetchTest::checkOverview($email, true);
            EmailPartFetchTest::checkBody($email, true);
            EmailPartFetchTest::checkHeader($email, true);
        }, Downloader::FETCH_OVERVIEW | Downloader::FETCH_HEADERS | Downloader::FETCH_BODY);
    }
}
