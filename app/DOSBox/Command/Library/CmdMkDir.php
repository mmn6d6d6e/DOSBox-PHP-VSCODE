<?php

namespace DOSBox\Command\Library;

use DOSBox\Interfaces\IDrive;
use DOSBox\Interfaces\IOutputter;
use DOSBox\Filesystem\Directory;
use DOSBox\Command\BaseCommand as Command;

class CmdMkDir extends Command {
    const PARAMETER_CONTAINS_BACKLASH = "At least one parameter denotes a path rather than a directory name.";
    const MSG_FAIL = 'Cannot create folder!';
    const MSG_FILE_EXISTS = self::MSG_FAIL.' File with same name already exists!';
    const MSG_FOLDER_EXISTS = self::MSG_FAIL.' Folder with same name already exists!';

    public function __construct($commandName, IDrive $drive){
        parent::__construct($commandName, $drive);
    }

    public function checkNumberOfParameters($numberOfParametersEntered) {
        return $numberOfParametersEntered >= 1 ? true : false;
    }

    public function checkParameterValues(IOutputter $outputter) {
        for($i=0; $i< $this->getParameterCount(); $i++) {
            if ($this->parameterContainsBacklashes($this->getParameterAt($i), $outputter))
                return false;
        }
        return true;
    }

    // TODO: Unit test
    public static function parameterContainsBacklashes($parameter, IOutputter $outputter) {
        // Do not allow "mkdir c:\temp\dir1" to keep the command simple
        if (strstr($parameter, "\\") !== false || strstr($parameter, "/") !== false) {
            $outputter->printLine(self::PARAMETER_CONTAINS_BACKLASH);
            return true;
        }

        return false;
    }

    public function execute(IOutputter $outputter){
        for($i=0; $i < $this->getParameterCount(); $i++) {
            $fileName = $this->params[$i];
            $listItems = $this->getDrive()->getCurrentDirectory()->getContent();
            foreach($listItems as $item){
                if($item->getName() == $fileName){
                    if($item->isDirectory()){
                        $outputter->printNoLine(self::MSG_FOLDER_EXISTS);
                        $outputter->newLine();
                        return;
                    }
                    $outputter->printNoLine(self::MSG_FILE_EXISTS);
                    $outputter->newLine();
                    return;
                }
            }
            $this->createDirectory($this->params[$i], $this->getDrive());
        }
    }

    public function createDirectory($newDirectoryName, IDrive $drive) {
        $newDirectory = new Directory($newDirectoryName);
        $drive->getCurrentDirectory()->add($newDirectory);
    }
}
