<?php 

require 'ipv6calculator.php';

class ipv6calculatorImpl implements ipv6calculator{

    /**
     * Validates a IPV-6 Address 
     * @author Mehedi Hasan Sabuz
     * @param  String   $ipaddress      IPV6 as string. i.e. "2001:0db8:0000:0000:0000:0000:0000:0001"
     * @return Boolean                  True if the IPV-6 Address is valid
     * @link https://www.w3schools.com/php/filter_validate_ip.asp
    */
    public function validateIpV6($ipaddress){
        if (filter_var($ipaddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Calculate the number of host available within a CIDR value
     *@author    Mehedi Hasan Sabuz
     *@param     Integer      $cidr       CIDR value of the proposed network
     *@return    Integer                  Total number of host
    */
    public function getAvailableHost($cidr){
        if($cidr > 0 && $cidr < 128){
            $totalHost = pow(2, (128-$cidr));               // Calculate total hosts into the given CIDR value
            return $totalHost;                             // Return the final output
        }
    }

    /**
     *Calculate the necessary CIDR id for a given number of host
     *@author    Mehedi Hasan Sabuz
     *@param     Integer      $hostcount   Total number of host required for a network
     *@return    Integer      $cidr        Returns the CIDR ID 
    */
    public function getCidrByHostCount($hostcount){
        $bits       = decbin($hostcount);       	//Convert the number to binary number
        $hostBits   = strlen($bits);                // find how many bits it takes to represent it
        $cidr       = 128 - $hostBits;               // Calculate the remaining cidr value
        return $cidr;
    }

    /**
     * This function generates the starting and ending IP Address for a given IP Address along with CIDR Notation
     *@author Mehedi Hasan Sabuz
     *@param string $a_Prefix       Contains the IP Address along with the CIDR notation. i.e. "2001:0db8:0000:0000:0000:0000:0000:0001/125"
     *@param bool $a_WantBins
     *@return object                Array of the subnet details. Includes: Starting IP, Ending IP, Mask Binary
     *Collected From
     *@link https://stackoverflow.com/questions/10085266/php5-calculate-ipv6-range-from-cidr-prefix
     *@link https://stackoverflow.com/users/1451110/codeangry
    */
    public function getIpV6Range($a_Prefix, $a_WantBins = false){
        // Validate input superficially with a RegExp and split accordingly
        if(!preg_match('~^([0-9a-f:]+)[[:punct:]]([0-9]+)$~i', trim($a_Prefix), $v_Slices)){
            return false;
        }
        // Make sure we have a valid ipv6 address
        if(!filter_var($v_FirstAddress = $v_Slices[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
            return false;
        }
        // The /## end of the range
        $v_PrefixLength = intval($v_Slices[2]);
        if($v_PrefixLength > 128){
            return false; // kind'a stupid :)
        }
        $v_SuffixLength = 128 - $v_PrefixLength;
    
        // Convert the binary string to a hexadecimal string
        $v_FirstAddressBin = inet_pton($v_FirstAddress);
        $v_FirstAddressHex = bin2hex($v_FirstAddressBin);
    
        // Build the hexadecimal string of the network mask
        // (if the manually formed binary is too large, base_convert() chokes on it... so we split it up)
        $v_NetworkMaskHex = str_repeat('1', $v_PrefixLength) . str_repeat('0', $v_SuffixLength);
        $v_NetworkMaskHex_parts = str_split($v_NetworkMaskHex, 8);
        foreach($v_NetworkMaskHex_parts as &$v_NetworkMaskHex_part){
            $v_NetworkMaskHex_part = base_convert($v_NetworkMaskHex_part, 2, 16);
            $v_NetworkMaskHex_part = str_pad($v_NetworkMaskHex_part, 2, '0', STR_PAD_LEFT);
        }
        $v_NetworkMaskHex = implode(null, $v_NetworkMaskHex_parts);
        unset($v_NetworkMaskHex_part, $v_NetworkMaskHex_parts);
        $v_NetworkMaskBin = inet_pton(implode(':', str_split($v_NetworkMaskHex, 4)));
    
        // We have the network mask so we also apply it to First Address
        $v_FirstAddressBin &= $v_NetworkMaskBin;
        $v_FirstAddressHex = bin2hex($v_FirstAddressBin);
    
        // Convert the last address in hexadecimal
        $v_LastAddressBin = $v_FirstAddressBin | ~$v_NetworkMaskBin;
        $v_LastAddressHex =  bin2hex($v_LastAddressBin);
    
        // Return a neat object with information
        /*$v_Return = array(
            'Prefix'    => "{$v_FirstAddress}/{$v_PrefixLength}",
            'FirstHex'  => $this->formatePlainIpv6($v_FirstAddressHex),
            'LastHex'   => $this->formatePlainIpv6($v_LastAddressHex),
            'MaskHex'   => $v_NetworkMaskHex,
        );
         // Bins are optional...
         if($a_WantBins){
            $v_Return = array_merge($v_Return, array(
                'FirstBin'  => $v_FirstAddressBin,
                'LastBin'   => $v_LastAddressBin,
                'MaskBin'   => $v_NetworkMaskBin,
            ));
        }
        */
        $v_Return = array(
            "{$v_FirstAddress}/{$v_PrefixLength}",
            $this->formatePlainIpv6($v_FirstAddressHex),
            $this->formatePlainIpv6($v_LastAddressHex),
            $v_NetworkMaskHex
        );
        if($a_WantBins){
            $v_Return = array_merge($v_Return, array(
                $v_FirstAddressBin,
                $v_LastAddressBin,
                $v_NetworkMaskBin,
            ));
        }

        return $v_Return;
    }


    /**
     * Calculates the host count available within a IP Address Range
     * @author Mehedi Hasan Sabuz
     * @param String      $startIpRange   The starting ip address of the network/range
     * @param String      $endIpRange     The ending ip address of the network/range
     * @return Integer    Total number of host available within the given IPv-6 Address range
     */
    public function getAvailableHostCountByRange($startIpRange, $endIpRange){
        if(!empty($startIpRange) && !empty($endIpRange)){
            $startIpRange = $this->ip2dec6($startIpRange);       //TODO : Convert the Host part of the IP Address into Decimal
            $endIpRange = $this->ip2dec6($endIpRange);         //TODO : Convert the Host part of the IP Address into Decimal
            if($startIpRange < $endIpRange){
                return $this->findDiff($startIpRange, $endIpRange);
            }
        }
    }

    /**
	 * Transforms ipv6 to decimal form
	 * @author phpipam
	 * @access public
	 * @param   String $ipv6    IPv6 Address into string formate (Human Readable form)
	 * @return  String          IPv6 Address into decimal form as string
     * @link https://github.com/phpipam/phpipam/blob/master/functions/classes/class.Common.php
	 */
	public function ip2dec6 ($ipv6) {
        if($this->validateIpV6($ipv6) != true) {
			return false;
		}
	    $ip_n = inet_pton($ipv6);
	    $bits = 15; // 16 x 8 bit = 128bit
	    $ipv6long = "";

	    while ($bits >= 0)
	    {
	        $bin = sprintf("%08b",(ord($ip_n[$bits])));
	        $ipv6long = $bin.$ipv6long;
	        $bits--;
        }
        $result = gmp_strval(gmp_init($ipv6long,2),10);
        return $result;
	}

    /**
	 * Transforms Decimal format to IPv6 Address as String
	 * @author phpipam
	 * @access  public
	 * @param   String $ipv6long        IPv6 Address into Decimal formate
	 * @return  String                  IPv6 Address in Hexadecimal (Human Readable form)
     * @link https://github.com/phpipam/phpipam/blob/master/functions/classes/class.Common.php
	 */
	public function dec2ip6($ipv6long) {
        $ipv6long = strval($ipv6long);
		$hex = sprintf('%032s', gmp_strval(gmp_init($ipv6long, 10), 16));
		$ipv6 = implode(':', str_split($hex, 4));
		// compress result
		return inet_ntop(inet_pton($ipv6));
	}

    /**
     * Validate if the first parameter is lower then the secound parameter
     * @author geeksforgeeks
     * @access public
     * @param   String  $str1       Holds the lowest value for the calculation
     * @param   String  $str2       Holds the largest value for the calculation
     * @return  Boolean             If if the first parameter is lower then the secound parameter then return true else false
     * @link https://www.geeksforgeeks.org/difference-of-two-large-numbers/
     */
    public function isSmaller($str1, $str2) { 
        // Calculate lengths of both string 
        $n1 = strlen($str1);  
        $n2 = strlen($str2); 
      
        if ($n1 < $n2) 
            return true; 
        if ($n2 > $n1) 
            return false; 
      
        for ($i = 0; $i < $n1; $i++) 
        { 
            if ($str1[$i] < $str2[$i]) 
                return true; 
            else if ($str1[$i] > $str2[$i]) 
                return false; 
        } 
        return false; 
    } 

    /**
     * Performs subtraction between 2 large number
     * @author geeksforgeeks
     * @access public
     * @param   String  $str1       Holds the lowest value for the calculation
     * @param   String  $str2       Holds the largest value for the calculation
     * @return  String              The difference of the subtraction operation between $str2 and $str1
     * @link https://www.geeksforgeeks.org/difference-of-two-large-numbers/
     */
    public function findDiff($str1, $str2) { 
        // Before proceeding further, make  
        // sure str1 is not smaller 
        if ($this->isSmaller($str1, $str2)) 
        { 
            $t = $str1; 
            $str1 = $str2; 
            $str2 = $t; 
        } 
    
        // Take an empty string for storing result 
        $str = ""; 
    
        // Calculate lengths of both string 
        $n1 = strlen($str1);  
        $n2 = strlen($str2); 
        $diff = $n1 - $n2; 
    
        // Initially take carry zero 
        $carry = 0; 
    
        // Traverse from end of both strings 
        for ($i = $n2 - 1; $i >= 0; $i--) 
        { 
            // Do school mathematics, compute  
            // difference of current digits and carry 
            $sub = ((ord($str1[$i + $diff]) - ord('0')) -  
                    (ord($str2[$i]) - ord('0')) - $carry); 
            if ($sub < 0) 
            { 
                $sub = $sub + 10; 
                $carry = 1; 
            } 
            else
                $carry = 0; 
    
            $str.=chr($sub + ord("0")); 
        } 
    
        // subtract remaining digits of str1[] 
        for ($i = $n1 - $n2 - 1; $i >= 0; $i--) 
        { 
            if ($str1[$i] == '0' && $carry > 0) 
            { 
                $str.="9"; 
                continue; 
            } 
            $sub = (ord($str1[$i]) - ord('0') - $carry); 
            if ($i > 0 || $sub > 0) // remove preceding 0's 
                $str.=chr($sub + ord("0")); 
            $carry = 0; 
    
        } 
    
        // reverse resultant string 
        return strval(strrev($str)); 
  
    } 

    /**
     * Performs addition between 2 large number
     * @author geeksforgeeks
     * @access public
     * @param   String  $str1       1st Operand
     * @param   String  $str2       2nd Operand
     * @return  Integer             The summation between $str2 and $str1
     * @link https://www.geeksforgeeks.org/difference-of-two-large-numbers/
     */
    public function findSum($str1, $str2)  { 
        // Before proceeding further, make  
        // sure length of str2 is larger.  
        if(strlen($str1)> strlen($str2)) 
        {  
            $temp = $str1;  
            $str1 = $str2;  
            $str2 = $temp; 
        } 
    
        // Take an empty string for storing result  
        $str3 = "";  
    
        // Calculate length of both string  
        $n1 = strlen($str1);  
        $n2 = strlen($str2);  
        $diff = $n2 - $n1;  
    
        // Initially take carry zero  
        $carry = 0; 
    
        // Traverse from end of both strings  
        for ($i = $n1 - 1; $i >= 0; $i--)  
        { 
            // Do school mathematics, compute sum   
            // of current digits and carry  
            $sum = ((ord($str1[$i]) - ord('0')) +  
                ((ord($str2[$i + $diff]) -  
                    ord('0'))) + $carry);  
        
            $str3 .= chr($sum % 10 + ord('0'));  
            
            
            $carry = (int)($sum / 10); 
        } 
    
        // Add remaining digits of str2[] 
        for ($i = $n2 - $n1 - 1; $i >= 0; $i--)  
        { 
            $sum = ((ord($str2[$i]) - ord('0')) + $carry);  
            $str3 .= chr($sum % 10 + ord('0'));  
            $carry = (int)($sum / 10); 
        } 
    
        // Add remaining carry  
        if ($carry)  
            $str3 .= chr($carry + ord('0'));  
    
        // reverse resultant string  
        return strrev($str3);  
    }   


    /**
     * Gets the summation of the number with a ip address
     * @author Mehedi Hasan Sabuz
     * @param String            $ipaddress      IP Address in Hexadecimal form. i.e. "2001:db8:0:0:0:0:0:ff"
     * @param String/Integer    $incrementBy    A Decimal number that needs to be added with the given ip address in @param $ipaddress
     * @return String                           The reuslt of summation between ipaddress  and the number in Hexform (IPv6). i.e. "2001:db8:0:0:0:0:0:ff"
     */
    public function ipaddressSum($ipaddress, $incrementBy){
        $incrementBy = strval($incrementBy);
        $decimalIpAddr = $this->ip2dec6($ipaddress);
        $decimalIpAddr = $this->findSum($decimalIpAddr, $incrementBy);
        return $this->dec2ip6($decimalIpAddr);
    }

    /**
     * Gets the Subtraction by the number with a ip address
     * @author Mehedi Hasan Sabuz
     * @param String            $ipaddress      IP Address in Hexadecimal form. i.e. "2001:db8:0:0:0:0:0:ff"
     * @param String/Integer    $incrementBy    A Decimal number that needs to be subtracted from the given ip address in @param $ipaddress
     * @return String                           The reuslt of subtraction between ipaddress  and the number in Hexform (IPv6). i.e. "2001:db8:0:0:0:0:0:ff"
     */
    public function ipaddressSubtract($ipaddress, $decrementBy){
        $decrementBy = strval($decrementBy);
        $decimalIpAddr = $this->ip2dec6($ipaddress);
        $decimalIpAddr = $this->findDiff($decrementBy, $decimalIpAddr);
        return $this->dec2ip6($decimalIpAddr);
    }

    /**
     * Formates a plain Hexadecimal Numbers into formate of IPv6
     * i.e. 20010db8000000000000000000000050 into 2001:0db8:0000:0000:0000:0000:0000:0050
     * @param String    $flatIpv6  Plain Hexadecimal numbers. i.e. 20010db8000000000000000000000050
     * @return String              Formated string. i.e. 2001:0db8:0000:0000:0000:0000:0000:0050
     */
    public function formatePlainIpv6($flatIpv6){
        return implode(":", str_split($flatIpv6, 4));
    }

}

?>