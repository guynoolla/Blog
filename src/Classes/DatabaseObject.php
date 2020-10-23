<?php
declare(strict_types=1);

namespace App\Classes;

/**
 * Class DatabaseObject
 * 
 * This is the Active Record implementation parent Class
 * It simplifies database interaction and data manipulation
 * Some of its methods specify rules for child classes
 */
class DatabaseObject {

  static protected $database;
  static protected $table_name;
  static protected $columns = [];
  public $errors = [];

  /**
   * Set Mysqli object
   *
   * @param \Mysqli $database
   * @return void
   */
  static public function setDatabase(\Mysqli $database) {
    self::$database = $database;
  }

  /**
   * Child class must have attributes validation method
   * 
   * @return boolean
   */
  protected function validate() {
    # add custom validations
    # ...
    return (empty($this->errors) == true);
  }

  /**
   * Child class have access to attributes before validation
   *
   * @param array $attr
   * @return array
   */
  protected function beforeValidation(array $attr) {
    # run custom before validation
    # ...
    return $attr;
  }

  /**
   * Child class have access to attributes before save
   *
   * @param array $attr
   * @return array
   */
  protected function beforeSave(array $attr) {
    # run custom beforeSave method
    # ...
    return $attr;
  }

  /**
   * Execute the sql and get rows of Class attributes
   * Instantiate Class object for every row of data 
   *
   * @param string $sql
   * @return object[]
   */
  static public function findBySql(string $sql) {
    $result = self::$database->query($sql);
    if (!$result) {
      exit("Database query failed. Query: " . $sql);
    }
    $object_array = [];
    while($record = $result->fetch_assoc()) {
      $object_array[] = static::instantiate($record);
    }
    $result->free();

    return $object_array;
  }

  /**
   * Instantiate Class object with attributes
   * that function become in array as argument
   *
   * @param array $record
   * @return Object
   */
  static protected function instantiate(array $record) {
    $object = new static;

    foreach ($record as $property => $value) {
      if (property_exists($object, $property)) {
        $object->{$property} = $value;
      }
    }

    return $object;
  }

  /**
   * Get all Class objects in array
   * 
   * @param array $order
   * @return object[]
   */
  static public function findAll(array $order=[]) {
    $sql = "SELECT * FROM " . static::$table_name;
    if (!empty($order)) {
      $sql .= " ORDER BY " . key($order) . " " . $order[key($order)];
    }
    return static::findBySql($sql);
  }

  /**
   * Get Class object by its ID attribute
   *
   * @param int $id
   * @return object | error
   */  
  static public function findById(int $id) {
    $sql = "SELECT * FROM " . static::$table_name;
    $sql .= " WHERE id='" . self::escape($id) . "'";
    $obj_array = static::findBySql($sql);
    if (!empty($obj_array)) {
      return array_shift($obj_array);
    } else {
      return false;
    }
  }

  /**
   * Execute create for new object or update for existing one
   *
   * @return boolean
   */
  public function save() {
    if (isset($this->id)) {
      return $this->update();
    } else {
      return $this->create();
    }
  }

  /**
   * Insert new object attributes row into database
   *
   * @return boolean
   */
  protected function create() {
    $attributes = $this->attributes();
    $attributes = $this->beforeValidation($attributes);

    if (!$this->validate()) return false;

    $attributes = $this->beforeSave($attributes);

    $into = [];
    $values = [];
    foreach ($attributes as $key => $value) {
      if ($value == "") continue;
      $into[] = $key;
      $values[] = self::escape($value);
    }

    $sql = "INSERT INTO " . static::$table_name . "(";
    $sql .= join(', ', array_values($into));
    $sql .= ") VALUES ('";
    $sql .= join("', '", array_values($values));
    $sql .= "')";
    $result = self::$database->query($sql);

    if ($result) $this->id = self::$database->insert_id;

    return $result;
  }

