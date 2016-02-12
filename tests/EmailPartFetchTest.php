<?php

namespace Tomaj\ImapMailDownloader;

require_once dirname(__FILE__) . '/mockups/ImapMockupForEmailPartFetchTest.php';

class EmailPartFetchTest extends \PHPUnit_Framework_TestCase
{
    protected $downloader;
    protected $criteria;

    protected function setUp()
    {
        $this->downloader = new Downloader('host', 12, 'username', 'password');
        $this->criteria = new MailCriteria();
        ImapMockup::setImplementation(new ImapMockupForEmailPartFetchTest());
    }

    protected function tearDown()
    {
        parent::tearDown();
        ImapMockup::setImplementation(null);
    }

    protected function checkOverview(Email $email, $expectIsset)
    {
        if ($expectIsset) {
            $this->assertEquals($email->getFrom(), 'from@asdsad.sk');
            $this->assertEquals($email->getTo(), 'asdsad@adsad.sk');
            $this->assertEquals($email->getDate(), '2014-01-02 14:34');
            $this->assertEquals($email->getMessageId(), 'sa09uywqet09u3t');
            $this->assertEquals($email->getReferences(), 'asdas09uyfei9f');
            $this->assertEquals($email->getInReplyTo(), '135325325325');
            $this->assertEquals($email->getSize(), 125);
            $this->assertEquals($email->getUid(), '236-0982369034856');
            $this->assertEquals($email->getMsgNo(), 4125);
            $this->assertEquals($email->getRecent(), 1);
            $this->assertEquals($email->getFlagged(), 0);
            $this->assertEquals($email->getAnswered(), 1);
            $this->assertEquals($email->getDeleted(), 0);
            $this->assertEquals($email->getSeen(), 1);
            $this->assertEquals($email->getDraft(), 1);
        } else {
            $this->assertEquals($email->getFrom(), null);
            $this->assertEquals($email->getTo(), null);
            $this->assertEquals($email->getDate(), null);
            $this->assertEquals($email->getMessageId(), null);
            $this->assertEquals($email->getReferences(), null);
            $this->assertEquals($email->getInReplyTo(), null);
            $this->assertEquals($email->getSize(), null);
            $this->assertEquals($email->getUid(), null);
            $this->assertEquals($email->getMsgNo(), null);
            $this->assertEquals($email->getRecent(), null);
            $this->assertEquals($email->getFlagged(), null);
            $this->assertEquals($email->getAnswered(), null);
            $this->assertEquals($email->getDeleted(), null);
            $this->assertEquals($email->getSeen(), null);
            $this->assertEquals($email->getDraft(), null);
        }
    }

    protected function checkBody(Email $email, $expectIsset)
    {
        if ($expectIsset) {
            $this->assertEquals($email->getBody(), 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg');
        } else {
            $this->assertEquals($email->getBody(), null);
        }
    }

    protected function checkHeader(Email $email, $expectIsset)
    {
        if ($expectIsset) {
            $this->assertEquals($email->getHeaders(), '1234567890 8yc81bch2zzxkjtyp8eraqziaou');
        } else {
            $this->assertEquals($email->getHeaders(), null);
        }
    }

    public function testFetchNoParts()
    {
        $this->downloader->fetch($this->criteria, function (Email $email) {
            $this->checkOverview($email, false);
            $this->checkBody($email, false);
            $this->checkHeader($email, false);
        }, 0);
    }

    public function testFetchOverviewOnly()
    {
        $this->downloader->fetch($this->criteria, function (Email $email) {
            $this->checkOverview($email, true);
            $this->checkBody($email, false);
            $this->checkHeader($email, false);
        }, Downloader::FETCH_OVERVIEW);
    }

    public function testFetchHeaderOnly()
    {
        $this->downloader->fetch($this->criteria, function (Email $email) {
            $this->checkOverview($email, false);
            $this->checkBody($email, false);
            $this->checkHeader($email, true);
        }, Downloader::FETCH_HEADERS);
    }

    public function testFetchBodyOnly()
    {
        $this->downloader->fetch($this->criteria, function (Email $email) {
            $this->checkOverview($email, false);
            $this->checkBody($email, true);
            $this->checkHeader($email, false);
        }, Downloader::FETCH_BODY);
    }

    public function testFetchAll()
    {
        $this->downloader->fetch($this->criteria, function (Email $email) {
            $this->checkOverview($email, true);
            $this->checkBody($email, true);
            $this->checkHeader($email, true);
        }, Downloader::FETCH_OVERVIEW | Downloader::FETCH_HEADERS | Downloader::FETCH_BODY);
    }
}
