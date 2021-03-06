<?php

Namespace Model;

class InvokeAllLinux extends Base {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    private $servers = array();
    private $sshCommands;
    protected $isNativeSSH ;

    public function askWhetherToInvokeSSHShell() {
        return $this->performInvokeSSHShell();
    }

    public function askWhetherToInvokeSSHScript() {
        return $this->performInvokeSSHScript();
    }

    public function askWhetherToInvokeSSHData() {
        return $this->performInvokeSSHData();
    }

    public function performInvokeSSHShell() {
        if ($this->askForSSHShellExecute() != true) { return false; }
        $this->populateServers();
        $commandExecution = true;
        echo "Opening CLI...\n"  ;
        while ($commandExecution == true) {
            $command = $this->askForACommand();
            if ( $command == false) {
                $commandExecution = false; }
            else {
                foreach ($this->servers as &$server) {
                    echo "[".$server["target"]."] Executing $command...\n"  ;
                    echo $this->doSSHCommand($server["ssh2Object"], $command) ;
                    echo "[".$server["target"]."] $command Completed...\n"  ; } } }
        echo "Shell Completed";
        return true;
    }

    public function performInvokeSSHScript(){
        if ($this->askForSSHScriptExecute() != true) { return false; }
        $scriptLoc = $this->askForScriptLocation();
        $this->populateServers();
        $this->sshCommands = explode("\n", file_get_contents($scriptLoc) ) ;
        foreach ($this->sshCommands as $sshCommand) {
            foreach ($this->servers as &$server) {
                if (isset($server["ssh2Object"]) && is_object($server["ssh2Object"])) {
                    echo "[".$server["target"]."] Executing $sshCommand...\n"  ;
                    echo $this->doSSHCommand($server["ssh2Object"], $sshCommand ) ;
                    echo "[".$server["target"]."] $sshCommand Completed...\n"  ; }
                else {
                    echo "[".$server["target"]."]Connection failure. Will not execute commands on this box..\n"  ; } } }
        echo "Script by SSH Completed";
        return true;
    }

    public function performInvokeSSHData(){
        if ($this->askForSSHDataExecute() != true) { return false; }
        $data = $this->askForSSHData();
        $this->populateServers();
        $this->sshCommands = explode("\n", $data) ;
        foreach ($this->sshCommands as $sshCommand) {
            foreach ($this->servers as &$server) {
                if (isset($server["ssh2Object"]) && is_object($server["ssh2Object"])) {
                    echo "[".$server["target"]."] Executing $sshCommand...\n"  ;
                    echo $this->doSSHCommand($server["ssh2Object"], $sshCommand ) ;
                    echo "[".$server["target"]."] $sshCommand Completed...\n"  ; }
                else {
                    echo "[".$server["target"]."]Connection failure. Will not execute commands on this box..\n"  ; } } }
        echo "Data by SSH Completed\n";
        return true;
    }

    public function populateServers() {
        $this->askForTimeout();
        $this->askForPort();
        $this->loadServerData();
        $this->loadSSHConnections();
    }

