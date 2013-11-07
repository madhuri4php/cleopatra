<?php

Namespace Model;

class SVNUbuntu extends BaseLinuxApp {

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
    $this->autopilotDefiner = "SVN";
    $this->installCommands = array("apt-get install subversion -y --force-yes ");
    $this->uninstallCommands = array("apt-get remove subversion -y --force-yes ");
    $this->programDataFolder = "";
    $this->programNameMachine = "svn"; // command and app dir name
    $this->programNameFriendly = "!Subversion!"; // 12 chars
    $this->programNameInstaller = "SVN";
    $this->initialize();
  }

}