<?php

// The MIT License
// 
// Copyright (c) 2012 +THECHURCH+
// 
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files
// (the "Software"), to deal in the Software without restriction,
// including without limitation the rights to use, copy, modify, merge,
// publish, distribute, sublicense, and/or sell copies of the Software, and
// to permit persons to whom the Software is furnished to do so, subject to
// the following conditions:
// 
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
// IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
// CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
// TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
// SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

/**
 * Database model for the CSV Advanced Fields plugin.
 * 
 * @package Csvaf
 * @version 0.1
 */
class CsvafModel {
  /**
   * The default wordpress fields.
   *
   * @todo    Translatable / use __() somehow.
   * @static
   * @access  public
   * @var     array
   */
  public static $WPFIELDS = array(
    'post_content'   => 'Content'
  , 'post_date'      => 'Date: Y-m-d H:i:s'
  , 'post_date_gmt'  => 'Date GMT: Y-m-d H:i:s'
  , 'post_excerpt'   => 'Excerpt'
  , 'post_name'      => 'Name'
  , 'post_password'  => 'Password'
  , 'post_title'     => 'Title'
  );

  /**
   * @static
   * @access  public
   * @var     array
   */
  public static $ACFFIELDS = array(
    'text',   'textarea', 'editor'
  , 'select', 'date'
  );

  /**
   * Get the available post types.
   * 
   * @static
   * @access  public
   * @return  array  The post types
   */
  public static function Getposttypes () {
    return get_post_types();
  }

  /**
   * Get fields for post type.
   *
   * If the advanced custom field plugin is installed then also include these
   * fields.
   * 
   * @static
   * @access  public
   * @param   string  $posttype 
   * @return  array   The fields.
   */
  public static function Getfieldsfortype ($posttype) {
    global $acf;
    $fields   = array();

    // Normal fields
    foreach (self::$WPFIELDS as $key => $name) {
      $fields[] = array(
        'advanced'  => false
      , 'id'        => $key
      , 'name'      => $name
      , 'key'       => $key
      );
    }

    // Advanced custom fields
    if (array_key_exists('acf', $GLOBALS)) {
      $fieldgroups  = $acf->get_field_groups();

      foreach ($fieldgroups as $fieldgroup) {
        $rules  = $fieldgroup['location']['rules'];
        $passes = false;

        foreach ($rules as $rule) {
          if (
             'post_type' == $rule['param']
          && '==' == $rule['operator']
          && $posttype == $rule['value']
          ) {
            $passes = true;
            break;
          }
        }

        if ($passes) {
          foreach ($fieldgroup['fields'] as $field) {
            if (!in_array($field['type'], self::$ACFFIELDS)) continue;

            $fields[]     = array(
              'advanced'  => true
            , 'id'        => $field['key']
            , 'name'      => $field['label']
            , 'key'       => $field['name']
            );
          }
        }
      }
    }

    var_dump($fields);
  }
}
