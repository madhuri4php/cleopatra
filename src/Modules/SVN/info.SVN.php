<?php

Namespace Info;

class SVNInfo extends Base {

    public $hidden = false;

    public $name = "SVN - The Source Control Manager";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "SVN" =>  array_merge(parent::routesAvailable(), array("install") ) );
    }

    public function routeAliases() {
      return array("svn"=>"SVN");
    }

    public function autoPilotVariables() {
      return array(
        "SVN" => array(
          "SVN" => array(
            "programDataFolder" => "", // command and app dir name
            "programNameMachine" => "svn", // command and app dir name
            "programNameFriendly" => "!SVN!!", // 12 chars
            "programNameInstaller" => "SVN",
          )
        )
      );
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This command allows you to install the latest available SVN in the Ubuntu
  repositories.

  SVN, svn

        - install
        Installs the latest version of SVN
        example: cleopatra svn install

HELPDATA;
      return $help ;
    }

}