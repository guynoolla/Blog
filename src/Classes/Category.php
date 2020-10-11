<?php
declare(strict_types=1);

namespace App\Classes;

class Category extends \App\Classes\DatabaseObject {

  static protected $table_name = "`categories`";
  static protected $db_columns = ['id','name','description','created_at'];
  
  public $id;
  public $name;
  public $description;
  public $created_at;

  public function __construct(array $args=[]) {
    foreach($args as $key => $value) {
      $args[$key] = strip_tags(trim($value));
    }
    $this->name = $args['name'] ?? '';
    $this->description = $args['description'] ?? '';
  }

  protected function beforeValidation($attr) {
    foreach($attr as $key => $value) {
      $value = trim(strip_tags($value));
      if ($key == 'description' && $value == "") {
        $value = 'NULL';
      }
      $attr[$key] = $value;
    }
    return parent::beforeValidation($attr);
  }

  protected function validate() {
    $this->errors = [];

    if (is_blank($this->name)) {
      $this->errors[] = 'Category name cannot be blank.';
    } elseif(!has_length($this->name, ['max' => 50])) {
      $this->errors[] = 'Category name can not contain more than 50 characters.';
    }

    if (!is_blank($this->description)) {
      if (!has_length($this->description, ['min' => 0, 'max' => 255])) {
        $this->errors[] = 'Category description can not contain more than 255 characters.';
      }
    }

    if (!isset($this->id)) {
      if (self::findByCategory($this->name)) {
        $this->errors[] = 'This category already exists.';
      }
    }

    return (empty($this->errors) == true);
  }

  static public function findByCategory($name) {
    $obj_array = parent::findWhere(['name' => $name]);
    if (!empty($obj_array)) {
      return array_shift($obj_array);
    } else {
      return false;
    }
  }

}
?>