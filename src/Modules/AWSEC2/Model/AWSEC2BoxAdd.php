<?php

Namespace Model;

class AWSEC2BoxAdd extends BaseAWSEC2AllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("BoxAdd") ;

    public function askWhetherToBoxAdd($params=null) {
        return $this->addBox($params);
    }

    public function addBox() {
        if ($this->askForOverwriteExecute() != true) { return false; }
        $this->accessKey = $this->askForAWSEC2AccessKey();
        $this->secretKey = $this->askForAWSEC2SecretKey();
        $serverPrefix = $this->getServerPrefix();
        $environments = \Model\AppConfig::getProjectVariable("environments");
        $workingEnvironment = $this->getWorkingEnvironment();

        foreach ($environments as $environment) {
            if ($environment["any-app"]["gen_env_name"] == $workingEnvironment) {
                $environmentExists = true ; } }

        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);

        if (isset($environmentExists)) {
            foreach ($environments as $environment) {
                if ($environment["any-app"]["gen_env_name"] == $workingEnvironment) {
                    $envName = $environment["any-app"]["gen_env_name"];

                    if (isset($this->params["yes"]) && $this->params["yes"]==true) {
                        $addToThisEnvironment = true ; }
                    else {
                        $question = 'Add AWS EC2 Server Boxes to '.$envName.'?';
                        $addToThisEnvironment = self::askYesOrNo($question); }

                    if ($addToThisEnvironment == true) {
                        for ($i = 0; $i < $this->getServerGroupBoxAmount(); $i++) {
                            $serverData = array();
                            $serverData["prefix"] = $serverPrefix ;
                            $serverData["envName"] = $envName ;
                            $serverData["sCount"] = $i ;
                            $serverData["sizeID"] = $this->getServerGroupSizeID() ;
                            $serverData["imageID"] = $this->getServerGroupImageID() ;
                            $serverData["regionID"] = $this->getServerGroupRegionID() ;
                            $serverData["name"] = (isset( $serverData["prefix"]) && strlen( $serverData["prefix"])>0)
                                ? $serverData["prefix"].'-'.$serverData["envName"].'-'.$serverData["sCount"]
                                : $serverData["envName"].'-'.$serverData["sCount"] ;
                            $response = $this->getNewServerFromAWSEC2($serverData) ;
                            // var_dump("response", $response) ;
                            $this->addServerToPapyrus($envName, $response); } } } }

                return (isset($callsReturned)) ? $callsReturned : null ; }
        else {
            $logging->log("The environment $workingEnvironment does not exist.") ; }

