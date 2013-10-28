<?php

Namespace Model;

class Firefox14Ubuntu extends BaseLinuxApp {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("Debian") ;
    public $distros = array("Ubuntu") ;
    public $versions = array("11.04", "11.10", "12.04", "12.10", "13.04") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Installer") ;

  public function __construct($params) {
    parent::__construct($params);
    $this->autopilotDefiner = "Firefox14";
    $this->installCommands = array(
      "cd /tmp" ,
      "git clone https://github.com/phpengine/cleopatra-firefox14 firefox14",
      "rm -rf ****PROGDIR****",
      "mkdir -p ****PROGDIR****",
      "mv /tmp/firefox14/* ****PROGDIR****",
      "rm -rf /tmp/firefox14" );
    $this->uninstallCommands = array(
      "rm -rf ****PROGDIR****",
      "rm -rf ****PROG EXECUTOR****", );
    $this->programDataFolder = "/opt/firefox14"; // command and app dir name
    $this->programNameMachine = "firefox14"; // command and app dir name
    $this->programNameFriendly = " Firefox 14 "; // 12 chars
    $this->programNameInstaller = "Firefox 14";
    $this->programExecutorFolder = "/usr/bin";
    $this->programExecutorTargetPath = "firefox-bin";
    $this->programExecutorCommand = $this->programDataFolder.'/'.$this->programExecutorTargetPath;
    $this->registeredPostInstallFunctions = array("deleteExecutorIfExists",
      "saveExecutorFile");
    $this->initialize();
  }

}