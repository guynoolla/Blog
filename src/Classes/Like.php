<?php
declare(strict_types=1);

namespace App\Classes;

class Like extends \App\Classes\DatabaseObject {

  static protected $table_name = 'likes';
  static protected $db_columns = ['id','post_id','user_id','liked','created_at'];
  public $id;
  public $post_id;
  public $user_id;
  public $liked;
  protected $actions = ['create','delete'];

  public function __construct(array $args = []) {
    $post_id = $args['post_id'] ?? '';
    $user_id = $args['user_id'] ?? '';
    $liked = '0';
    $like = Like::issetUserPostLike($post_id, $user_id);

    if (!$like) {
      $sql = "INSERT INTO likes(post_id, user_id, liked)";
      $sql .= " VALUES('{$post_id}', '{$user_id}', '{$liked}')";
      $result = self::$database->query($sql);
      if ($result) {
        $this->id = self::$database->insert_id;
        $this->post_id = $post_id;
        $this->user_id = $user_id;
        $this->liked = $liked;
        return $this;
      }
    } else {
      $this->id = $like['id'];
      $this->post_id = $like['post_id'];
      $this->user_id = $like['user_id'];
      $this->liked = $like['liked'];
      return $this;
    }
  }

  public function process($action) {
    if (in_array($action, $this->actions)) {
      $this->liked = $action == 'create' ? '1' : '0';
      return $this->save();
    }
    return false;
  }

  static public function issetUserPostLike($post_id, $user_id) {
    $sql = "SELECT * FROM likes";
    $sql .= " WHERE post_id=" . self::escape($post_id);
    $sql .= " AND user_id=" . self::escape($user_id);
    $result = self::$database->query($sql);
    if ($result) {
      return $result->fetch_assoc();
    } else {
      return false;
    }
  }

  static public function countPostLikes(int $post_id) {
    return self::countAll([
      'post_id' => $post_id,
      'liked' => '1'
    ]);
  }

  static public function userLikesForLast30Days($user_id) {
    $uid = parent::escape($user_id);

    $sql = <<<SQL
    SELECT * FROM likes WHERE
    created_at BETWEEN NOW() - INTERVAL 30 DAY AND NOW()
    AND user_id = $uid AND liked = '1'
SQL;

    return self::findBySql($sql);
  }
}
?>