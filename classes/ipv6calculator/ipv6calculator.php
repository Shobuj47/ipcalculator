<?php


interface ipv6calculator{
public function validateIpV6($ipaddress);

/**
 * Calculate the number of host available within a CIDR value
 *@author    Mehedi Hasan Sabuz
 *@param     Integer      $cidr       CIDR value of the proposed network
 *@return    Integer                  Total number of host
*/
public function getAvailableHost($cidr);

/**
 *Calculate the necessary CIDR id for a given number of host
 *@author    Mehedi Hasan Sabuz
 *@param     Integer      $hostcount   Total number of host required for a network
 *@return    Integer      $cidr        Returns the CIDR ID 
*/
public function getCidrByHostCount($hostcount);

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
public function getIpV6Range($a_Prefix, $a_WantBins = false);

/**
 * Calculates the host count available within a IP Address Range
 * @author Mehedi Hasan Sabuz
 * @param String      $startIpRange   The starting ip address of the network/range
 * @param String      $endIpRange     The ending ip address of the network/range
 * @return Integer    Total number of host available within the given IPv-6 Address range
 */
public function getAvailableHostCountByRange($startIpRange, $endIpRange);

/**
 * Transforms ipv6 to decimal form
 * @author phpipam
 * @access public
 * @param   String $ipv6    IPv6 Address into string formate (Human Readable form)
 * @return  String          IPv6 Address into decimal form as string
 * @link https://github.com/phpipam/phpipam/blob/master/functions/classes/class.Common.php
 */
public function ip2dec6 ($ipv6);

/**
 * Transforms Decimal format to IPv6 Address as String
 * @author phpipam
 * @access  public
 * @param   String $ipv6long        IPv6 Address into Decimal formate
 * @return  String                  IPv6 Address in Hexadecimal (Human Readable form)
 * @link https://github.com/phpipam/phpipam/blob/master/functions/classes/class.Common.php
 */
public function dec2ip6($ipv6long) ;

/**
 * Validate if the first parameter is lower then the secound parameter
 * @author geeksforgeeks
 * @access public
 * @param   String  $str1       Holds the lowest value for the calculation
 * @param   String  $str2       Holds the largest value for the calculation
 * @return  Boolean             If if the first parameter is lower then the secound parameter then return true else false
 * @link https://www.geeksforgeeks.org/difference-of-two-large-numbers/
 */
public function isSmaller($str1, $str2);

/**
 * Performs subtraction between 2 large number
 * @author geeksforgeeks
 * @access public
 * @param   String  $str1       Holds the lowest value for the calculation
 * @param   String  $str2       Holds the largest value for the calculation
 * @return  String              The difference of the subtraction operation between $str2 and $str1
 * @link https://www.geeksforgeeks.org/difference-of-two-large-numbers/
 */
public function findDiff($str1, $str2);

/**
 * Performs addition between 2 large number
 * @author geeksforgeeks
 * @access public
 * @param   String  $str1       1st Operand
 * @param   String  $str2       2nd Operand
 * @return  Integer             The summation between $str2 and $str1
 * @link https://www.geeksforgeeks.org/difference-of-two-large-numbers/
 */
public function findSum($str1, $str2); 


/**
 * Gets the summation of the number with a ip address
 * @author Mehedi Hasan Sabuz
 * @param String            $ipaddress      IP Address in Hexadecimal form. i.e. "2001:db8:0:0:0:0:0:ff"
 * @param String/Integer    $incrementBy    A Decimal number that needs to be added with the given ip address in @param $ipaddress
 * @return String                           The reuslt of summation between ipaddress  and the number in Hexform (IPv6). i.e. "2001:db8:0:0:0:0:0:ff"
 */
public function ipaddressSum($ipaddress, $incrementBy);

/**
 * Gets the Subtraction by the number with a ip address
 * @author Mehedi Hasan Sabuz
 * @param String            $ipaddress      IP Address in Hexadecimal form. i.e. "2001:db8:0:0:0:0:0:ff"
 * @param String/Integer    $incrementBy    A Decimal number that needs to be subtracted from the given ip address in @param $ipaddress
 * @return String                           The reuslt of subtraction between ipaddress  and the number in Hexform (IPv6). i.e. "2001:db8:0:0:0:0:0:ff"
 */
public function ipaddressSubtract($ipaddress, $decrementBy);

/**
 * Formates a plain Hexadecimal Numbers into formate of IPv6
 * i.e. 20010db8000000000000000000000050 into 2001:0db8:0000:0000:0000:0000:0000:0050
 * @param String    $flatIpv6  Plain Hexadecimal numbers. i.e. 20010db8000000000000000000000050
 * @return String              Formated string. i.e. 2001:0db8:0000:0000:0000:0000:0000:0050
 */
public function formatePlainIpv6($flatIpv6);




}

?>