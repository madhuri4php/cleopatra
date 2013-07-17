<?php

Namespace Info;

class StandardToolsInfo extends Base {

  public $hidden = false;

  public $name = "StandardTools";

  public function __construct() {
    parent::__construct();
  }

  public function routesAvailable() {
    return array( "StandardTools" =>  array_merge(parent::defaultActionsAvailable(), array("install") ) );
  }

  public function routeAliases() {
    return array("standard-tools"=>"StandardTools", "standardtools"=>"StandardTools",
      "stdtools"=>"StandardTools", "std-tools"=>"StandardTools");
  }

  public function helpDefinition() {
    $help = <<<"HELPDATA"
  This command allows you to install a few GC recommended Standard Tools
  for productivity in your system.  The kinds of tools we found ourselves
  installing on every box we have, client or server. These include curl,
  vim, drush and zip.

  StandardTools, standard-tools, standardtools, stdtools, std-tools

        - install
        Installs some standard tools
        example: cleopatra stdtools install

HELPDATA;
    return $help ;
  }

}