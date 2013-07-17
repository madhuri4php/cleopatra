<?php

Namespace Info;

class CleopatraInfo extends Base {

    public $hidden = false;

    public $name = "Cleopatra";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "Cleopatra" =>  array_merge(parent::defaultActionsAvailable(), array("install") ) );
    }

    public function routeAliases() {
      return array("cleo"=>"Cleopatra", "cleopatra"=>"Cleopatra");
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This command allows you to update Cleopatra.

  Cleopatra, cleo, cleopatra

        - install
        Installs the latest version of cleopatra
        example: cleopatra cleopatra install

HELPDATA;
      return $help ;
    }

}