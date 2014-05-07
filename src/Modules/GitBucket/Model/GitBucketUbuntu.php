<?php

Namespace Model;

class GitBucketUbuntu extends BaseLinuxApp {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("Debian") ;
    public $distros = array("Ubuntu") ;
    public $versions = array("11.04", "11.10", "12.04", "12.10", "13.04") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "GitBucket";
        $this->installCommands = array(
            array("method"=> array("object" => $this, "method" => "executeDependencies", "params" => array()) ),
            array("command"=> array(
                "cd /tmp" ,
                "mkdir -p /tmp/gitbucket" ,
                "cd /tmp/gitbucket" ,
                "wget http://github.com/takezoe/gitbucket/releases/tag/1.13",
                "mkdir -p ****PROGDIR****",
                "mv /tmp/gitbucket/* ****PROGDIR****",
                "rm -rf /tmp/gitbucket/" ) ) ,
            array("method"=> array("object" => $this, "method" => "askForRepoHome", "params" => array()) ),
            array("method"=> array("object" => $this, "method" => "setExecutorCommand", "params" => array()) ),
            array("method"=> array("object" => $this, "method" => "deleteExecutorIfExists", "params" => array()) ),
            array("method"=> array("object" => $this, "method" => "saveExecutorFile", "params" => array()) ),
        );
        $this->uninstallCommands = array(
            array("method"=> array("object" => $this, "method" => "executeDependencies", "params" => array()) ),
            array("method"=> array("object" => $this, "method" => "deleteExecutorIfExists", "params" => array()) ),
        );
        $this->programDataFolder = "/opt/gitbucket/";
        $this->programNameMachine = "gitlab"; // command and app dir name
        $this->programNameFriendly = "!Git Lab!!"; // 12 chars
        $this->programNameInstaller = "Git Lab";
        $this->programExecutorFolder = "/usr/bin";
        $this->programExecutorTargetPath = "selenium";
        $this->initialize();
    }

    public function executeDependencies() {
        $gitToolsFactory = new \Model\GitTools($this->params);
        $gitTools = new $gitToolsFactory->getModel($this->params);
        $gitTools->ensureInstalled();
        $javaFactory = new \Model\Java();
        $java = new $javaFactory->getModel($this->params);
        $java->ensureInstalled();
    }

    public function askForRepoHome() {
        if (isset($this->params["repository-home"])) {
            $this->repoHome = $this->params["repository-home"]; }
        else if (isset($this->params["guess"])) {
            $defaultRepo = "/opt/gitbucket/repositories" ;
            $this->executeAndOutput("mkdir -p $defaultRepo") ;
            $this->repoHome = $defaultRepo ; }
        else {
            $question = "Enter Repository Root Directory:";
            $this->repoHome = self::askForInput($question, true); }
    }

    public function setExecutorCommand() {
        $this->programExecutorCommand = 'java -jar ' . $this->programDataFolder . '/gitbucket.jar';
    }

}