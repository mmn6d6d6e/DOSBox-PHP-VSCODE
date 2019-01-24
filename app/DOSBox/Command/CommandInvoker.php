<?php

namespace DOSBox\Command;

use DOSBox\Interfaces\IExecuteCommand;
use DOSBox\Interfaces\IOutputter;

class CommandInvoker implements IExecuteCommand {
    private $commands = array();

    public function __construct(){}

    public function setCommands($commands){
        $this->commands = $commands;
    }

    public function addCommand($command){
        array_push($this->commands, $command);
    }

    public function getCommands(){
        return $this->commands;
    }

    public function executeCommand($command, IOutputter $outputter) {
        

       /* if($command=='EXIT') {
            $command='exit';
        }*/

        $cmdhelp = explode(" ", $command);
        if($cmdhelp[0]=='help')
        {
            $help['cd']="Displays the name of or changes the current directory";
            $help['dir']="Displays a list of files and subdirectories in a directory.";
            $help['exit']="Quits the CMD.EXE program (command interpreter).";
            $help['format']="Formats a disk for use with Windows.";
            $help['help']="Provides Help information for Windows commands";
            $help['label']="Creates, changes, or deletes the volume label of a disk.";
            $help['mkdir']="Creates a directory";
            $help['mkfile']="Created a file.";
            $help['move']="Moves one or more files from one directory to another directory";
            
            if(!$cmdhelp[1])
            {
                foreach($help as $hlp=>$val)
                {
                    $outputter->printLine("{$hlp} => {$val}");
                }
            }
            else 
            {
                if($help[$cmdhelp[1]])
                {
                    $outputter->printLine($cmdhelp[1]." => ".$help[$cmdhelp[1]]);
                }
                else
                {
                    $outputter->printLine("Error : This command is not supported by the help utility.
                    ");
                }
            }   
        }

        if($cmdhelp[0]=='VER')
        { 
            $outputter->printLine("Microsoft Windows XP [Version 5.1.2600] as fixed text.");
            
            if(isset($cmdhelp[1])=='/w')
            {
                $help['Erwin Sutomo']="sutomo@stikom.edu";
                $help['Eva Paramita']="mita@stikom.edu";
                $help['Nunuk']="nunuk@stikom.edu.";
                $help['Nuansa Jala Persada']="nuansa@stikom.edu";
                $help['Nur Rahman Hadi']="rahman@stikom.edu";
                foreach($help as $hlp=>$val)
                {
                    $outputter->printLine("{$hlp} => {$val}");
                }
            }
        }
    
        
        $cmdName = $this->parseCommandName($command);
        $params = $this->parseCommandParams($command);

        try{
            foreach($this->commands as $cmd){
                if($cmd->compareCmdName($cmdName)){
                    $cmd->setParams($params);

                    if($cmd->checkParameters($outputter) == false) {
                        $outputter->printLine("Wrong parameter entered.");
                        return;
                    }

                    return $cmd->execute($outputter);
                }
            }
            if($command!='exit' && substr($command,0,4)!='help'&& substr($command,0,3)!='VER')
            $outputter->printLine("'{$command}' is not recognized as an internal or external command, operable program or batch file.");
        
        } catch(Exception $e){
            if($e->getMessage() != null) {
                $outputter->printLine("Unexpected exception while execution command: " . $e->getMessage());
            }
            else {
                $outputter->printLine("Unknown exception caught");
                $outputter->printLine($e->toString());
                $e->printStackTrace();
            }
        }
    }

    public function parseCommandName($command){
        $cmd = strtolower($command);
        $cmdName = NULL;

        $cmd = trim($cmd);
        $cmd = str_replace(",", " ", $cmd);
        $cmd = str_replace(";", " ", $cmd);

        $cmdName = $cmd;
        for($i=0; $i < strlen($cmd); $i++){
            if($cmd[$i] === ' '){
                $cmdName = substr($cmd, 0, $i);
                break;
            }
        }

        return $cmdName;
    }

    public function extractCommandParams($command){
        $params = trim(substr($command, strlen($this->parseCommandName($command)), strlen($command)));
        return $params;
    }

    public function parseCommandParams($command) {
        $params = array();
        $str_params = $this->extractCommandParams($command);

        $str_params = trim($str_params);
        $str_params = str_replace(",", " ", $str_params);
        $str_params = str_replace(";", " ", $str_params);

        $tmp_params = array();
        if(!empty($str_params))
            $tmp_params = explode(" ", $str_params);

        foreach($tmp_params as $param){
            if(!empty($param)) array_push($params, trim($param));
        }

        return $params;
    }
}
