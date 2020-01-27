<?php
interface ipv4calculator{
    public function getHostList($iprange, $cidr);
    public function getSubnet($cidr);
    public function getWCMask($cidr);
    public function getHostRanges($ipaddress, $cidr);
    public function getCidrBySubnetMask($subnetmask);
    public function getCidrByHostCount($hostcount);
    public function getAvailableHost($cidr);
    public function validateIp($ipaddress);
    public function validateDecIp($ipaddress);
    public function getIpClass($ipaddress);
    public function ip2dec($ip);
    public function dec2IP ($dec);
    public function dec2hex($dec);
}

?>