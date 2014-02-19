<?php

Namespace Model;

class SshHardenUbuntu extends BaseLinuxApp {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("Debian") ;
    public $distros = array("Ubuntu") ;
    public $versions = array("11.04", "11.10", "12.04", "12.10", "13.04") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;
    protected $sshhardenName ;
    protected $actionsToMethods =
        array(
            "securify" => "performSshHardenSecurify",
        ) ;

    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "SshHarden";
        $this->programDataFolder = "";
        $this->programNameMachine = "sshharden"; // command and app dir name
        $this->programNameFriendly = "!SshHarden!!"; // 12 chars
        $this->programNameInstaller = "SshHarden";
        $this->initialize();
    }

    protected function performSshHardenSecurify() {
        return $this->securify();
    }

    public function securify() {
        $this->shouldNotAllowRoot();
        $this->shouldNotAllowPlainTextPasswords();
        return true;
    }

    private function shouldNotAllowRoot() {
        $fileFactory = new \Model\File();
        $file = $fileFactory->getModel($this->params);
        $file->setFile('/etc/ssh/sshd_config') ;
        $file->replaceIfPresent(new RegExp("/^#?PermitRootLogin yes/m"), 'PermitRootLogin no');
        $file->shouldHaveLine("PermitRootLogin no");
        $consoleFactory = new \Model\Console();
        $console = $consoleFactory->getModel($this->params);
        $console->log("/etc/ssh/sshd_config modified to disallow root ssh login") ;
    }

    private function shouldNotAllowPlainTextPasswords() {
        $fileFactory = new \Model\File();
        $file = $fileFactory->getModel($this->params);
        $file->setFile('/etc/ssh/sshd_config') ;
        $file->replaceIfPresent(new RegExp("/^#?PasswordAuthentication yes/m"), 'PasswordAuthentication no');
        $file->shouldHaveLine("PasswordAuthentication no");
        $consoleFactory = new \Model\Console();
        $console = $consoleFactory->getModel($this->params);
        $console->log("/etc/ssh/sshd_config modified to disallow password based ssh login") ;
        $serviceFactory = new \Model\Service();
        $service = $serviceFactory->getModel($this->params);
        $service->setService("ssh") ;
        $service->restart() ;
    }

}