<?php
declare(strict_types=1);

namespace App\Classes;

/**
 * Class Like
 * 
 * This class handles user posts likes
 */
class Like extends \App\Classes\DatabaseObject {

  static protected $table_name = "`likes`";
  static protected $db_columns = ['id','post_id','user_id','liked','created_at'];
  public $id;
  public $post_id;
  public $user_id;
  public $liked;
  public $created_at;
  protected $actions = ['create','delete'];

  /**
   * Create Like
   *
   * @param int $post_id
   * @param int $user_id
   * @return void
   */  
  static protected function createLike(int $post_id, int $user_id) {
    $like = new Like;
    $like->mergeAttributes([
      'post_id' => $post_id,
      'user_id' => $user_id,
      'liked' => '0'
    ]);
    if ($like->save()) return $like;
  }

  /**
   * Get user like object
   * 
   * If object does not exists create it
   *
   * @param int $post_id
   * @param int $user_id
   * @return void
   */
  static public function get(int $post_id, int $user_id) {
    $liked = Like::isLiked($post_id, $user_id);
    if ($liked) {
      return $liked;
    } else {
      return Like::createLike($post_id, $user_id);
    }
  }

  /**
   * Check if the User liked the post
   *
   * @param int $post_id
   * @param int $user_id
   * @return object | boolean
   */
  static public function isLiked(int $post_id, int $user_id) {
    $pid = self::escape($post_id);
    $uid = self::escape($user_id);
    $sql = <<<SQL
      SELECT * FROM `likes` WHERE
      post_id = $pid AND user_id = $uid
SQL;
    $like = parent::findBySql($sql);

    return (!empty($like) ? $like[0] : false);
  }

  /**
   * Set liked property for object
   *
   * @param string $action
   * @return boolean
   */
  public function process(string $action) {
    if (in_array($action, $this->actions)) {
      $this->liked = $action == 'create' ? '1' : '0';
      return $this->save();
    }
    return false;
  }

  /**
   * Count likes for particular post
   *
   * @param int $post_id
   * @return number
   */
  static public function countPostLikes(int $post_id) {
    return self::countAll([
      'post_id' => $post_id,
      'liked' => '1'
    ]);
  }

  /**
   * Get user's likes for particular time period
   *
   * @param int $user_id
   * @return object | false
   */
  static public function userLikesForLast30Days(int $user_id) {
    $uid = parent::escape($user_id);

    $sql = <<<SQL
    SELECT * FROM `likes` WHERE
    created_at BETWEEN NOW() - INTERVAL 30 DAY AND NOW()
    AND user_id = $uid AND liked = '1'
SQL;

    return self::findBySql($sql);
  }

}
?>