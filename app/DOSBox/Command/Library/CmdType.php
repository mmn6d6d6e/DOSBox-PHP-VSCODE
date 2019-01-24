<?php

namespace DOSBox\Command\Library;

use DOSBox\Interfaces\IDrive;
use DOSBox\Interfaces\IOutputter;

use DOSBox\Command\BaseCommand as Command;

class CmdType extends Command {
    private $targetFile;

    public function __construct($commandName, IDrive $drive){
        parent::__construct($commandName, $drive);
    }

    public function checkNumberOfParameters($numberOfParametersEntered) {
        return $numberOfParametersEntered == 1 ? true : false;
    }

    public function checkParameterValues(IOutputter $outputter) {
        $fileName = $this->getParameterAt(0);
        $currentDir = $this->getDrive()->getCurrentDirectory();
        
        $content = array_filter($currentDir->getContent(), function ($file) use ($fileName) {
            return $file->getName() == $fileName;
        });
        if(!$fileName){
            $outputter->printNoLine("The system cannot find the file specified"); 
            $outputter->newLine();
            return false;
        }

        if(!isset($content[0])){
            $outputter->printNoLine("Directory not found"); 
            $outputter->newLine();
            return false;
        }
        else{
        $this->targetFile = $content[0];
        }

        if($this->targetFile->isDirectory())
        {
            $outputter->printNoLine("Access Denied"); 
            $outputter->newLine();
            return false;   
        }
        // TODO: untuk pak Nuansa, pengecekan sebelum eksekusi di sini
        return true;

    }

    public function execute(IOutputter $outputter){
        $this->printContent($this->targetFile, $outputter);
    }

    protected function printContent($targetFile, IOutputter $outputter){
        $outputter->printNoLine($targetFile->getFileContent());
        $outputter->newLine();
    }
}
