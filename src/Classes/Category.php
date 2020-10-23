<?php
declare(strict_types=1);

namespace App\Classes;

/**
 * Class Category
 * Category description property is optional
 */
class Category extends \App\Classes\DatabaseObject {

  static protected $table_name = "`categories`";
  static protected $db_columns = ['id','name','description','created_at'];
  
  public $id;
  public $name;
  public $description;
  public $created_at;

  /**
   * Class constructor
   * Initializes properties of new Category object
   *
   * @param array $args
   */
  public function __construct(array $args=[]) {
    foreach($args as $key => $value) {
      $args[$key] = strip_tags(trim($value));
    }
    $this->name = $args['name'] ?? '';
    $this->description = $args['description'] ?? '';
  }

  /**
   * Overrides the parent's beforeValidation method
   *  to manipulate or modify some Category attributes
   *
   * @param array $attr
   * @return array
   */
  protected function beforeValidation(array $attr) {
    foreach($attr as $key => $value) {
      $value = trim(strip_tags($value));
      if ($key == 'description' && $value == "") {
        $value = 'NULL';
      }
      $attr[$key] = $value;
    }
    return parent::beforeValidation($attr);
  }

  /**
   * Validate the Category attributes that come from Category Form
   * Errors if they exists gather parent's errors property
   *
   * @return boolean
   */
  protected function validate() {
    $this->errors = [];

    if (is_blank($this->name)) {
      $this->errors[] = 'Category name can not be blank.';
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

  /**
   * Retrieve category by its name
   *
   * @param string $name
   * @return object | error
   */
  static public function findByCategory(string $name) {
    $obj_array = parent::findWhere(['name' => $name]);
    if (!empty($obj_array)) {
      return array_shift($obj_array);
    } else {
      return false;
    }
  }

}
?>