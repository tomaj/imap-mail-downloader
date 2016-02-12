<?php

namespace Tomaj\ImapMailDownloader;

require_once dirname(__FILE__) .'/ImapMockup.php';

class ImapMockupForImapFolderTest extends ImapMockup
{
    public function imapGetmailboxes($mailbox, $host, $folder)
    {
        if (ImapFolderTest::$folderExists) {
            return array(1);
        }
        return array();
    }

    public function imapMailMove($mailbox, $emailIndex, $processedFolder)
    {
        return ImapFolderTest::$folderExists;
    }

    public function imapCreatemailbox($mailbox, $folder)
    {
        ImapFolderTest::$folderExists = true;
        ImapFolderTest::$folderWasCreated = true;
        return true;
    }
}
