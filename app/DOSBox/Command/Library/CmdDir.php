<?php

namespace DOSBox\Command\Library;

use DOSBox\Interfaces\IDrive;
use DOSBox\Interfaces\IOutputter;
use DOSBox\Filesystem\Directory;
use DOSBox\Command\BaseCommand as Command;

class CmdDir extends Command {
    private $directoryToPrint;

    const SYSTEM_CANNOT_FIND_THE_PATH_SPECIFIED = "File Not Found.";

    public function __construct($commandName, IDrive $drive){
        parent::__construct($commandName, $drive);
    }

    public function checkNumberOfParameters($numberOfParametersEntered) {
        return ($numberOfParametersEntered == 0 || $numberOfParametersEntered == 1);
    }

    public function checkParameterValues(IOutputter $outputter) {
        if ($this->getParameterCount() == 0) {
            $this->directoryToPrint = $this->getDrive()->getCurrentDirectory(); // TODO: check with params
        } else {
            $this->directoryToPrint = $this->checkAndPreparePathParameter($this->getParameterAt(0), $outputter);
        }
        return $this->directoryToPrint != NULL;
    }

    private function checkAndPreparePathParameter($pathName, IOutputter $outputter) {
        $fsi = $this->getDrive()->getItemFromPath($pathName);

        if ($fsi == null) {
            $outputter->printLine(self::SYSTEM_CANNOT_FIND_THE_PATH_SPECIFIED);
            return null;
        }

        return $fsi;
    }

    public function execute(IOutputter $outputter){
        $this->checkParameterValues($outputter);

        $this->printHeader($this->directoryToPrint, $outputter);
        $this->printContent($this->directoryToPrint, $outputter);
        $this->printFooter($this->directoryToPrint, $outputter);
    }

    public function printHeader($directoryToPrint, IOutputter $outputter) {
        $outputter->printLine(" Directory of " . $directoryToPrint->getPath());
        $outputter->newLine();
    }

    public function printContent($directoryToPrint, IOutputter $outputter) {
        if($directoryToPrint->isDirectory()) {
            $directoryContent = $directoryToPrint->getContent();
            
            $outputter->printNoLine("\t\t\t");
            $outputter->printNoLine($directoryToPrint->getTimestamp());
            
            $outputter->printNoLine("\t");
            $outputter->printNoLine("<DIR>");
            $outputter->printNoLine("\t");
            $outputter->printNoLine("  ");

            $outputter->printNoLine($directoryToPrint->getName());
            $outputter->newLine();
            $outputter->newLine();
        } else {
            $directoryContent = [$this->directoryToPrint];
        }
        foreach ($directoryContent as $item) {
            $outputter->printNoLine("\t\t\t");
            $outputter->printNoLine($item->getTimestamp());
            
            if ($item->isDirectory()) {
                $outputter->printNoLine("\t");
                $outputter->printNoLine("<DIR>");
                $outputter->printNoLine("\t");
                $outputter->printNoLine("  ");
            } else {
                $outputter->printNoLine("\t\t");
                $outputter->printNoLine($item->getSize() . " ");
            }

            $outputter->printNoLine($item->getName());
            $outputter->newLine();
        }
    }

    public function printFooter($directoryToPrint, IOutputter $outputter) {
        $outputter->printLine("\t" . $directoryToPrint->getNumberOfContainedFiles() . " File(s)");
        $outputter->printLine("\t" . $directoryToPrint->getNumberOfContainedDirectories() . " Dir(s)");
    }
}
