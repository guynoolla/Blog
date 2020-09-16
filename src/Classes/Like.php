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
  public $created_at;
  protected $actions = ['create','delete'];

  public function __construct() {
  }

  static protected function createLike($post_id, $user_id) {
    $like = new Like;
    $like->mergeAttributes([
      'post_id' => $post_id,
      'user_id' => $user_id,
      'liked' => '0'
    ]);
    if ($like->save()) return $like;
  }

  static public function get($post_id, $user_id) {
    $liked = Like::isLiked($post_id, $user_id);
    if ($liked) {
      return $liked;
    } else {
      return Like::createLike($post_id, $user_id);
    }
  }

  static public function isLiked($post_id, $user_id) {
    $pid = self::escape($post_id);
    $uid = self::escape($user_id);
    $sql = <<<SQL
      SELECT * FROM likes WHERE
      post_id = $pid AND user_id = $uid
SQL;
    $like = parent::findBySql($sql)[0];

    return $like;
  }

  public function process($action) {
    if (in_array($action, $this->actions)) {
      $this->liked = $action == 'create' ? '1' : '0';
      return $this->save();
    }
    return false;
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