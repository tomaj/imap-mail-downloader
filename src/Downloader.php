<?php

namespace Tomaj\ImapMailDownloader;

class Downloader
{
    const FETCH_OVERVIEW    = 1;
    const FETCH_HEADERS     = 2;
    const FETCH_BODY        = 4;

    private $host;

    private $port;

    private $username;

    private $password;


    /**
     * @var string
     */
    private $inboxFolder = 'INBOX';

    private $processedFolder = 'INBOX/processed';

    /**
     * @var bool
     */
    private $processedFoldersAutomake = TRUE;

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

    /**
     * Set inbox folder
     * @param string $inboxFolder
     * @default "INBOX"
     * @return $this
     */
    public function setInboxFolder($inboxFolder = 'INBOX'){
        $this->inboxFolder = $inboxFolder;
        return $this;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setProcessedFoldersAutomake($enabled){
        $this->processedFoldersAutomake = $enabled;
        return $this;
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

    public function fetch(MailCriteria $criteria, $callback, $fetchParts = self::FETCH_OVERVIEW | self::FETCH_BODY)
    {
        $HOST = '{' . $this->host . ':' . $this->port . '}';
        $INBOX = $HOST . $this->inboxFolder;

        $mailbox = NULL;
        try {
            $mailbox = @imap_open($INBOX, $this->username, $this->password);
            if (!$mailbox) {
                throw new ImapException("Cannot connect to imap server: {$HOST}'");
            }

            $this->checkProcessedFolder($mailbox, $this->processedFolder, $this->processedFoldersAutomake);

            $emails = $this->fetchEmails($mailbox, $criteria);

            if ($emails) {
                foreach ($emails as $emailIndex) {
                    // fetch only wanted parts
                    $overview = $fetchParts & self::FETCH_OVERVIEW ? imap_fetch_overview($mailbox, $emailIndex, 0) : NULL;
                    $headers = $fetchParts & self::FETCH_HEADERS ? @imap_fetchheader($mailbox, $emailIndex, 0) : NULL;
                    $body = $fetchParts & self::FETCH_BODY ? imap_body($mailbox, $emailIndex) : NULL;

                    $email = new Email($overview, $body, $headers);

                    $processed = $callback($email);

                    if ($processed) {
                        $res = imap_mail_move($mailbox, $emailIndex, $this->processedFolder);
                        if (!$res) {
                            throw new \Exception("Unexpected error: Cannot move email to {$this->processedFolder}");
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

    private function checkProcessedFolder($mailbox, $processedFolder, $automake = FALSE)
    {
        $HOST = '{' . $this->host . ':' . $this->port . '}';
        $list = imap_getmailboxes($mailbox, $HOST, $processedFolder);
        if (count($list) == 0){
            if ($automake){
                imap_createmailbox($mailbox,$processedFolder);
            } else {
                throw new \Exception("You need to create imap folder '{$processedFolder}'");
            }
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
