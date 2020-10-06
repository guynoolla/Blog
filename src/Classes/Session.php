<?php
declare(strict_types=1);

namespace App\Classes;

class Session {

  protected $user_id;
  protected $username;
  protected $email;
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
      $this->email = $_SESSION['email'] = $user->email_confirmed ? $user->email : false;
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
    unset($_SESSION['email']);
    unset($this->user_id);
    unset($this->username);
    unset($this->last_login);
    unset($this->user_type);
    unset($this->email);

    unset($_SESSION['store']);

    return true;
  }

  private function checkStoredLogin() {
    if (isset($_SESSION['user_id'])) {
      $this->user_id = $_SESSION['user_id'];
      $this->username = $_SESSION['username'];
      $this->last_login = $_SESSION['last_login'];
      $this->user_type = $_SESSION['user_type'];
      $this->email = $_SESSION['email'];
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

  public function message($msg="", $msg_type="success") {
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

  public function clearMessage() {
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
  }

  public function getUserId() {
    return $this->user_id;
  }

  public function isAdmin() {
    if (!$this->isLoggedIn()) {
      return false;
    } else {
      return ($this->user_type == 'admin' && $this->email);
    }
  }

  public function isAuthor() {
    if (!$this->isLoggedIn()) return false;
      // admin also has author capability!
    else return (
      $this->user_type == 'author' && $this->email ||
      $this->user_type == 'admin' && $this->email
    );
  }

  public function emailFalse() {
    $_SESSION['email'] = false;
    $this->email = false;
  }

  public function userEmail() {
    return $this->email;
  }

  public function username() {
    return $this->username;
  }

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

  public function store_of($value, $store=true) {
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