    private function loadServerData() {
        $allProjectEnvs = \Model\AppConfig::getProjectVariable("environments");
        if (isset($this->params["servers"])) {
            $this->servers = unserialize($this->params["servers"]); }
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

    private function loadSSHConnections() {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $logging->log("Attempting to load SSH connections...");
        foreach ($this->servers as $srvId => &$server) {
            if (isset($this->params["environment-box-id-include"])) {
                if ($srvId != $this->params["environment-box-id-include"] ) {
                    $logging->log("Skipping {$server["name"]} for box id Include constraint") ;
                    continue ; } }
            if (isset($this->params["environment-box-id-ignore"])) {
                if ($srvId == $this->params["environment-box-id-ignore"] ) {
                    $logging->log("Skipping {$server["name"]} for box id Ignore constraint") ;
                    continue ; } }
            $attempt = $this->attemptSSH2Connection($server) ;
            if ($attempt == null) {
                $logging->log("Connection to Server {$server["target"]} failed.");  }
            else {
                $server["ssh2Object"] = $attempt ;
                $logging->log("Connection to Server {$server["target"]} successful.");
                if (!isset($this->isNativeSSH) || (isset($this->isNativeSSH) && $this->isNativeSSH != true)) {
                    echo $this->changeBashPromptToPharaoh($server["ssh2Object"]); }
                echo $this->doSSHCommand($server["ssh2Object"], 'echo "Pharaoh Remote SSH on ...'.$server["target"].'"', true ) ; } }
        return true;
    }

    protected function attemptSSH2Connection($server) {
        $pword = (isset($server["pword"])) ? $server["pword"] : false ;
        $pword = (isset($server["password"])) ? $server["password"] : $pword ;
        //if (function_exists("ssh2_connect")) {
        if ( 1 === 0) { // @todo native ssh hangs long commands
            $this->isNativeSSH = true ;
            $sshFactory = new \Model\Invoke();
            $ssh = $sshFactory->getModel($this->params, "NativeWrapper" ) ;
            $ssh->target = $server["target"] ;
            $ssh->port = $this->params["port"];
            $ssh->timeout = $this->params["timeout"] ;
            if ($ssh->login($server["user"], $pword) == true) { return $ssh; }
            return null; }
        else {
            if (!class_exists('Net_SSH2')) {
                $srcFolder =  str_replace("/Model", "", dirname(__FILE__) ) ;
                $ssh2File = $srcFolder."/Libraries/seclib/Net/SSH2.php" ;
                require_once($ssh2File) ;}
            $ssh = new \Net_SSH2($server["target"], $this->params["port"], $this->params["timeout"]);
            $pword = $this->getKeyIfAvailable($pword);
            if ($ssh->login($server["user"], $pword) == true) { return $ssh; }
            return null; }
    }

    private function getKeyIfAvailable($pword) {
        if (substr($pword, 0, 4) == 'KS::') {
            $ksf = new SshKeyStore();
            $ks = $ksf->getModel($this->params) ;
            $ks->params["key"] = $pword ;
            $pword = $ks->findKey() ; }
        if (substr($pword, 0, 1) == '~') {
            $home = $_SERVER['HOME'] ;
            $pword = str_replace('~', $home, $pword) ; }
        if (file_exists($pword)) {
            if (!class_exists('Crypt_RSA')) {
                $srcFolder =  str_replace("/Model", "/Libraries", dirname(__FILE__) ) ;
                $rsaFile = $srcFolder."/seclib/Crypt/RSA.php" ;
                require_once($rsaFile) ; }
            $key = new \Crypt_RSA();
            $key->loadKey(file_get_contents($pword));
            return $key ; }
        return $pword ;
    }

    private function askForSSHShellExecute(){
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Invoke SSH Shell on Server group?';
        return self::askYesOrNo($question);
    }

    private function askForSSHScriptExecute(){
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Invoke SSH Script on Server group?';
        return self::askYesOrNo($question);
    }

    private function askForSSHDataExecute(){
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Invoke SSH Data on Server group?';
        return self::askYesOrNo($question);
    }

    private function askForScriptLocation(){
        if (isset($this->params["ssh-script"])) { return $this->params["ssh-script"] ; }
        $question = 'Enter Location of bash script to execute';
        return self::askForInput($question, true);
    }

    private function askForSSHData(){
        if (isset($this->params["ssh-data"])) { return $this->params["ssh-data"] ; }
        $question = 'Enter data to execute via SSH';
        return self::askForInput($question, true);
    }

    private function askForServerInfo(){
        $startQuestion = <<<QUESTION
***********************************
*   Due to a software limitation, *
*    The user that you use here   *
*  will have their command prompt *
*    changed to PHARAOHPROMPT     *
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

    private function askForTimeout(){
        if (isset($this->params["timeout"])) { return ; }
        if (isset($this->params["guess"])) {
            $this->params["timeout"] = 100 ;
            return ; }
        $question = 'Please Enter SSH Timeout in seconds';
        $input = self::askForInput($question, true) ;
        $this->params["timeout"] = $input ;
    }

    private function askForPort(){
        if (isset($this->params["port"])) { return ; }
        if (isset($this->params["guess"])) {
            $this->params["port"] = 22 ;
            return ; }
        $question = 'Please Enter remote SSH Port';
        $input = self::askForInput($question, true) ;
        $this->params["port"] = $input ;
    }

    private function askForServerTarget(){
        if (isset($this->params["ssh-target"])) { return $this->params["ssh-target"] ; }
        $question = 'Please Enter SSH Server Target Host Name/IP';
        $input = self::askForInput($question, true) ;
        return  $input ;
    }

    private function askForServerUser(){
        if (isset($this->params["ssh-user"])) { return $this->params["ssh-user"] ; }
        $question = 'Please Enter SSH User';
        $input = self::askForInput($question, true) ;
        return  $input ;
    }

    private function askForServerPassword(){
        if (isset($this->params["ssh-key-path"])) { return $this->params["ssh-key-path"] ; }
        else if (isset($this->params["ssh-pass"])) { return $this->params["ssh-pass"] ; }
        $question = 'Please Enter Server Password or Key Path';
        $input = self::askForInput($question) ;
        return  $input ;
    }

    private function askForACommand(){
        $question = 'Enter command to be executed on remote servers? Enter none to close connection and end program';
        $input = self::askForInput($question) ;
        return ($input=="") ? false : $input ;
    }

    private function changeBashPromptToPharaoh( $sshObject ) {
        $command = 'echo "export PS1=PHARAOHPROMPT" > ~/.bash_login ' ;
        return $sshObject->exec("$command\n") ;
    }

    private function doSSHCommand( $sshObject, $command, $first=null ) {
        if ($this->isNativeSSH) {
            return $sshObject->exec($command) ; }
        $returnVar = ($first==null) ? "" : $sshObject->read("PHARAOHPROMPT") ;
        $sshObject->write("$command\n") ;
        $returnVar .= $sshObject->read("PHARAOHPROMPT") ;
        return str_replace("PHARAOHPROMPT", "", $returnVar) ;
    }

}