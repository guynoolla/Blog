<?php
declare(strict_types=1);

namespace App\Classes;

class Like extends \App\Classes\DatabaseObject {

  static protected $table_name = 'likes';
  static protected $db_columns = ['id','user_id','post_id','created_at'];
  protected $id;
  protected $post_id;
  protected $user_id;

  public function __construct(array $args=[]) {
    $this->post_id = $args['post_id'] ?? '';
    $this->user_id = $args['user_id'] ?? '';
  }

  static public function getUserPostLike(int $user_id, int $post_id) {
    $obj_arr = self::findWhere(['user_id' => $user_id, 'post_id' => $post_id]);
    if (!empty($obj_arr)) {
      return array_shift($obj_arr);
    } else {
      return false;
    }
  }

  static public function countPostLikes(int $post_id) {
    return self::countAll(['post_id' => $post_id]);
  }

}
?>