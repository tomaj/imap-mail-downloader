<?php

namespace Tomaj\ImapMailDownloader;

class Downloader
{
    private $host;

    private $port;

    private $username;

    private $password;

    private $processedFolder = 'INBOX/processed';

    /**
     * @var bool|array
     */
    private $alerts = FALSE;

    /**
     * @var bool|array
     */
    private $errors = FALSE;

    public function __construct($host, $port, $username, $password)
    {
        if (!extension_loaded('imap')) {
            throw new \Exception('Extension \'imap\' must be loaded');
        }

        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    /** Get IMAP alerts
     * @return array|bool
     * @see imap_alerts()
     */
    public function getAlerts(){
        return $this->alerts;
    }

    /** Get IMAP errors
     * @return array|bool
     * @see imap_errors()
     */
    public function getErrors(){
        return $this->errors;
    }

    public function fetch(MailCriteria $criteria, $callback)
    {
        $mailbox = NULL;
        try {
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
                            throw new \Exception("Unexpected error: Cannot move email to ");
                            break;
                        }
                    }
                }
            }
        } catch(\Exception $e){
            throw $e;
        } finally {
            $this->alerts = imap_alerts();
            $this->errors = imap_errors();

            if (is_resource($mailbox)) {
                @imap_close($mailbox);
            }
        }
    }

    private function checkProcessedFolder($mailbox)
    {
        $list = imap_getmailboxes($mailbox, '{' . $this->host . '}', $this->processedFolder);
        if (count($list) == 0) {
            throw new \Exception("You need to create imap folder '{$this->processedFolder}'");
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