//        $envConfig = new EnvironmentConfig();
//        $envConfig->environments = $environments ;
//        $envConfig->writeEnvsToProjectFile();
//        return $envConfig->environments ;
    }

    private function askForOverwriteExecute() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Add AWS EC2 Server Boxes?';
        return self::askYesOrNo($question);
    }

    private function getServerPrefix() {
        if (isset($this->params["aws-ec2-server-prefix"])) {
            return $this->params["aws-ec2-server-prefix"] ; }
        $question = 'Enter Prefix for all Servers (None is fine)';
        return self::askForInput($question);
    }

    private function getWorkingEnvironment() {
        if (isset($this->params["aws-ec2-environment-name"])) {
            return $this->params["aws-ec2-environment-name"] ; }
        $question = 'Enter Environment to add Servers to';
        return self::askForInput($question);
    }

    private function getServerGroupImageID() {
        if (isset($this->params["aws-ec2-image-id"])) {
            return $this->params["aws-ec2-image-id"] ; }
        $question = 'Enter Image ID for this Server Group';
        return self::askForInput($question, true);
    }

    private function getServerGroupSizeID() {
        if (isset($this->params["aws-ec2-size-id"])) {
            return $this->params["aws-ec2-size-id"] ; }
        $question = 'Enter size ID for this Server Group';
        return self::askForInput($question, true);
    }

    private function getServerGroupRegionID() {
        if (isset($this->params["aws-ec2-region-id"])) {
            return $this->params["aws-ec2-region-id"] ; }
        $question = 'Enter Region ID for this Server Group';
        return self::askForInput($question, true);
    }

    private function getServerGroupBoxAmount() {
        if (isset($this->params["aws-ec2-box-amount"])) {
            return $this->params["aws-ec2-box-amount"] ; }
        $question = 'Enter number of boxes to add to Environment';
        return self::askForInput($question, true);
    }

    private function getUsernameOfBox($boxName = null) {
        if (isset($this->params["aws-ec2-box-user-name"])) {
            return $this->params["aws-ec2-box-user-name"] ; }
        if (isset($this->params["aws-ec2-box-username"])) {
            return $this->params["aws-ec2-box-username"] ; }
        $question = (isset($boxName))
            ? 'Enter SSH username of box '.$boxName
            : 'Enter SSH username of remote box';
        return self::askForInput($question, true);
    }

    private function getSSHKeyLocation() {
        if (isset($this->params["aws-ec2-private-ssh-key-path"])) {
            return $this->params["aws-ec2-private-ssh-key-path"] ; }
        $question = 'Enter file path of private SSH Key';
        return self::askForInput($question, true);
    }

    private function getNewServerFromAWSEC2($serverData) {

        $result = $client->runInstances(array(
                'DryRun' => true || false,
                // ImageId is required
                'ImageId' => 'string',
                // MinCount is required
                'MinCount' => integer,
                // MaxCount is required
                'MaxCount' => integer,
                'KeyName' => 'string',
                'SecurityGroups' => array('string' ),
                'SecurityGroupIds' => array('string' ),
                'UserData' => 'string',
                'InstanceType' => 'string',
                'Placement' => array(
                        'AvailabilityZone' => 'string',
                        'GroupName' => 'string',
                        'Tenancy' => 'string',
                    ),
                'KernelId' => 'string',
                'RamdiskId' => 'string',
                'BlockDeviceMappings' => array(
                        array(
                            'VirtualName' => 'string',
                            'DeviceName' => 'string',
                            'Ebs' => array(
                                'SnapshotId' => 'string',
                                'VolumeSize' => integer,
                                'DeleteOnTermination' => true || false,
                                'VolumeType' => 'string',
                                'Iops' => integer,
                            ),
                            'NoDevice' => 'string',
                        ),
                        // ... repeated
                    ),
                'Monitoring' => array(
                        // Enabled is required
                        'Enabled' => true || false,
                    ),
                'SubnetId' => 'string',
                'DisableApiTermination' => true || false,
                'InstanceInitiatedShutdownBehavior' => 'string',
                'PrivateIpAddress' => 'string',
                'ClientToken' => 'string',
                'AdditionalInfo' => 'string',
                'NetworkInterfaces' => array(
                        array(
                            'NetworkInterfaceId' => 'string',
                            'DeviceIndex' => integer,
                            'SubnetId' => 'string',
                            'Description' => 'string',
                            'PrivateIpAddress' => 'string',
                            'Groups' => array('string' ),
                        'DeleteOnTermination' => true || false,
                        'PrivateIpAddresses' => array(
                            array(
                                // PrivateIpAddress is required
                                'PrivateIpAddress' => 'string',
                                'Primary' => true || false,
                            ),
                            // ... repeated
                        ),
                        'SecondaryPrivateIpAddressCount' => integer,
                        'AssociatePublicIpAddress' => true || false,
                    ),
                    // ... repeated
                ),
                'IamInstanceProfile' => array(
                        'Arn' => 'string',
                        'Name' => 'string',
                    ),
                'EbsOptimized' => true || false,
            ));

        $callVars = array() ;
        $callVars["name"] = $serverData["name"];
        $callVars["size_id"] = $serverData["sizeID"];
        $callVars["image_id"] = $serverData["imageID"];
        $callVars["region_id"] = $serverData["regionID"];
        $callVars["ssh_key_ids"] = $this->getAllSshKeyIdsString();
        $curlUrl = "https://api.awsec2.com/droplets/new" ;
        $callOut = $this->awsCall($callVars, $curlUrl);
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $logging->log("Request for {$callVars["name"]} complete") ;
        return $callOut ;
    }

    private function addServerToPapyrus($envName, $data) {
        $environments = \Model\AppConfig::getProjectVariable("environments");
        var_dump($data->droplet);
        $dropletData = $this->getDropletData($data->droplet->id);
        $server = array();
        $server["target"] = $dropletData->ip_address;
        $server["user"] = $this->getUsernameOfBox($data->name) ;
        $server["password"] = $this->getSSHKeyLocation() ;
        $server["provider"] = "AWSEC2";
        $server["id"] = $data->droplet->id;
        $server["name"] = $data->droplet->name;
        for ($i= 0 ; $i<count($environments); $i++) {
            if ($environments[$i]["any-app"]["gen_env_name"] == $envName) {
                $environments[$i]["servers"][] = $server; } }
        \Model\AppConfig::setProjectVariable("environments", $environments);
    }

    private function getAllSshKeyIdsString() {
        if (isset($this->params["aws-ec2-ssh-key-ids"])) {
            return $this->params["aws-ec2-ssh-key-ids"] ; }
        $curlUrl = "https://api.awsec2.com/ssh_keys" ;
        $sshKeysObject =  $this->awsCall(array(), $curlUrl);
        $sshKeys = array();
        foreach($sshKeysObject->ssh_keys as $sshKey) {
            $sshKeys[] = $sshKey->name ; }
        $keysString = implode(",", $sshKeys) ;
        return $keysString;
    }

    private function getDropletData($dropletId) {
        $curlUrl = "https://api.awsec2.com/droplets/$dropletId" ;
        $dropletObject =  $this->awsCall(array(), $curlUrl);
        return $dropletObject;
    }

}