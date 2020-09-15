<?php
declare(strict_types=1);

namespace App\Classes;

class DatabaseObject {

  static protected $database;
  static protected $table_name;
  static protected $columns = [];
  public $errors = [];

  static public function setDatabase(\Mysqli $database) {
    self::$database = $database;
  }

  protected function validate() {
    // Add custom validations
    // ...
    return (empty($this->errors) == true);
  }

  protected function beforeValidation($attr) {
    // Run Child Class filter here
    // ...
    $this->errors = [];
    return $attr;
  }

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

  static protected function instantiate($record) {
    $object = new static;

    foreach ($record as $property => $value) {
      if (property_exists($object, $property)) {
        $object->{$property} = $value;
      }
    }

    return $object;
  }

  static public function findAll($order=[]) {
    $sql = "SELECT * FROM " . static::$table_name;
    if (!empty($order)) {
      $sql .= " ORDER BY " . key($order) . " " . $order[key($order)];
    }
    return static::findBySql($sql);
  }

  static public function findById($id) {
    $id = strval($id);
    $sql = "SELECT * FROM " . static::$table_name;
    $sql .= " WHERE id='" . self::escape($id) . "'";
    $obj_array = static::findBySql($sql);
    if (!empty($obj_array)) {
      return array_shift($obj_array);
    } else {
      return false;
    }
  }

  public function save() {
    if (isset($this->id)) {
      return $this->update();
    } else {
      return $this->create();
    }
  }

  protected function create() {
    $attributes = $this->attributes();
    $attributes = $this->beforeValidation($attributes);

    if (!$this->validate()) return false;

    $attribute_pairs = [];
    foreach ($attributes as $key => $value) {
      if ($value == "") { continue; }
      else { $attribute_pairs[] = "$key = '" . self::escape($value) . "'"; }
    }

    $sql = "INSERT INTO " . static::$table_name . "(";
    $sql .= join(', ', array_keys($attributes));
    $sql .= ") VALUES ('";
    $sql .= join("', '", array_values($attributes));
    $sql .= "')";
    $result = self::$database->query($sql);

    if ($result) { $this->id = self::$database->insert_id; }

    return $result;
  }

  protected function update() {
    $attributes = $this->attributes();
    $attributes = $this->beforeValidation($attributes);
  
    if (!$this->validate()) return false;

    $attribute_pairs = [];
    foreach ($attributes as $key => $value) {
      if ($value == "") { continue; }
      elseif ($value === 'NULL') { $attribute_pairs[] = "$key = NULL"; } 
      else { $attribute_pairs[] = "$key = '" . self::escape($value) . "'"; }
    }

    $sql = "UPDATE " . static::$table_name . " SET ";
    $sql .= join(', ', $attribute_pairs);
    $sql .= " WHERE id='" . self::escape($this->id) . "'";
    $sql .= " LIMIT 1";
    $result = self::$database->query($sql);

    return $result;
  }

  public function attributes() {
    $attributes = [];
    foreach (static::$db_columns as $column) {
      $ignore_columns = ['id','created_at','updated_at'];
      if (in_array($column, $ignore_columns)) { continue; }
      $attributes[$column] = $this->$column;
    }
    return $attributes;
  }

  public function mergeAttributes(array $args=[]) {
    foreach ($args as $key => $value) {
      if (property_exists($this, $key) && !is_null($value)) {
        $this->$key = $value;
      }
    }
  }

  static protected function escape($value) {
    return self::$database->escape_string($value);
  }

  public function delete() {
    $sql = "DELETE FROM " . static::$table_name;
    $sql .= " WHERE id='" . self::escape($this->id) . "'";
    $sql .= " LIMIT 1";
    $result = self::$database->query($sql);
    return $result;
  }

  static public function findWhere(array $where=[], string $append="") {
    $sql = "SELECT * FROM " . static::$table_name;
    $sql = self::concatWhereToSql($sql, $where);
    if ($append != "") $sql .= " {$append}";

    return static::findBySql($sql);
  }

  static public function countAll($where=[]) {
    $sql = "SELECT COUNT(*) FROM " . static::$table_name;
    $sql = self::concatWhereToSql($sql, $where);
    $result_set = self::$database->query($sql);
    $row = $result_set->fetch_array();
    $result_set->free();
    return array_shift($row);
  }

  static protected function concatWhereToSql($sql, $where) {
    if (!empty($where)) {
      $i = 0;
      foreach ($where as $column => $value) {
        if (!is_int($column)) {
          if (!is_array($value)) {
            $value = strval($value);
            if ($i > 0) {
              $sql .= " AND $column = '" . self::escape($value) . "'";
            } else {
              $sql .= " WHERE $column = '" . self::escape($value) . "'";
            }
          } else {
            foreach ($value as $operator => $ivalue) {
              $ivalue = strval($ivalue);
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

  static public function executeSql($sql, $fst_row=false) {
    $result_set = self::$database->query($sql);
    $row = $result_set->fetch_array();
    $result_set->free();
    if ($fst_row) return array_shift($row);
    else return $row;
  }

}

?>