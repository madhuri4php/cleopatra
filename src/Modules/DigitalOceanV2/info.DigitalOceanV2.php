<?php

Namespace Info;

class DigitalOceanV2Info extends CleopatraBase {

    public $hidden = false;

    public $name = "Digital Ocean Server Management Functions - API Version 2";

    public function _construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "DigitalOceanV2" => array_merge(parent::routesAvailable(), array("save-ssh-key",
          "box-add", "box-remove", "box-destroy", "box-destroy-all", "list") ) );
    }

    public function routeAliases() {
      return array("digitaloceanv2"=>"DigitalOceanV2", "digital-ocean-v2"=>"DigitalOceanV2");
    }

    public function boxProviderName() {
        return "DigitalOceanV2";
    }

    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This is an extension provided for Handling Servers on Digital Ocean.

    DigitalOceanV2, digitaloceanv2, digital-ocean-v2

        - box-add
        Lets you add boxes to Digital Ocean, and adds them to your papyrusfile
        example: cleopatra digital-ocean-v2 box-add
                    --yes
                    --server-prefix=dbomb # common prefix to use for these instances
                    --environment-name=demodave # papyrusfile environment to add instances to - must already exist
                    --box-amount=1 # number of instances to create
                    --size-id=512mb # size of instances
                    --image-id=7111343 # image id to create instances with
                    --region-id=lon1 # region to create instance in
                    --wait-until-active # wait until box has an ip address active before moving to the next one (You
                        usually want this, unless you are asynchronously populating the connection details)

        - box-destroy
        Will destroy box/es in an environment for you, and remove them from the papyrus file
        example: cleopatra digital-ocean-v2 box-destroy --yes --guess --digital-ocean-v2-ssh-key-path="/home/dave/.ssh/bastion.pub" --digital-ocean-v2-ssh-key-name="bastion"

        - box-destroy-all
        Will destroy all boxes in your digital ocean account - Careful - its irreversible
        example: cleopatra digital-ocean-v2 box-destroy-all --yes --guess

        - save-ssh-key
        Will let you save a local ssh key to your Digital Ocean account, so you can ssh in to your nodes
        securely and without a password
        example: cleopatra digital-ocean-v2 save-ssh-key
                    --yes
                    --path="/home/dave/.ssh/bastion.pub"
                    --name="bastion"

        - list
        Will display data about your digital ocean account
        example: cleopatra digital-ocean-v2 list
        example: cleopatra digital-ocean-v2 list --yes
                    --guess # use project saved connection details if possible
                    --type=sizes # droplets, sizes, images, domains, regions, ssh_keys

HELPDATA;
      return $help ;
    }

}