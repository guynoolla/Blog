<?php
declare(strict_types=1);

namespace App\Classes;

use App\Classes\Token;

class User extends \App\Classes\DatabaseObject {

  static protected $table_name = "`users`";
  static protected $db_columns = ['id','user_type','username','email','about_image','about_text','about_appear','email_confirmed','hashed_password','created_at','password_reset_hash','password_reset_expires_at','email_confirm_hash','email_confirm_expires_at'];

  public $id;
  public $user_type;
  public $username;
  public $email;
  public $about_image;
  public $about_text;
  public $about_appear;
  protected $email_confirmed;
  protected $hashed_password;
  protected $password_reset_hash;
  protected $password_reset_expires_at;
  protected $email_confirm_hash;
  protected $email_confirm_expires_at;
  public $created_at;
  // form password fields
  public $password;
  public $confirm_password;
  // extra property
  protected $password_required = true;
  public $empty_password_field = true;

  protected $image_obj;

  // Relational data by foreign key in posts
  public $posted = "";
  public $approved = "";

  public function __construct(array $args=[]) {
    $this->username = $args['username'] ?? "";
    $this->email = $args['email'] ?? "";
    $this->password = $args['password'] ?? "";
    $this->confirm_password = $args['confirm_password'] ?? "";
  }

  public function fileInstance(File $image_obj) {
    $this->image_obj = $image_obj;
  }

  protected function setHashedPassword() {
    $this->hashed_password = password_hash($this->password, PASSWORD_BCRYPT);
  }

  public function verifyPassword($password) {
    return password_verify($password, $this->hashed_password);
  }

  protected function create() {
    $this->setHashedPassword();
    return parent::create();
  }

  protected function update() {
    if ($this->password != '') {
      $this->setHashedPassword();
      // validate password
    } else {
      // password not being updated, skip hashing and validation
      $this->password_required = false;
    }
    return parent::update();
  }

  public function beforeValidation($attr) {
    foreach($attr as $prop => $value) {
      if ($prop === 'about_appear') {
        $this->filterCheckboxValue($prop);
        $attr[$prop] = $this->about_appear;
      }
    }
    return $attr;
  }

  protected function validate() {
    $this->errors = [];

    if (is_blank($this->username)) {
      $this->errors[] = "Username cannot be blank"; // ru Нужно ввести имя пользователя
    } elseif (!has_length($this->username, array('min' => 2, 'max' => 20))) {
      $this->errors[] = "Username must be between 2 and 20 characters."; // ru Имя пользователя должно включать в себя от 4 до 20 символов.
    } elseif(!has_unique_username($this->username, $this->id ?? 0)) {
      $this->errors[] = "Username not allowed. Try another."; // ru Это имя пользователя недоступно. Попробуйте другое.
    }

    if (is_blank($this->email)) {
      $this->errors[] = "Email cannot be blank."; // ru Нужно ввести эл.адрес.
    } elseif(!has_length($this->email, array('max' => 50))) {
      $this->errors[] = "Email must be less than 50 characters."; // ru Длина эл.адреса не должна превышать 50 символов.
    } elseif(!has_valid_email_format($this->email)) {
      $this->errors[] = "Email must be a valid format."; // ru Эл.адрес должен иметь соответствующий формат.
    } elseif(!has_unique_email($this->email, $this->id ?? 0)) {
      $this->errors[] = "Email already exists."; // ru Пользователь с данным эл.адресом зарегестрирован.
    }

    if (has_length_greater_than($this->about_text, 10000)) {
      $this->errors[] = "About text cannot contain more than 10000 characters.";
    }

    if ($this->password_required) {
      if(is_blank($this->password)) {
        $this->errors[] = "Password cannot be blank."; // ruНужно ввести пароль.
      } elseif(!has_length($this->password, array('min' => 8))) {
        $this->errors[] = "Password must contain at least 8 characters"; // ru Пароль должен включать в себя не менее 8 символов.
      } elseif(!preg_match('/[A-Z]/', $this->password)) {
        $this->errors[] = "Password must contain at least 1 uppercase letter"; //ru Пароль должен включать в себя хотя бы 1 большую букву.
      } elseif(!preg_match('/[a-z]/', $this->password)) {
        $this->errors[] = "Password must contain at least 1 lowercase letter"; // ru Пароль должен включать в себя хотя бы 1 маленькую букву.
      } elseif(!preg_match('/[0-9]/', $this->password)) {
        $this->errors[] = "Password must contain at least 1 number"; // Пароль должен включать в себя хотя бы 1 цифру
      } else {
        $this->empty_password_field = false;
      }
      if (is_blank($this->confirm_password)) {
        $this->errors[] = "Confirm password cannot be blank."; // ru Пароль нужно ввести повторно.
        $this->empty_password_field = true;
      } elseif($this->password !== $this->confirm_password) {
        $this->errors[] = "Password and confirm password must match."; // ru Пароли не совпадают.
        $this->empty_password_field = true;
      }
    }

    if (!is_null($this->about_appear)) {
      if (!$this->image_obj->isFileSelected() && $this->about_image == "") {
        $this->errors[] = "About image is not selected.";
      }
      if ($this->about_text == "") {
        $this->errors[] = "About text is not entered.";
      }
    }

    return (empty($this->errors) == true);
  }

