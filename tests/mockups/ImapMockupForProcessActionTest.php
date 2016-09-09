<?php

namespace Tomaj\ImapMailDownloader;

require_once dirname(__FILE__) .'/ImapMockup.php';

class ImapMockupForProcessActionTest extends ImapMockup
{

    public function imapOpen($inbox, $username, $password)
    {
        return 'mailbox-resource 1234';
    }


    public function imapSearch($mailbox, $searchString)
    {
        return array(1234567890);
    }

    public function imapMailMove($mailbox, $emailIndex, $processedFolder)
    {
        ProcessActionTest::$movedEmail = true;
        return true;
    }

    public function imapDelete($mailbox, $emailIndex)
    {
        ProcessActionTest::$deletedEmail = true;
        return true;
    }
}
