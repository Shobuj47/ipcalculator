<?php 

    /*
    * Interface
    */
    require 'ipv4calculator.php';


    class ipv4calculatorImpl implements ipv4calculator{

    /**
     * Generates a list of ip address for a given range with a network id.
     *@author    Mehedi Hasan Shobuj  
     *@param     Integer[]    $iprange     Range of IP Address in string or in decimal form
     *@param     Integer      $cidr        CIDR value of the proposed network
     *@return    Integer[]                 Returns a list of ip address in decimal form. 
    */
    public function getHostList($iprange, $cidr){
        $hostList = array();
        //Validate if the ip address is in String form
        if((validateIp($iprange[0])) && (validateIp($iprange[1]))){
            $iprange[0] = ip2dec($iprange[0]);          //If in string form then convert the ip address in decimal form 
            $iprange[1] = ip2dec($iprange[1]);          //If in string form then convert the ip address in decimal form 
            getHostList($iprange, $cidr);               //Recall self with the converted ip address and the cidr value
        //Else if the ip addresses are in decimal form 
        }elseif((validateDecIp($iprange[0])) && (validateDecIp($iprange[1]))){
            if($iprange[0] < $iprange[1]){              //If the starting ip address is lower then the ending ip address then continue.
                while($iprange[0] <= $iprange[1]){      //Loop through untile the starting ip address reaches the ending ip address
                    array_push($hostList, $iprange[0]); //Add the ip address into the Array
                    $iprange[0] = $iprange[0] + 1;      //Increment the ip address by 1
                }
                return $hostList;                       //When the list genaration is complete return the list of ip address.
            }else{
                return null;                            //If the starting ip address is not less then the ending ip address then return null
            }
        }else{                                          //If provided IP Addresses in decimal form is not correct then return null.
            return null;
        }
    }

    /**
     * Calculate the Subnetmask in decimal form for a given CIDR value
     *@author    Mehedi Hasan Shobuj   
     *@param     Integer      $cidr       CIDR value of the proposed network
     *@return    Integer                  Subnet Mask in decimal form 
    */
    public function getSubnet($cidr){
        return long2ip(-1 << (32 - (int)$cidr));
    }

    /**
     * Calculate the Wild Card Mask in decimal form for a given CIDR value
     *@author    Mehedi Hasan Shobuj    
     *@param     String       $subnetmask  Subnet Mask
     *@return    Integer                   Wild Card Mask in Decimal form
    */
    public function getWCMask($subnetmask){
        return long2ip( ~ip2long($subnetmask) );
    }

    /**
     * Calcualte the first and last IP Address with a IP Address and CIDR id
     *@author    Mehedi Hasan Shobuj   
     *@param     Integer      $ipaddress   A ip address which belongs to a network
     *@param     Integer      $cidr        CIDR value of the network
     *@return    Integer[]                 Returns the starting and ending ip address of the network
     *@link      https://stackoverflow.com/questions/4931721/getting-list-ips-from-cidr-notation-in-php
    */
    public function getHostRanges($ipaddress, $cidr){
        $range = array();
        if($this->validateIp($ipaddress)){
            $range[0] = long2ip((ip2long($ipaddress)) & ((-1 << (32 - (int)$cidr))));
            $range[1] = long2ip((ip2long($range[0])) + pow(2, (32 - (int)$cidr)) - 1);
            return $range;
        }else{
            return null;
        }
        
    }

    /**
     * Calculates the CIDR value from a given Subnetmask
     *@author    Mehedi Hasan Shobuj
     *@param     Integer      $subnetmask    Subnetmask of the network
     *@return    Integer                     Returns the CIDR id 
    */
    public function getCidrBySubnetMask($subnetmask){

    }

    /**
     * Calculate the necessary CIDR id for a given number of host
     *@author    Mehedi Hasan Shobuj 
     *@param     Integer      $hostcount   Total number of host required for a network
     *@return    Integer                   Returns the CIDR ID 
    */
    public function getCidrByHostCount($hostcount){
        $bits       = decbin($hostcount);       	//Convert the number to binary number
        $hostBits   = strlen($bits);                // find how many bits it takes to represent it
        $cidr       = 32 - $hostBits;               // Calculate the remaining cidr value
        return $cidr;
    }

    /**
     * Calculate the number of host available within a CIDR value
     *@author    Mehedi Hasan Shobuj
     *@param     Integer      $cidr       CIDR value of the proposed network
     *@return    Integer                  Total number of host
    */
    public function getAvailableHost($cidr){
        $totalHost = pow(2, (32-$cidr));               // Calculate total hosts into the given CIDR value
        return $totalHost;                             // Return the final output
    }

    /**
     * Validate a IP Address if the ip address is correct or not
     *@author    Mehedi Hasan Shobuj   
     *@param     String      $ipaddress   IP Address in String format
     *@return    Boolean                  Return True if given ip address is valid
    */
    public function validateIp($ipaddress){
        if($this->dec2ip($ipaddress)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Validate a Decimal formed IP Address if the ip address is correct or not
     *@author    Mehedi Hasan Shobuj
     *@param     String      $ipaddress   IP Address in String format
     *@return    Boolean                  Return True if given ip address is valid
    */
    public function validateDecIp($ipaddress){
        if(($ipaddress >= 16777216 && $ipaddress <= 2130706431) || ($ipaddress >= 2147549186  && $ipaddress <= 3221225471) || ($ipaddress >= 3221225728 && $ipaddress <= 3758096127)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Calculate IP Address Class
     *@author    Mehedi Hasan Shobuj  
     *@param     Integer      $ipaddress  IP Address in decimal form
     *@return    Char                     IP Address Class in character
    */
    public function getIpClass($ipaddress){
        if($ipaddress >= 16777216 && $ipaddress <= 2130706431){
            return 'A';
        }elseif($ipaddress >= 2147549186  && $ipaddress <= 3221225471){
            return 'B';
        }elseif($ipaddress >= 3221225728 && $ipaddress <= 3758096127){
            return 'C';
        }else{
            return null;
        }
    }


    /**
     * Converts a IP Address to decimal form 
     *@author    Snort Report
     *@copyright (C) 2000-2013 Symmetrix Technologies, LLC. September 9, 2013
     *@license   This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
     *@param     String      $ip            IP Address. i.e. "192.168.5.5"
     *@return    Integer                    Returns the ip address in decimal form. 
     *Collected From :
     *@link      https://fossies.org/dox/snortreport-1.3.4/functions_8php_source.html
    */
    public function ip2dec($ip){
        return (double)(sprintf("%u", ip2long($ip)));
    }

    /**
     * Converts a IP Address from decimal to IP Address String Value
     *@author    Snort Report
     *@copyright (C) 2000-2013 Symmetrix Technologies, LLC. September 9, 2013
     *@license   This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.    
     *@param     Integer      $dec         The decimal form of a IP Address
     *@return    String                    IP Address. i.e. 192.168.5.5
     *Collected From :
     *@link      https://fossies.org/dox/snortreport-1.3.4/functions_8php_source.html
    */
    public function dec2ip ($dec){
        $hex = $this->dec2hex($dec);
        if (strlen($hex) == 7) $hex = "0".$hex;
        $one = hexdec(substr($hex,0,2));
        $two = hexdec(substr($hex,2,2));
        $three = hexdec(substr($hex,4,2));
        $four = hexdec(substr($hex,6,2));
        $ip = $one.".".$two.".".$three.".".$four;
        return ($ip);
    }


    /**
     * Converts a decimal value to Hexadecimal value
     *@author    Snort Report
     *@copyright (C) 2000-2013 Symmetrix Technologies, LLC. September 9, 2013
     *@license   This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.    
     *@param     Integer      $dec         A decimal value
     *@return    Integer                   A Hex Value corresponds to the decimal value
     *Collected From :
     *@link      https://fossies.org/dox/snortreport-1.3.4/functions_8php_source.html
    */
    public function dec2hex($dec) {
        if($dec > 2147483648) {
            $result = dechex($dec - 2147483648);
            $prefix = dechex($dec / 268435456);
            $suffix = substr($result,-7);
            $hex = $prefix.str_pad($suffix, 7, "0000000", STR_PAD_LEFT);
        }
        else {
            $hex = dechex($dec);
        }
        $hex = strtoupper ($hex);
        return($hex);
    }

}
?>