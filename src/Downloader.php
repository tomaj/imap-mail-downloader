<?php

namespace Tomaj\ImapMailDownloader;

use Tomaj\ImapMailDownloader\ProcessAction;

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

    /**
     * @var ProcessAction
     */
    private $defaultProcessAction;

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



    public function __construct($host, $port, $username, $password, $defaultProcessAction = NULL)
    {
        if (!extension_loaded('imap')) {
            throw new \Exception('Extension \'imap\' must be loaded');
        }

        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;

        if ($defaultProcessAction !== NULL and $defaultProcessAction instanceof ProcessAction){
            $this->defaultProcessAction = $defaultProcessAction;
        } else {
            $this->defaultProcessAction = ProcessAction::move('INBOX/processed');
        }
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

    /**
     * @param ProcessAction $processAction
     * @return $this
     * @throws \Exception
     */
    public function setDefaultProcessAction(ProcessAction $processAction){
        if ($processAction === NULL or !($processAction instanceof  ProcessAction)){
            throw new \Exception('Default processed action is invalid!');
        }
        $this->defaultProcessAction = $processAction;
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


            // if default folder is set, check for its existence
            if ($this->defaultProcessAction->getProcessedFolder() !== NULL) {
                $this->checkProcessedFolder($mailbox, $this->defaultProcessAction->getProcessedFolder(), $this->processedFoldersAutomake);
            }

            $emails = $this->fetchEmails($mailbox, $criteria);

            if ($emails) {
                foreach ($emails as $emailIndex) {
                    // fetch only wanted parts
                    $overview = $fetchParts & self::FETCH_OVERVIEW ? imap_fetch_overview($mailbox, $emailIndex, 0) : NULL;
                    $headers = $fetchParts & self::FETCH_HEADERS ? @imap_fetchheader($mailbox, $emailIndex, 0) : NULL;
                    $body = $fetchParts & self::FETCH_BODY ? imap_body($mailbox, $emailIndex) : NULL;

                    $email = new Email($overview, $body, $headers);

                    $processAction = $callback($email);

                    if (is_bool($processAction) and $processAction) {
                        $processAction = $this->defaultProcessAction;
                    } elseif(is_callable($processAction)){
                        $processAction = ProcessAction::callback($processAction);
                    } elseif (is_string($processAction)){
                        switch($processAction){
                            case ProcessAction::ACTION_MOVE:
                                $processAction = ProcessAction::move($this->defaultProcessAction->getProcessedFolder());
                                break;

                            case ProcessAction::ACTION_DELETE:
                                $processAction = ProcessAction::delete();
                                break;

                            case ProcessAction::ACTION_CALLBACK:
                                $processAction = ProcessAction::callback($this->$this->defaultProcessedAction->getCallback());
                                break;

                            default:
                                throw \Exception("Unexpected process action: {$processAction}");
                        }
                    }

                    // do not process if FALSE;
                    if ($processAction instanceof ProcessAction){

                        switch($processAction->getAction()){
                            case ProcessAction::ACTION_MOVE:
                                $this->checkProcessedFolder($mailbox, $processAction->getProcessedFolder(), $this->processedFoldersAutomake);
                                $res = imap_mail_move($mailbox, $emailIndex, $processAction->getProcessedFolder());
                                if (!$res) {
                                    throw new \Exception("Unexpected error: Cannot move email to ");
                                    break;
                                }
                                break;

                            case ProcessAction::ACTION_DELETE:
                                $res = imap_delete($mailbox, $emailIndex);
                                if (!$res){
                                    throw new \Exception("Unexpected error: Cannot delete email.");
                                }
                                break;

                            case ProcessAction::ACTION_CALLBACK:
                                call_user_func_array($processAction->getCallback(),array($mailbox, $emailIndex));
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
