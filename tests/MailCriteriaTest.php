<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Tomaj\ImapMailDownloader\MailCriteria;

class MailCriteriaTest extends PHPUnit_Framework_TestCase
{
	public function testDefault()
	{
		$criteria = new MailCriteria();
		$this->assertEquals('ALL', $criteria->getSearchString());
	}

	public function testFrom()
	{
		$criteria = new MailCriteria();
		$criteria->setFrom('a@a.sk');
		$this->assertEquals('FROM "a@a.sk"', $criteria->getSearchString());
	}

    public function testText()
    {
        $criteria = new MailCriteria();
        $criteria->setText("test text");
        $this->assertEquals('TEXT "test text"', $criteria->getSearchString());
    }

	public function testAllCriteria()
	{
		$criteria = new MailCriteria();
		$criteria->setFrom('b@b.sk');
		$criteria->setKeyword('testKeyword');
		$criteria->setUnseen(true);
		$criteria->setSince('2014-01-05 14:56:11');
		$criteria->setSubject('my Subject');

		$this->assertEquals('FROM "b@b.sk" KEYWORD "testKeyword" UNSEEN SUBJECT "my Subject" SINCE "2014-01-05 14:56:11"', $criteria->getSearchString());
	}
}
