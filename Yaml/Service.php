<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfYaml class.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: Service.php 1568 2010-12-07 22:53:07Z olivier.madre $
 */
class Yaml_Service
{
  /**
   * Load YAML into a PHP array statically
   *
   * The load method, when supplied with a YAML stream (string or file),
   * will do its best to convert YAML in a file into a PHP array.
   *
   *  Usage:
   *  <code>
   *   $array = sfYAML::Load('config.yml');
   *   print_r($array);
   *  </code>
   *
   * @param string $input Path of YAML file or string containing YAML
   *
   * @return array
   */
  public static function load($input)
  {
    $file = '';

    // if input is a file, process it
    if (strpos($input, "\n") === false && is_file($input))
    {
      $file = $input;

      ob_start();
      $retval = include($input);
      $content = ob_get_clean();

      // if an array is returned by the config file assume it's in plain php form else in yaml
      $input = is_array($retval) ? $retval : $content;
    }

    // if an array is returned by the config file assume it's in plain php form else in yaml
    if (is_array($input))
    {
      return $input;
    }


    $yaml = new Yaml_Parser();

    try
    {
      $ret = $yaml->parse($input);
    }
    catch (Exception $e)
    {
      throw new InvalidArgumentException(sprintf('Unable to parse %s: %s', $file ? sprintf('file "%s"', $file) : 'string', $e->getMessage()));
    }

    return $ret;
  }

  /**
   * Dump YAML from PHP array statically
   *
   * The dump method, when supplied with an array, will do its best
   * to convert the array into friendly YAML.
   *
   * @param array $array PHP array
   *
   * @return string
   */
  public static function dump($array, $inline = 2)
  {

    $yaml = new Yaml_Dumper();

    return $yaml->dump($array, $inline);
  }
}

/**
 * Wraps echo to automatically provide a newline.
 *
 * @param string The string to echo with new line
 */
function echoln($string)
{
  echo $string."\n";
}