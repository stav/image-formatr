<?php
/**
 * generic debug printer
 *
 * Because I didn't like having to pass two arguments to a debug printer, namely the evaluated and un-evaluated expressions, like: $baker->bread and "baker.bread", i.e., I only wanted to pass the un-evaluated string and let the print routine do the evaluating.  This script does that proceduraly, i.e., not in a function, so expression scope is not changed.
 *
 * Usage:
 * <code>
 *   // be careful about single quotes
 *   $debug_sma_title =__METHOD__;
 *   $debug_sma_eval ='$any_expression';
 *   include('debug_output_sma.php');    // include, not include_once
 * </code>
 *
 * Variables:
 *
 * - <var>$debug_sma_title</var> :text description string that gets output in front, use the PHP magic constants if you want
 * - <var>$debug_sma_eval</var>  :the un-evaluated expression passed to us
 * - <var>$e</var>               :the evaluated expression
 * - <var>$tostring</var>        :evaluated expression, array contents, or description
 * - <var>$length</var>          :length of evaluated expression
 *
 * Output:
 *
 * 1. a string title
 * 2. un-evaluated expression
 * 3. evaluated expression length
 * 4. evaluated expression type
 * 5. evaluated expression converted tostring
 *
 * @version 1.4
 * @author Steven Almeroth
 * @note Requires PHP 4
 * @todo eval code should filter errors for undefined vars
 */

  defined('NEWLINE') or define('NEWLINE', chr(10));

  $title = isset($debug_sma_title) ? $debug_sma_title : '';

  eval('$e='."$debug_sma_eval;");

  if (is_resource($e))
  {
    $tostring ="$e\n";
    while ($row = mysql_fetch_assoc($e))
      $tostring .=print_r($row, TRUE);
    $length =mysql_num_rows($e);
    mysql_data_seek($e, 0); // reset pointer
  }
  elseif (is_object($e))
  {
    $tostring ="new ". get_class($e) ." object";
    $length =count($e);
  }
  elseif (is_array($e))
  {
    $tostring =count($e, COUNT_RECURSIVE) ." total elements";
    $length =count($e);
  }
  elseif (is_bool($e))
  {
    $tostring =($e ? 'TRUE' : 'FALSE');
    $length =strlen($e);
  }
  else
  {
    $tostring =str_replace(NEWLINE , '←', $e);
    $length =strlen($e);
  }

 #print "<!--\n";
  printf("\n @ %-25s %-25s%3d (%s) %s\n", $title, $debug_sma_eval, $length, gettype($e), $tostring);
  
  if (is_array($e) || is_object($e))
    print_r($e);
 #print "-->\n";

?>
