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
        $this->installCommands = $this->getInstallCommands() ;
        $this->uninstallCommands = array(
            array("method"=> array("object" => $this, "method" => "executeDependencies", "params" => array()) ),
            array("method"=> array("object" => $this, "method" => "deleteExecutorIfExists", "params" => array()) ),
        );
        $this->programDataFolder = "/opt/gitbucket/";
        $this->programNameMachine = "gitbucket"; // command and app dir name
        $this->programNameFriendly = "!GitBucket!"; // 12 chars
        $this->programNameInstaller = "Git Bucket";
        $this->programExecutorFolder = "/usr/bin";
        $this->programExecutorTargetPath = "gitbucket";
        $this->versionInstalledCommand = 'echo "1.13"' ;
        $this->versionRecommendedCommand = 'echo "1.13"' ;
        $this->versionLatestCommand = 'echo "1.13"' ;
        $this->initialize();
    }

    protected function getInstallCommands() {
        $ray =  array(
            array("method"=> array("object" => $this, "method" => "executeDependencies", "params" => array()) ),
            array("command"=> array(
                "cd /tmp" ,
                "mkdir -p /tmp/gitbucket" ,
                "cd /tmp/gitbucket" ,
                "git clone https://github.com/phpengine/gitbucket-war.git",
                "mkdir -p ****PROGDIR****",
                "mv /tmp/gitbucket/gitbucket-war/* ****PROGDIR****",
                "rm -rf /tmp/gitbucket/" ) ) ,
            array("method"=> array("object" => $this, "method" => "askForRepoHome", "params" => array()) ),
            array("method"=> array("object" => $this, "method" => "setExecutorCommand", "params" => array()) ),
            array("method"=> array("object" => $this, "method" => "deleteExecutorIfExists", "params" => array()) ),
            array("method"=> array("object" => $this, "method" => "saveExecutorFile", "params" => array()) ),
        );
        if (isset($this->params["with-http-port-proxy"]) && $this->params["with-http-port-proxy"] == true) {
            $dapperAuto = $this->getDapperAutoPath() ;
            $miniray = array("command"=> array("sudo dapperstrano autopilot execute --autopilot-file=$dapperAuto")) ;
            array_push($ray, $miniray)  ;
        }
        return $ray ;
    }

    private function getDapperAutoPath() {
        $path = dirname(dirname(__FILE__)).'/Autopilots/Dapperstrano/proxy-8080-to-80.php' ;
        return $path ;
    }

    public function executeDependencies() {
        $tempVersion = $this->params["version"] ;
        unset($this->params["version"]) ;
        $gitToolsFactory = new \Model\GitTools($this->params);
        $gitTools = $gitToolsFactory->getModel($this->params);
        $gitTools->ensureInstalled();
        $javaFactory = new \Model\Java();
        $java = $javaFactory->getModel($this->params);
        $java->ensureInstalled();
        $this->params["version"] = $tempVersion ;
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
        $this->programExecutorCommand = 'java -jar ' . $this->programDataFolder . 'gitbucket.war > /dev/null 2>&1 &';
    }

    public function versionInstalledCommandTrimmer($text) {
        $done = substr($text, 0, 4) ;
        return $done ;
    }

    public function versionLatestCommandTrimmer($text) {
        $done = substr($text, 0, 4) ;
        return $done ;
    }

    public function versionRecommendedCommandTrimmer($text) {
        $done = substr($text, 0, 4) ;
        return $done ;
    }

}