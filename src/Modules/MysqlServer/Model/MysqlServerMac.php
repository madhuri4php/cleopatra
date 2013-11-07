<?php

Namespace Model;

class MysqlServerMac extends BaseLinuxApp {

    // Compatibility
    public $os = array("Darwin") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("32") ;

    // Model Group
    public $modelGroup = array("Installer") ;


    public function __construct($params) {
        parent::__construct($params);
        $newRootPass = $this->getNewRootPass();
        $this->autopilotDefiner = "MysqlServer";
        $this->installCommands = array(
            // First thing, lets download the dmg
            "",
            // package install the dmg
            "",
            // add dir to path
            'echo "/usr/local/mysql/bin" >> /etc/paths',
            // update the root password
            "mysqladmin -uroot password $newRootPass",
            // remove the dmg
            "rm /tmp/"
        );
        $this->uninstallCommands = array(
            ""
        );
        $this->programDataFolder = "/opt/MysqlServer"; // command and app dir name
        $this->programNameMachine = "mysqlserver"; // command and app dir name
        $this->programNameFriendly = "MySQL Server!"; // 12 chars
        $this->programNameInstaller = "MySQL Server";
        $this->initialize();
    }

    private function getNewRootPass() {
        if (isset($this->params["mysql-root-pass"])) {
            $newRootPass = $this->params["mysql-root-pass"] ; }
        else if (AppConfig::getProjectVariable("mysql-default-root-pass") != "") {
            $newRootPass = AppConfig::getProjectVariable("mysql-default-root-pass") ; }
        else {
            $newRootPass = "cleopatra" ; }
        return $newRootPass;
    }

}