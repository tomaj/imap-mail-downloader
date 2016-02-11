<?php

namespace Tomaj\ImapMailDownloader;

class ProcessAction
{
    const ACTION_MOVE = 'move';
    const ACTION_DELETE = 'delete';
    const ACTION_CALLBACK = 'callback';

    private $action = 'move';
    private $processedFolder = null;
    private $callback = null;

    public static function createDefault($action, $processedFolder = null, $callback = null)
    {

        if ($action == self::ACTION_MOVE and !is_string($processedFolder)) {
            throw new \Exception("Invalid processed folder for action PROCESSED_ACTION_MOVE: {$processedFolder}");
        } elseif ($action == self::ACTION_CALLBACK and !is_callable($callback)) {
            throw new \Exception("Invalid process callback for action PROCESSED_ACTION_CALLBACK");
        }

        $processAction = new ProcessAction();
        $processAction->action = $action;
        $processAction->processedFolder = $processedFolder;
        $processAction->callback = $callback;
        return $processAction;
    }


    public static function move($folder)
    {
        $processAction = new ProcessAction();
        $processAction->action = self::ACTION_MOVE;
        $processAction->processedFolder = $folder;
        return $processAction;
    }

    public static function delete()
    {
        $processAction = new ProcessAction();
        $processAction->action = self::ACTION_DELETE;
        return $processAction;
    }

    public static function callback($callback)
    {
        $processAction = new ProcessAction();
        $processAction->action = self::ACTION_CALLBACK;
        $processAction->callback = $callback;
        return $processAction;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getProcessedFolder()
    {
        return $this->processedFolder;
    }

    public function getCallback()
    {
        return $this->callback;
    }
}
