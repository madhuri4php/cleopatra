<?php

Namespace Core ;

class AutoPilotConfigured extends AutoPilot {

    public $steps ;

    public function __construct() {
        $this->setSteps();
    }

    /* Steps */
    private function setSteps() {

        include("settings.php") ;

        $this->steps =
            array(
                // Production Web Nodes
                array ( "Logging" => array( "log" => array( "log-message" => "Lets add the Production Web Nodes Environment" ),),),
                array ( "EnvironmentConfig" => array("configure" => array(
                    "guess" => true,
                    "environment-name" => "medium-prod-web-nodes",
                    "tmp-dir" => "/tmp/",
                    "keep-current-environments" => true,
                    "no-manual-servers" => true,
                    "add-single-environment" => true,
                ),),),
                array ( "Logging" => array( "log" => array( "log-message" => "Lets add the Production Web Nodes Box" ),),),
                array ( "Boxify" => array("box-add" => array(
                    "guess" => true,
                    "environment-name" => "medium-prod-web-nodes",
                    "provider-name" => "$provider_load_balancer",
                    "box-amount" => "$box_amount_load_balancer",
                    "image-id" => "$image_id_load_balancer",
                    "region-id" => "$region_id_load_balancer",
                    "size-id" => "$size_id_load_balancer",
                    "server-prefix" => $prefix,
                    "box-user-name" => "$user_name_load_balancer",
                    "private-ssh-key-path" => "$priv_ssh_key_load_balancer",
                    "wait-for-box-info" => true,
                    "max-box-info-wait-time" => $wait_time,
                    "wait-until-active" => true,
                    "max-active-wait-time" => $wait_time,
                ),),),

                array ( "Logging" => array( "log" => array( "log-message" => "Creating medium-prod-web-nodes environment complete"),),),

            );

    }

}