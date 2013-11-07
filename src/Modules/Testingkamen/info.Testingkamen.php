<?php

Namespace Info;

class TestingkamenInfo extends Base {

    public $hidden = false;

    public $name = "Testingkamen - Upgrade or Re-install Testingkamen";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "Testingkamen" =>  array_merge(parent::routesAvailable(), array("install") ) );
    }

    public function routeAliases() {
      return array("cleo"=>"Testingkamen", "testingkamen"=>"Testingkamen");
    }

    public function autoPilotVariables() {
      return array(
        "Testingkamen" => array(
          "Testingkamen" => array(
            "programNameMachine" => "testingkamen", // command and app dir name
            "programNameFriendly" => " Testingkamen! ",
            "programNameInstaller" => "Testingkamen - Update to latest version",
            "programExecutorTargetPath" => 'testingkamen/src/Bootstrap.php',
          )
        )
      );
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This command allows you to update Testingkamen.

  Testingkamen, cleo, testingkamen

        - install
        Installs the latest version of testingkamen
        example: testingkamen testingkamen install

HELPDATA;
      return $help ;
    }

}