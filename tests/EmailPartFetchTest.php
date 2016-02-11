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
    return array(1);
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
    return true;
}



class EmailPartFetchTest extends \PHPUnit_Framework_TestCase
{
    protected $downloader;
    protected $criteria;


    protected function setUp(){
        $this->downloader = new Downloader('host',12,'username','password');
        $this->criteria = new MailCriteria();
    }

    protected function checkOverview(Email $email, $expectIsset){
        if ($expectIsset){
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
            $this->assertEquals($email->getFrom(), NULL);
            $this->assertEquals($email->getTo(), NULL);
            $this->assertEquals($email->getDate(), NULL);
            $this->assertEquals($email->getMessageId(), NULL);
            $this->assertEquals($email->getReferences(), NULL);
            $this->assertEquals($email->getInReplyTo(), NULL);
            $this->assertEquals($email->getSize(), NULL);
            $this->assertEquals($email->getUid(), NULL);
            $this->assertEquals($email->getMsgNo(), NULL);
            $this->assertEquals($email->getRecent(), NULL);
            $this->assertEquals($email->getFlagged(), NULL);
            $this->assertEquals($email->getAnswered(), NULL);
            $this->assertEquals($email->getDeleted(), NULL);
            $this->assertEquals($email->getSeen(), NULL);
            $this->assertEquals($email->getDraft(), NULL);
        }
    }

    protected function checkBody(Email $email, $expectIsset){
        if ($expectIsset){
            $this->assertEquals($email->getBody(), 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg');
        } else {
            $this->assertEquals($email->getBody(), NULL);
        }
    }

    protected function checkHeader(Email $email, $expectIsset){
        if ($expectIsset){
            $this->assertEquals($email->getHeaders(), '1234567890 8yc81bch2zzxkjtyp8eraqziaou');
        } else {
            $this->assertEquals($email->getHeaders(), NULL);
        }
    }

    public function testFetchNoParts(){
        $this->downloader->fetch($this->criteria,function(Email $email){
            $this->checkOverview($email,false);
            $this->checkBody($email,false);
            $this->checkHeader($email,false);
        },0);
    }

    public function testFetchOverviewOnly(){
        $this->downloader->fetch($this->criteria,function(Email $email){
            $this->checkOverview($email,true);
            $this->checkBody($email,false);
            $this->checkHeader($email,false);
        },Downloader::FETCH_OVERVIEW);
    }

    public function testFetchHeaderOnly(){
        $this->downloader->fetch($this->criteria,function(Email $email){
            $this->checkOverview($email,false);
            $this->checkBody($email,false);
            $this->checkHeader($email,true);
        },Downloader::FETCH_HEADERS);
    }

    public function testFetchBodyOnly(){
        $this->downloader->fetch($this->criteria,function(Email $email){
            $this->checkOverview($email,false);
            $this->checkBody($email,true);
            $this->checkHeader($email,false);
        },Downloader::FETCH_BODY);
    }

    public function testFetchAll(){
        $this->downloader->fetch($this->criteria,function(Email $email){
            $this->checkOverview($email,true);
            $this->checkBody($email,true);
            $this->checkHeader($email,true);
        },Downloader::FETCH_OVERVIEW | Downloader::FETCH_HEADERS | Downloader::FETCH_BODY);
    }
}
