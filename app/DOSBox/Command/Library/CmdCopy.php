<?php

namespace DOSBox\Command\Library;

use DOSBox\Interfaces\IDrive;
use DOSBox\Interfaces\IOutputter;
use DOSBox\Filesystem\File;

use DOSBox\Command\BaseCommand as Command;

class CmdCopy extends Command {
    const MSG_FAILED = 'Cannot copy file!';
    const MSG_FILE_EXISTS = self::MSG_FAILED.' File with same name already exists!';
    const MSG_FILE_NOT_EXISTS = self::MSG_FAILED.' File not exists!';

    private $sourceFile = null;
    private $targetDir = null;

    public function __construct($commandName, IDrive $drive){
        parent::__construct($commandName, $drive);
    }

    public function checkNumberOfParameters($numberOfParametersEntered) {
        return $numberOfParametersEntered == 2 ? true : false;
    }

    public function checkParameterValues(IOutputter $outputter) {
        return true;
    }

    public function execute(IOutputter $outputter) {
        // copied file
        $fileName = $this->getParameterAt(0);
        $currentDir = $this->getDrive()->getCurrentDirectory();
        $filteredFiles = array_filter($currentDir->getContent(), function($fileItem) use ($fileName) {
            return $fileItem->getName() == $fileName;
        });
        $sourceFile = $filteredFiles[0];

        // target directory
        $dirName = $this->getParameterAt(1);
        $targetDir = $this->getDrive()->getItemFromPath($dirName);

        // write copied file to target directory
        $newFile = new File($sourceFile->getName(), $sourceFile->getFileContent());
        $targetDir->add($newFile);
    }
}
