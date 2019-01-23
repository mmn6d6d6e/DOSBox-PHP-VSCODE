<?php

namespace DOSBox\Command\Library;

use DOSBox\Interfaces\IOutputter;

class CmdExit extends CmdMock {

    public function execute(IOutputter $outputter){
        return true;
    }
}
