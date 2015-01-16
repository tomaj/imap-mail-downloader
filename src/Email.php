<?php

namespace Tomaj\ImapMailDownloader;

class Email
{
	private $from;

	private $to;

	private $date;

	private $messageId;

	private $references;

	private $inReplyTo;

	private $size;

	private $uid;

	private $msgNo;

	private $recent;

	private $flagged;

	private $answered;

	private $deleted;

	private $seen;

	private $draft;

	private $body;

	public function __construct($params, $body)
	{
		$options = $params[0];

		$this->from = $options->from;
		$this->to = $options->to;
		$this->date = $options->date;
		$this->messageId = $options->message_id;
		if (isset($options->references)) {
			$this->references = $options->references;
		}
		if (isset($options->in_reply_to)) {
			$this->inReplyTo = $options->in_reply_to;
		}
		$this->size = $options->size;
		$this->uid = $options->uid;
		$this->msgNo = $options->msgno;
		$this->recent = $options->recent;
		$this->flagged = $options->flagged;
		$this->answered = $options->answered;
		$this->deleted = $options->deleted;
		$this->seen = $options->seen;
		$this->draft = $options->draft;

		$this->body = $body;
	}

}