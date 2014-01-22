<?php

Namespace Model;

class NagiosServerUbuntu extends BaseLinuxApp {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("any") ;
    public $distros = array("Ubuntu") ;
    public $versions = array("11.04", "11.10", "12.04", "12.10", "13.04") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Installer") ;

  public function __construct($params) {
    parent::__construct($params);
    $this->autopilotDefiner = "NagiosServer";
    $this->installCommands = array("apt-get install -y nagios3");
    $this->uninstallCommands = array("apt-get remove -y nagios3");
    $this->programDataFolder = "/opt/NagiosServer"; // command and app dir name
    $this->programNameMachine = "nagiosserver"; // command and app dir name
    $this->programNameFriendly = "Nagios Server!"; // 12 chars
    $this->programNameInstaller = "Nagios Server";
    $this->initialize();
  }

}