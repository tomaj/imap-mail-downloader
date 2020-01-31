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
                $structure = imap_fetchstructure($mailbox,$emailIndex);

                $attachments = $this->processAttachments($structure, $mailbox, $emailIndex);

                $email = new Email($overview, $message, $attachments);

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

    private function processAttachments($structure, $mailbox, $emailIndex)
    {
        $attachments = array();

        if (isset($structure->parts) && count($structure->parts)) {
            for ($i = 0; $i < count($structure->parts); $i++) {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );

                if ($structure->parts[$i]->ifdparameters) {
                    foreach ($structure->parts[$i]->dparameters as $object) {
                        if (strtolower($object->attribute) == 'filename') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }

                if ($structure->parts[$i]->ifparameters) {
                    foreach ($structure->parts[$i]->parameters as $object) {
                        if (strtolower($object->attribute) == 'name') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }

                if ($attachments[$i]['is_attachment']) {
                    $attachments[$i]['attachment'] = imap_fetchbody($mailbox, $emailIndex, $i + 1);
                    if ($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    } elseif ($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
            }
        }

        $attachments = array_filter($attachments, function ($item) {
            if ($item['is_attachment']) {
                return true;
            }
            return false;
        });

        return $attachments;
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
