<?php

Namespace Info;

class YumInfo extends Base {

    public $hidden = false;

    public $name = "Add, Remove or Modify Yums";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
        // return array( "Yum" =>  array_merge(parent::routesAvailable(), array() ) );
        return array( "Yum" =>  array_merge(
            array("help", "status", "pkg-install", "pkg-ensure", "pkg-remove", "update")
        ) );
    }

    public function routeAliases() {
        return array("apt"=>"Yum");
    }

    public function packagerName() {
        return "Yum";
    }

    public function autoPilotVariables() {
      return array(
        "Yum" => array(
          "Yum" => array(
            "programDataFolder" => "", // command and app dir name
            "programNameMachine" => "apt", // command and app dir name
            "programNameFriendly" => "    Yum    ", // 12 chars
            "programNameInstaller" => "Yum",
          )
        )
      );
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This command allows you to modify create or modify apts

  Yum, apt

        - create
        Create a new system apt, overwriting if it exists
        example: cleopatra apt create --aptname="somename"

        - remove
        Remove a system apt
        example: cleopatra apt remove --aptname="somename"

        - set-password
        Set the password of a system apt
        example: cleopatra apt set-password --aptname="somename" --new-password="somepassword"

        - exists
        Check the existence of a apt
        example: cleopatra apt exists --aptname="somename"

        - show-groups
        Show groups to which a apt belongs
        example: cleopatra apt show-groups --aptname="somename"

        - add-to-group
        Add apt to a group
        example: cleopatra apt add-to-group --aptname="somename" --groupname="somegroupname"

        - remove-from-group
        Remove apt from a group
        example: cleopatra apt remove-from-group --aptname="somename" --groupname="somegroupname"

HELPDATA;
      return $help ;
    }

}