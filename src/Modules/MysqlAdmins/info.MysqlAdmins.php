<?php

Namespace Info;

class MysqlAdminsInfo extends Base {

  public $hidden = false;

  public $name = "MysqlAdmins";

  public function __construct() {
    parent::__construct();
  }

  public function routesAvailable() {
    return array( "MysqlAdmins" =>  array_merge(parent::defaultActionsAvailable(), array("install") ) );
  }

  public function routeAliases() {
    return array("mysql-admins"=>"MysqlAdmins", "mysqladmins"=>"MysqlAdmins");
  }

  public function helpDefinition() {
    $help = <<<"HELPDATA"
  This command allows you to install admin users for MySQL so that MySQL can
  be managed without using the Root User.

  MysqlAdmins, mysql-admins, mysqladmins

        - install
        Installs Mysql Admin Users.
        example: cleopatra mysql-admins install

HELPDATA;
    return $help ;
  }

}