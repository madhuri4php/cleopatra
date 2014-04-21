<?php

Namespace Model;

class SFTPAllLinux extends Base {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    private $servers = array();

    public function askWhetherToSFTPPut() {
        return $this->performSFTPPut();
    }

    public function askWhetherToSFTPGet() {
        return $this->performSFTPGet();
    }

    public function performSFTPPut() {
        if ($this->askForSFTPExecute() != true) { return false; }
        $this->populateServers() ;
        $sourceDataPath = $this->getSourceFilePath("local") ;
        $sourceData = $this->attemptToLoad($sourceDataPath) ;
        if (is_null($sourceData)) {
            // @todo make this a logging call not an echo
            echo "SFTP Put will cancel, no source file\n" ;
            return false ;}
        $targetPath = $this->getTargetFilePath("remote") ;
        echo "Opening SFTP Connections...\n"  ;
        foreach ($this->servers as &$server) {
            echo "[".$server["target"]."] Executing SFTP Put...\n"  ;
            echo $this->doSFTPPut($server["sftpObject"], $targetPath, $sourceData) ;
            echo "[".$server["target"]."] SFTP Put Completed...\n" ; }
        echo "All SFTP Puts Completed\n";
        return true;
    }

    private function attemptToLoad($sourceDataPath){
        if (file_exists($sourceDataPath)) {
            return file_get_contents($sourceDataPath) ; }
        else {
            // @todo make this a logging call not an echo
            echo "SFTP Could not load source file\n" ;
            return null ; }
    }

    public function performSFTPGet() {
        if ($this->askForSFTPExecute() != true) { return false; }
        $this->populateServers();
        $sourceDataPath = $this->getSourceFilePath("remote");
        $targetPath = $this->getTargetFilePath("local");
        echo "Opening SFTP Connections...\n"  ;
        foreach ($this->servers as &$server) {
            echo "[".$server["target"]."] Executing SFTP Get...\n"  ;
            echo $this->doSFTPGet($server["sftpObject"], $sourceDataPath, $targetPath) ;
            echo "[".$server["target"]."] SFTP Get Completed...\n"  ; }
        echo "All SFTP Gets Completed\n";
        return true;
    }

    private function doSFTPPut($sftpObject, $remoteFile, $data) {
        if ($sftpObject instanceof \Net_SFTP) {
            $result = $sftpObject->put($remoteFile, $data); }
        else {
            // @todo make this a log
            echo "No SFTP Object, Connection likely failed\n";
            $result = false; }
        return $result ;
    }

    private function doSFTPGet($sftpObject, $remoteFile, $localFile = false) {
        if ($sftpObject instanceof \Net_SFTP) {
            $result = $sftpObject->get($remoteFile, $localFile); }
        else {
            // @todo make this a log
            echo "No SFTP Object\n";
            $result = false; }
        return $result ;
    }

    public function populateServers() {
        $this->loadServerData();
        $this->loadSFTPConnections();
    }

    private function loadServerData() {
        $allProjectEnvs = \Model\AppConfig::getProjectVariable("environments");
        if (isset($this->params["servers"])) {
            $this->servers = $this->params["servers"]; }
        else if (isset($this->params["environment-name"])) {
            $names = $this->getEnvironmentNames($allProjectEnvs) ;
            $this->servers = $allProjectEnvs[$names[$this->params["environment-name"]]]["servers"]; }
        else if (count($allProjectEnvs) > 0) {
            $question = 'Use Environments Configured in Project?';
            $useProjEnvs = self::askYesOrNo($question, true);
            if ($useProjEnvs == true ) {
                $this->servers = new \ArrayObject($allProjectEnvs) ;
                return; } }
        else {
            $this->askForServerInfo(); }
    }

    private function getEnvironmentNames($envs) {
        $eNames = array() ;
        foreach ($envs as $envKey => $env) {
            $envName = $env["any-app"]["gen_env_name"] ;
            $eNames[$envName] = $envKey ; }
        return $eNames ;
    }

    private function loadSFTPConnections() {
        echo "Attempting to load SFTP connections...\n";
        foreach ($this->servers as &$server) {
            $attempt = $this->attemptSFTPConnection($server) ;
            if ($attempt == null) {
                echo "Connection to Server {$server["target"]} failed.\n";
                $server["sftpObject"] = null ; }
            else {
                $server["sftpObject"] = $attempt ;
                // echo $this->changeBashPromptToPharoah($server["sftpObject"]);
                // echo $this->doSFTPCopy($server["sftpObject"], 'echo "Pharoah Remote SFTP on ...'.$server["target"].'"', true ) ;
            } }
        return true;
    }

    // @todo it currently looks for both pword and password lets stick to one
    private function attemptSFTPConnection($server) {
        $srcFolder =  str_replace("/Model", "/Libraries", dirname(__FILE__) ) ;
        $ssh2File = $srcFolder."/seclib/Net/SSH2.php" ;
        require_once($ssh2File) ;
        $sftpFile = dirname($ssh2File).'/SFTP.php' ;
        require_once($sftpFile) ;
        $sftp = new \Net_SFTP($server["target"]);
        $pword = (isset($server["pword"])) ? $server["pword"] : false ;
        $pword = (isset($server["password"])) ? $server["password"] : $pword ;
        if ($sftp->login($server["user"], $pword) == true) { return $sftp; }
        return null;
    }

    private function askForSFTPExecute(){
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'SFTP on Server group?';
        return self::askYesOrNo($question);
    }

    private function askForServerInfo(){
        $startQuestion = <<<QUESTION
***********************************
*   Due to a software limitation, *
*    The user that you use here   *
*  will have their command prompt *
*    changed to PHAROAHPROMPT     *
*  ... I'm working on that one... *
*  Exit program to stop (CTRL+C)  *
***********************************
Enter Server Info:

QUESTION;
        echo $startQuestion;
        $serverAddingExecution = true;
        while ($serverAddingExecution == true) {
            $server = array();
            $server["target"] = $this->askForServerTarget();
            $server["user"] = $this->askForServerUser();
            $server["pword"] = $this->askForServerPassword();
            $this->servers[] = $server;
            $question = 'Add Another Server?';
            if ( count($this->servers)<1) { $question .= "You need to enter at least one server\n"; }
            $serverAddingExecution = self::askYesOrNo($question); }
    }

    private function askForServerTarget(){
        $question = 'Please Enter SSH Server Target Host Name/IP';
        $input = self::askForInput($question, true) ;
        return  $input ;
    }

    private function askForServerUser(){
        $question = 'Please Enter SSH User';
        $input = self::askForInput($question, true) ;
        return  $input ;
    }

    private function askForServerPassword(){
        $question = 'Please Enter Server Password or Key Path';
        $input = self::askForInput($question) ;
        return  $input ;
    }

    private function getSourceFilePath($flag = null){
        if (isset($this->params["source"])) { return $this->params["source"] ; }
        if (isset($flag)) { $question = "Enter $flag source file path" ; }
        else { $question = "Enter source file path"; }
        $input = self::askForInput($question) ;
        return ($input=="") ? false : $input ;
    }

    private function getTargetFilePath($flag = null){
        if (isset($this->params["target"])) { return $this->params["target"] ; }
        if (isset($flag)) { $question = "Enter $flag target file path" ; }
        else { $question = "Enter target file path"; }
        $input = self::askForInput($question) ;
        return ($input=="") ? false : $input ;
    }
}