  public function save() {
    if (!isset($this->image_obj)) {
      return parent::save();
    }
    $create = (!isset($this->id) == true);
    $update = (isset($this->id) && $this->image_obj->isFileSelected() == true);
    if (!$create && !$update) {
      return parent::save();
    }
    $this->image_obj->handleUpload('about_image');

    if ($this->image_obj->error) {
      $this->errors[] = $this->image_obj->error;
      return false;

    } else {
      $old_image = $update ? $this->about_image : false;
      $this->about_image = $this->image_obj->getFileInfo()['about_image'];
      
      if (parent::save()) {
        if ($old_image) $this->image_obj->remove($old_image);
        return true;

      } else {
        $this->image_obj->remove($this->about_image);
        return false;
      }
    }
  }

  public function mergeAttributes(array $args=[]) {
    if (isset($args['about'])) {
      if (!isset($args['about']['about_appear'])) {
        $args['about']['about_appear'] = '0';
      }
      foreach ($args['about'] as $key => $value) {
        $args[$key] = $value;
      }
    }
    parent::mergeAttributes($args);
  }

  public function isAdmin() {
    return $this->user_type === 'admin';
  }

  public function getUserType() {
    if ($this->user_type == '') return 'logged_in';
    else return $this->user_type;
  }

  static public function findByUsername($username) {
    $sql = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE username='" . self::$database->escape_string($username) . "'";
    $obj_array = static::findBySql($sql);
    if (!empty($obj_array)) {
      return array_shift($obj_array);
    } else {
      return false;
    }
  }

  static public function findByEmail($email) {
  $sql = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE email='" . self::$database->escape_string($email) . "'";
    $obj_array = static::findBySql($sql);
    if (!empty($obj_array)) {
      return array_shift($obj_array);
    } else {
      return false;
    }
  }

  static protected function token($token=false) {
    if (!$token) return new Token;
    else return new Token($token);
  }

  static public function createPasswordResetToken($email) {
    $token = User::token();
    $user = self::findByEmail($email);

    if ($user) {
      $two_hours = time() + 60*60*2; // 2 hours from now
      $user->password_reset_hash = $token->getHash();
      $user->password_reset_expires_at = date('Y-m-d H:i:s', $two_hours);
      
      if ($user->save()) return $token->getValue();
      else {
        dd($user->errors);
      }
    }
    return false;
  }

  static public function getByPasswordResetToken($user_token) {
    $token = User::token($user_token);
  
    $sql = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE password_reset_hash = '" . $token->getHash() . "'";
    $obj_array = static::findBySql($sql);

    if (!empty($obj_array)) {
      $user = array_shift($obj_array);
      if (strtotime($user->password_reset_expires_at) > time()) {
        return $user;
      } else {
        return false;
      }
    }
  }

  public function resetPassword($data) {
    $this->mergeAttributes([
      'password' => $data['password'],
      'confirm_password' => $data['confirm_password'],
      'password_reset_hash' => 'NULL',
      'password_reset_expires_at' => 'NULL'
    ]);
    return $this->save();
  }

  public function isEmailConfirmed() {
    return ($this->email_confirmed == '1');
  }

  static public function createEmailConfirmToken($email) {
    $token = User::token();

    $user = self::findByEmail($email);

    if ($user) {
      $two_hours = time() + 60*60*2; // 2 hours from now
      $user->email_confirm_hash = $token->getHash();
      $user->email_confirm_expires_at = date('Y-m-d H:i:s', $two_hours);
      
      if ($user->save()) {
        return $token->getValue();
      }
    }
    return false;
  }

  static public function getByEmailConfirmToken($user_token) {
    $token = User::token($user_token);
    
    $sql = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE email_confirm_hash = '" . $token->getHash() . "'";
    $obj_array = static::findBySql($sql);

    if (!empty($obj_array)) {
      $user = array_shift($obj_array);
      if (strtotime($user->email_confirm_expires_at) > time()) {
        return $user;
      } else {
        return false;
      }
    }
  }

  public function confirmEmail() {
    $this->mergeAttributes([
      'email_confirmed' => '1',
      'user_type' => 'author',
      'email_confirm_hash' => 'NULL',
      'email_confirm_expires_at' => 'NULL'
    ]);
    return $this->save();
  }

  static public function queryUsersWithPostsNum(int $per_page, int $offset) {
    $sql = <<<SQL
          SELECT u.*, COUNT(p.user_id) AS posted,
          SUM(if (p.approved = '1', 1, 0)) AS approved
          FROM `users` AS u LEFT JOIN `posts` AS p
          ON u.id = p.user_id
          GROUP BY u.id ORDER BY u.username
          LIMIT $per_page OFFSET $offset
SQL;
    return self::findBySql($sql);
  }

  protected function filterCheckboxValue($property) {
    if (in_array($this->$property, ['on','1','checked'])) {
      $this->$property = '1';
    }
  }

}
?>