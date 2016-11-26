<?php

namespace Tomaj\ImapMailDownloader;

use Exception;

class Downloader
{
    private $host;

    private $port;

    private $username;

    private $password;

    private $processedFolder;

    public function __construct($host, $port, $username, $password, $processedFolder = 'INBOX/processed')
    {
        if (!extension_loaded('imap')) {
            throw new Exception("Extension 'imap' must be loaded");
        }

        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->processedFolder = $processedFolder;
    }

    public function fetch(MailCriteria $criteria, $callback)
    {
        $mailbox = @imap_open('{' . $this->host . ':' . $this->port . '}INBOX', $this->username, $this->password);
        if (!$mailbox) {
            throw new ImapException("Cannot connect to imap server: {$this->host}:{$this->port}'");
        }

        $this->checkProcessedFolder($mailbox);

        $emails = $this->fetchEmails($mailbox, $criteria);

        if ($emails) {
            foreach ($emails as $emailIndex) {
                $overview = imap_fetch_overview($mailbox, $emailIndex, 0);
                $message = imap_body($mailbox, $emailIndex);

                $email = new Email($overview, $message);

                $processed = $callback($email);

                if ($processed) {
                    $res = imap_mail_move($mailbox, $emailIndex, $this->processedFolder);
                    if (!$res) {
                        throw new Exception("Unexpected error: Cannot move email to ");
                        break;
                    }
                }
            }
        }

        @imap_close($mailbox);
    }

    private function checkProcessedFolder($mailbox)
    {
        $list = imap_getmailboxes($mailbox, '{' . $this->host . '}', $this->processedFolder);
        if (count($list) == 0) {
            throw new Exception("You need to create imap folder '{$this->processedFolder}'");
        }
    }

    private function fetchEmails($mailbox, $criteria)
    {
        $emails = imap_search($mailbox, $criteria->getSearchString());
        if (!$emails) {
            return false;
        }
        rsort($emails);
        return $emails;
    }
}
