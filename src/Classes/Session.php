<?php
declare(strict_types=1);

namespace App\Classes;

class Session {

  protected $user_id;
  public $username;
  private $last_login;
  private $user_type;

  public const MAX_LOGIN_AGE = 60*60*24; // 1 day

  public function __construct() {
    session_start();
    $this->checkStoredLogin();
  }

  public function login(User $user) {
    if ($user) {
      // prevent session fixation attacks
      if ($user->user_type == 'admin') session_regenerate_id();

      $this->user_id = $_SESSION['user_id'] = $user->id;
      $this->username = $_SESSION['username'] = $user->username;
      $this->last_login = $_SESSION['last_login'] = time();
      $this->user_type = $_SESSION['user_type'] = $user->user_type;
    }
    return true;
  }

  public function isLoggedIn() {
    return isset($this->user_id) && $this->lastLoginIsRecent();
  }

  public function logout() {
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
    unset($_SESSION['last_login']);
    unset($_SESSION['user_type']);
    unset($this->user_id);
    unset($this->username);
    unset($this->last_login);
    unset($this->user_type);

    unset($_SESSION['store']);

    return true;
  }

  private function checkStoredLogin() {
    if(isset($_SESSION['user_id'])) {
      $this->user_id = $_SESSION['user_id'];
      $this->username = $_SESSION['username'];
      $this->last_login = $_SESSION['last_login'];
      $this->user_type = $_SESSION['user_type'];
    }
  }

  private function lastLoginIsRecent() {
    if (!isset($this->last_login)) {
      return false;
    } elseif(($this->last_login + self::MAX_LOGIN_AGE) < time()) {
      return false; 
    } else {
      return true;
    }
  }

  public function message($msg="") {
    if (!empty($msg)) {
      // Then this is a "set" message
      $_SESSION['message'] = $msg;
      return true;
    } else {
      // Then this is a "get" message
      return $_SESSION['message'] ?? '';
    }
  }

  public function clearMessage() {
    unset($_SESSION['message']);
  }

  public function getUserId() {
    return $this->user_id;
  }

  public function isAdmin() {
    if (!$this->isLoggedIn()) {
      return false;
    } else {
      return $this->user_type == 'admin';
    }
  }

  public function isAuthor() {
    if (!$this->isLoggedIn()) return false;
      // admin also has author capability!
    else return ($this->user_type == 'author' || $this->user_type == 'admin');
  }

  public function store(array $data=[]) {
    if (empty($data) && isset($_SESSION['store'])) {
      return $_SESSION['store'];
    } elseif (!empty($data)) {
      foreach ($data as $key => $value) {
        $_SESSION['store'][$key] = $value;
      }
    } else {
      return false;
    }
  }

}
?>