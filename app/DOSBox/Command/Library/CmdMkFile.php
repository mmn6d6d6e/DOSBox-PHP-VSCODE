<?php

namespace DOSBox\Command\Library;

use DOSBox\Interfaces\IDrive;
use DOSBox\Interfaces\IOutputter;
use DOSBox\Filesystem\File;
use DOSBox\Command\BaseCommand as Command;

class CmdMkFile extends Command {
    const MSG_FAIL = 'Cannot create file!';
    const MSG_FILE_EXISTS = self::MSG_FAIL.' File with same name already exists!';
    const MSG_FOLDER_EXISTS = self::MSG_FAIL.' Folder with same name already exists!';

    public function __construct($commandName, IDrive $drive){
        parent::__construct($commandName, $drive);
    }

    public function checkNumberOfParameters($numberOfParametersEntered) {
        return true;
    }

    public function checkParameterValues(IOutputter $outputter) {
        return true;
    }

    public function execute(IOutputter $outputter){
        $fileName = $this->params[0];
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
        $fileContent = count($this->params) >= 1 ? implode(' ', array_slice($this->params, 1)) : '';
        $newFile = new File($fileName, $fileContent);
        $this->getDrive()->getCurrentDirectory()->add($newFile);
    }

}
