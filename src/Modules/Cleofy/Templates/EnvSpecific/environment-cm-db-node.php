<?php

Namespace Core ;

class AutoPilotConfigured extends AutoPilot {

    public $steps ;
    protected $myUser ;

    public function __construct() {
        $this->setSteps();
    }

    /* Steps */
    private function setSteps() {

        $this->steps =
            array(
                array ( "Logging" => array( "log" => array( "log-message" => "Lets begin Configuration of a Database Node on environment <%tpl.php%>env_name</%tpl.php%>"),),),

//                // Install Keys - Bastion Public Key
//                array ( "Logging" => array( "log" => array( "log-message" => "Lets ensure our Bastion Public Key is installed" ),),),
//                array ( "SshKeyInstall" => array( "file" =>
//                    array("public-key-file" => "build/config/cleopatra/SSH/keys/public/raw/bastion"),
//                    array("user-name" => "{$this->myUser}"),),),

                // SSH Hardening
                array ( "Logging" => array( "log" => array( "log-message" => "Lets ensure we have some SSH Security" ),),),
                array ( "SshHarden" => array( "ensure" => array(),),),

                // Standard Tools
                array ( "Logging" => array( "log" => array( "log-message" => "Lets ensure some standard tools are installed" ),),),
                array ( "StandardTools" => array( "ensure" => array(),),),

                // Git Tools
                array ( "Logging" => array( "log" => array( "log-message" => "Lets ensure some git tools are installed" ),),),
                array ( "GitTools" => array( "ensure" => array(),),),

                // PHP Modules
                array ( "Logging" => array( "log" => array( "log-message" => "Lets ensure our common PHP Modules are installed" ),),),
                array ( "PHPModules" => array( "ensure" => array(),),),

                //Mysql
                //@todo Mysql Client/Cluster etc
                array ( "Logging" => array( "log" => array( "log-message" => "Lets ensure Mysql Server is installed" ),),),
                array ( "MysqlServer" => array( "ensure" =>  array(
                    "version" => "5",
                    "version-operator" => "+"
                ), ), ),
                array ( "Logging" => array( "log" => array( "log-message" => "Lets ensure a Mysql Admin User is installed"),),),
                array ( "MysqlAdmins" => array( "install" => array(
                    "root-user" => "root",
                    "root-pass" => "cleopatra",
                    "new-user" => "root",
                    "new-pass" => "root",
                    "mysql-host" => "127.0.0.1"
                ) ) ),

                // Firewall
                array ( "Logging" => array( "log" => array( "log-message" => "Lets disable Firewall to change settings"), ) , ) ,
                array ( "Firewall" => array( "disable" => array(), ) , ) ,
                array ( "Logging" => array( "log" => array( "log-message" => "Lets deny all input"), ) , ) ,
                array ( "Firewall" => array( "default" => array("policy" => "deny" ), ) , ) ,
                array ( "Logging" => array( "log" => array( "log-message" => "Lets allow SSH input"), ) , ) ,
                array ( "Firewall" => array( "allow" => array("firewall-rule" => "ssh/tcp" ), ) , ) ,
                array ( "Logging" => array( "log" => array( "log-message" => "Lets allow MySQL input"), ) , ) ,
                array ( "Firewall" => array( "allow" => array("firewall-rule" => "3306/tcp" ), ) , ) ,
                array ( "Logging" => array( "log" => array( "log-message" => "Lets enable Firewall again"), ) , ) ,
                array ( "Firewall" => array( "enable" => array(), ) , ) ,

                array ( "Logging" => array( "log" => array( "log-message" => "Configuring a Database Node on environment <%tpl.php%>env_name</%tpl.php%> complete"),),),

        );

    }

}