  /**
   * Update existing object attributes row in database
   *
   * @return boolean
   */
  protected function update() {
    $attributes = $this->attributes();
    $attributes = $this->beforeValidation($attributes);
  
    if (!$this->validate()) return false;

    $attributes = $this->beforeSave($attributes);

    $attribute_pairs = [];
    foreach ($attributes as $key => $value) {
      if ($value == "") {
        continue;
      } elseif ($value === 'NULL') {
        $attribute_pairs[] = "$key = NULL";
      } 
      else { $attribute_pairs[] = "$key = '" . self::escape($value) . "'"; }
    }

    $sql = "UPDATE " . static::$table_name . " SET ";
    $sql .= join(', ', $attribute_pairs);
    $sql .= " WHERE id='" . self::escape($this->id) . "'";
    $sql .= " LIMIT 1";
    $result = self::$database->query($sql);

    return $result;
  }

  /**
   * Get object attributes
   * Ignore attributes automatically created in database
   *
   * @return array
   */
  public function attributes() {
    $attributes = [];
    foreach (static::$db_columns as $column) {
      $ignore_columns = ['id','created_at','updated_at'];
      if (in_array($column, $ignore_columns)) { continue; }
      $attributes[$column] = $this->$column;
    }
    return $attributes;
  }

  /**
   * Merge object attributes with provided arguments
   *
   * @param array $args
   * @return void
   */
  public function mergeAttributes(array $args=[]) {
    foreach ($args as $key => $value) {
      if (property_exists($this, $key) && !is_null($value)) {
        $this->$key = $value;
      }
    }
  }

  /**
   * Escape attribute value executing in sql
   *
   * @param $value
   * @return string
   */
  static protected function escape($value) {
    $value = strval($value);
    return self::$database->escape_string($value);
  }

  /**
   * Delete object data from database
   *
   * @return boolean
   */
  public function delete() {
    $sql = "DELETE FROM " . static::$table_name;
    $sql .= " WHERE id='" . self::escape($this->id) . "'";
    $sql .= " LIMIT 1";
    $result = self::$database->query($sql);
    return $result;
  }

  /**
   * Get array of objects on where condition
   *
   * @param array $where
   * @param string $append
   * @return object[]
   */
  static public function findWhere(array $where=[], string $append="") {
    $sql = "SELECT * FROM " . static::$table_name;
    $sql = self::concatWhereToSql($sql, $where);
    if ($append != "") $sql .= " {$append}";

    return static::findBySql($sql);
  }

  /**
   * Count number of rows in database
   *
   * @param array $where
   * @return string
   */
  static public function countAll(array $where=[]) {
    $sql = "SELECT COUNT(*) FROM " . static::$table_name;
    $sql = self::concatWhereToSql($sql, $where);
    $result_set = self::$database->query($sql);
    $row = $result_set->fetch_array();
    $result_set->free();
    return array_shift($row);
  }

  /**
   * Concatenate to 'sql' string 'where conditions'
   *
   * @param string $sql
   * @param array $where
   * @return string
   */
  static protected function concatWhereToSql(string $sql, array $where) {
    if (!empty($where)) {
      $i = 0;
      foreach ($where as $column => $value) {
        if (!is_int($column)) {
          if (!is_array($value)) {
            if ($i > 0) {
              $sql .= " AND $column = '" . self::escape($value) . "'";
            } else {
              $sql .= " WHERE $column = '" . self::escape($value) . "'";
            }
          } else {
            foreach ($value as $operator => $ivalue) {
              if ($i > 0) {
                $sql .= " AND $column $operator '" . self::escape($ivalue) . "'";
              } else {
                $sql .= " WHERE $column $operator '" . self::escape($ivalue) . "'";
              }
            }
          }
        } else {
          $q = "";
          $arr = [];
          foreach ($value as $ykey => $yvalue) {
            if ($ykey == 0) {
              $q = str_replace('%', '%%', $yvalue);
              $q = str_replace('?', '%s', $q);
            } else {
              $arr[] = self::escape($yvalue);
            }
          }
          $sql .= $i > 0 ? ' AND ' : ' WHERE ';
          $sql .= vsprintf($q, $arr);
        }
        $i++;
      }
    }

    return $sql;
  }

  /**
   * Execute sql provided as argument
   *
   * @param string $sql
   * @param boolean $fst_row
   * @return array 
   */
  static public function executeSql(string $sql, bool $fst_row=false) {
    $result_set = self::$database->query($sql);
    $row = $result_set->fetch_array();
    $result_set->free();
    if ($fst_row) return array_shift($row);
    else return $row;
  }

}
?>