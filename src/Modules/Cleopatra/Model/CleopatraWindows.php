<?php

Namespace Model;

class CleopatraWindows extends BasePHPWindowsApp {

    // Compatibility
    public $os = array("Windows", "WINNT") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "Cleopatra";
        $this->fileSources = array(
          array(
              "https://github.com/PharaohTools/cleopatra.git",
              "cleopatra",
              null // can be null for none
          )
        );
        $this->programNameMachine = "cleopatra"; // command and app dir name
        $this->programNameFriendly = " Cleopatra! "; // 12 chars
        $this->programNameInstaller = "Cleopatra - Update to latest version";
        $this->programExecutorTargetPath = 'cleopatra/src/Bootstrap.php';
        $this->initialize();
    }

}