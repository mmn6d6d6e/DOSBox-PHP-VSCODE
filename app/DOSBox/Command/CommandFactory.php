<?php

namespace DOSBox\Command;

use DOSBox\Interfaces\IDrive;
use DOSBox\Command\Library\CmdCd as CmdCd;
use DOSBox\Command\Library\CmdDir as CmdDir;
use DOSBox\Command\Library\CmdMkDir as CmdMkDir;
use DOSBox\Command\Library\CmdMkFile as CmdMkFile;
use DOSBox\Command\Library\CmdExit as CmdExit;
use DOSBox\Command\Library\CmdCopy as CmdCopy;

class CommandFactory {
    private $commands = array();

    public function __construct(IDrive $drive){
        array_push($this->commands, new CmdDir("dir", $drive));
        array_push($this->commands, new CmdCd("cd", $drive));
        array_push($this->commands, new CmdCd("chdir", $drive));
        array_push($this->commands, new CmdMkDir("mkdir", $drive));
        array_push($this->commands, new CmdMkDir("md", $drive));
        array_push($this->commands, new CmdMkFile("mkfile", $drive));
        array_push($this->commands, new CmdMkFile("mf", $drive));
        array_push($this->commands, new CmdExit("exit", $drive));
        array_push($this->commands, new CmdCopy("copy", $drive));
        array_push($this->commands, new CmdCopy("cp", $drive));

        // Add your commands here
    }

    public function getCommands(){
        return $this->commands;
    }
}
