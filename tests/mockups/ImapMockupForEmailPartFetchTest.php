<?php

namespace Tomaj\ImapMailDownloader;

require_once dirname(__FILE__) .'/ImapMockup.php';

class ImapMockupForEmailPartFetchTest extends \Tomaj\ImapMailDownloader\ImapMockup
{
    public function imapFetchOverview($mailbox, $emailIndex, $options)
    {

        $data = new \stdClass;
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

        return array($data);
    }

    public function imapFetchheader($mailbox, $emailIndex, $options)
    {
        return '1234567890 8yc81bch2zzxkjtyp8eraqziaou';
    }

    public function imapBody($mailbox, $emailIndex)
    {
        return 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg';
    }
}
