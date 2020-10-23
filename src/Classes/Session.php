<?php
declare(strict_types=1);

namespace App\Classes;

/**
 * Session Class
 */
class Session {

  protected $user_id;
  protected $username;
  protected $email;
  private $last_login;
  private $user_type;

  public const MAX_LOGIN_AGE = 60*60*24; // 1 day

  /**
   * Start session
   * Check if the User is logged in
   */
  public function __construct() {
    session_start();

    $this->checkStoredLogin();
  }

  /**
   * Login the User
   * Put User data into SESSION and set Session object properties
   *
   * @param User $user
   * @return boolean
   */
  public function login(User $user) {
    if ($user) {
      // prevent session fixation attacks
      if ($user->user_type == 'admin') session_regenerate_id();

      $this->user_id = $_SESSION['user_id'] = $user->id;
      $this->username = $_SESSION['username'] = $user->username;
      $this->email = $_SESSION['email'] = $user->email_confirmed ? $user->email : false;
      $this->last_login = $_SESSION['last_login'] = time();
      $this->user_type = $_SESSION['user_type'] = $user->user_type;
    }

    return true;
  }

  /**
   * Checks if the User is logged in
   *
   * @return boolean
   */
  public function isLoggedIn() {
    return isset($this->user_id) && $this->lastLoginIsRecent();
  }

  /**
   * Logout the User
   * Remove User data from SESSION and Session object
   *
   * @return boolean
   */
  public function logout() {
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
    unset($_SESSION['email']);
    unset($_SESSION['last_login']);
    unset($_SESSION['user_type']);
    unset($this->user_id);
    unset($this->username);
    unset($this->email);
    unset($this->last_login);
    unset($this->user_type);

    unset($_SESSION['store']);

    return true;
  }

  /**
   * Set Session object properties from SESSION
   * if the User is logged in
   *
   * @return void
   */
  private function checkStoredLogin() {
    if (isset($_SESSION['user_id'])) {
      $this->user_id = $_SESSION['user_id'];
      $this->username = $_SESSION['username'];
      $this->email = $_SESSION['email'];
      $this->last_login = $_SESSION['last_login'];
      $this->user_type = $_SESSION['user_type'];
    }
  }

  /**
   * Check if the User Session is outdated
   * relative to the User's last activity
   *
   * @return boolean
   */
  private function lastLoginIsRecent() {
    if (!isset($this->last_login)) {
      return false;
    } elseif(($this->last_login + self::MAX_LOGIN_AGE) < time()) {
      return false; 
    } else {
      return true;
    }
  }

  /**
   * Put message and its type into user SESSION
   * As type it accepts Bootsrap4 alert classes
   *
   * @param string $msg
   * @param string $msg_type
   * @return string[]
   */
  public function message(string $msg="", $msg_type="success") {
    if (!empty($msg)) {
      // Then this is a "set" message
      $_SESSION['message'] = $msg;
      $_SESSION['message_type'] = $msg_type;
      return true;
    } else {
      // Then this is a "get" message
      if (isset($_SESSION['message'])) {
        return [$_SESSION['message'], $_SESSION['message_type']];
      } else {
        return ["", ""];
      }
    }
  }

  /**
   * Remove message from the SESSION
   *
   * @return void
   */
  public function clearMessage() {
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
  }

  /**
   * Returns user ID
   *
   * @return integer
   */
  public function getUserId() {
    return $this->user_id;
  }

  /**
   * Check if user is admin
   *
   * @return boolean
   */
  public function isAdmin() {
    if (!$this->isLoggedIn()) {
      return false;
    } else {
      return ($this->user_type == 'admin' && $this->email);
    }
  }

  /**
   * Check if user is author
   *
   * @return boolean
   */
  public function isAuthor() {
    if (!$this->isLoggedIn()) return false;
      // admin also has author capability!
    else return (
      $this->user_type == 'author' && $this->email ||
      $this->user_type == 'admin' && $this->email
    );
  }

  /**
   * If the user changes email
   * it must be set to false
   *
   * @return void
   */
  public function emailFalse() {
    $_SESSION['email'] = false;
    $this->email = false;
  }

  /**
   * Get the User email
   *
   * @return string
   */
  public function userEmail() {
    return $this->email;
  }

  /**
   * Get the User username
   *
   * @return string
   */
  public function username() {
    return $this->username;
  }

  /**
   * Retrieve data stored in SESSION
   * Provide data to store in SESSION
   *
   * @param array $data
   * @return void | boolean
   */
  public function store(array $data=[]) {
    if (empty($data) && isset($_SESSION['store'])) {
      return $_SESSION['store'];

    } elseif (!empty($data)) {
      foreach ($data as $key => $value) {
        if (!is_null($value)) {
          $_SESSION['store'][$key] = $value;
        } else {
          $_SESSION['store'][$key] = null;
          unset($_SESSION['store'][$key]);
        }
      }
    } else {
      return false;
    }
  }

  /**
   * Retrieve value stored in SESSION
   * Provide value to store in SESSION
   *
   * @param string $value
   * @param boolean $store
   * @return void | boolean
   */
  public function store_of(string $value, bool $store=true) {
    if (isset($_SESSION['store'][$value])) {
      if ($store == false) {
        $item = $_SESSION['store'][$value];
        $_SESSION['store'][$value] = null;
        unset($_SESSION['store'][$value]);
        return $item;
      } else {
        return $_SESSION['store'][$value];
      }
    } else {
      return false;
    }
  }

}
?>