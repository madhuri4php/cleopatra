<?php

/*************************************
*      Generated Autopilot file      *
*     ---------------------------    *
*Autopilot Generated By Dapperstrano *
*     ---------------------------    *
*************************************/

Namespace Core ;

class AutoPilotConfigured extends AutoPilot {

    public $steps ;

    public function __construct() {
	    $this->setSteps();
        $this->setVHostTemplate();
    }

    /* Steps */
    private function setSteps() {

	    $this->steps =
	      array(
              array ( "Git" => array(
                  "gitDeletorExecute" => true,
                  "gitDeletorCustomFolder" => "/var/www/gcapplications/live/alca-enterprise/alca-enterprise/"
              ), ) ,
              array ( "HostEditor" => array(
                  "hostEditorDeletionExecute" => true,
                  "hostEditorDeletionIP" => "178.63.72.156",
                  "hostEditorDeletionURI" => "www.alca-enterprise.co.uk.local",
              ) , ) ,
              array ( "VHostEditor" => array(
                  "virtualHostEditorDeletionExecute" => "boolean",
                  "virtualHostEditorDeletionDirectory" => "/etc/apache2/sites-available",
                  "virtualHostEditorDeletionTarget" => "www.alca-enterprise.co.uk",
                  "virtualHostEditorDeletionVHostDisable" => false,
                  "virtualHostEditorDeletionSymLinkDirectory" => "/etc/apache2/sites-enabled",
                  "virtualHostEditorDeletionApacheCommand" => "apache2",
              ) , ) ,
              array ( "ApacheControl" => array(
                  "apacheCtlRestartExecute" => true,
              ) , ) ,
	      );

	}

}