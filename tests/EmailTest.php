<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Tomaj\ImapMailDownloader\Email;

class EmailTest extends PHPUnit_Framework_TestCase
{
	public function testCreationWithouOptionalAttributes()
	{
		$data = new stdClass;
		$data->from = 'from@asdsad.sk';
		$data->to = 'asdsad@adsad.sk';
		$data->date = '2014-01-02 14:34';
		$data->message_id = 'sa09uywqet09u3t';
		$data->size = 125;
		$data->uid = '236-0982369034856';
		$data->msgno = 4125;
		$data->recent = 1;
		$data->flagged = 0;
		$data->answered = 1;
		$data->deleted = 0;
		$data->seen = 1;
		$data->draft = 1;
        $data->text = "text";

		$body = 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg';

		$email = new Email(array($data), $body);

		$this->assertEquals($email->getFrom(), 'from@asdsad.sk');
		$this->assertEquals($email->getTo(), 'asdsad@adsad.sk');
		$this->assertEquals($email->getDate(), '2014-01-02 14:34');
		$this->assertEquals($email->getMessageId(), 'sa09uywqet09u3t');
		$this->assertEquals($email->getReferences(), null);
		$this->assertEquals($email->getInReplyTo(), null);
		$this->assertEquals($email->getSize(), 125);
		$this->assertEquals($email->getUid(), '236-0982369034856');
		$this->assertEquals($email->getMsgNo(), 4125);
		$this->assertEquals($email->getRecent(), 1);
		$this->assertEquals($email->getFlagged(), 0);
		$this->assertEquals($email->getAnswered(), 1);
		$this->assertEquals($email->getDeleted(), 0);
		$this->assertEquals($email->getSeen(), 1);
		$this->assertEquals($email->getDraft(), 1);

		$this->assertEquals($email->getBody(), 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg');
	}

	public function testCreationWithAllAttributes()
	{
		$data = new stdClass;
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

		$body = 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg';

		$email = new Email(array($data), $body);

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

		$this->assertEquals($email->getBody(), 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg');
	}
}
