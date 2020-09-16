<?php
declare(strict_types=1);

namespace App\Classes;

class Topic extends \App\Classes\DatabaseObject {

  static protected $table_name = "`topics`";
  static protected $db_columns = ['id','name','description'];
  
  public $id;
  public $name;
  public $description;

  public function __construct(array $args=[]) {
    foreach($args as $key => $value) {
      $args[$key] = strip_tags($value);
    }
    $this->name = $args['name'] ?? '';
    $this->description = $args['description'] ?? '';
  }

  protected function validate() {
    $this->errors = [];

    if(is_blank($this->name)) {
      $this->errors[] = 'Topic name cannot be blank.';
    } elseif(!has_length($this->name, ['max' => 100])) {
      $this->errors[] = 'Topic name must be less than 100 characters.';
    } elseif(self::findByTopic($this->name)) {
      $this->errors[] = 'This topic already exists.';
    }
    if(is_blank($this->description)) {
      $this->errors[] = 'Description cannot be blank.';
    } elseif(!has_length($this->description, ['min' => 10, 'max' => 1000])) {
      $this->errors[] = 'Description must be between 10 and 1000 characters.';
    }

    return (empty($this->errors) == true);
  }

  static public function findByTopic($name) {
    $obj_array = parent::findWhere(['name' => $name]);
    if (!empty($obj_array)) {
      return array_shift($obj_array);
    } else {
      return false;
    }
  }

}
?>