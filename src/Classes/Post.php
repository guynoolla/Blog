<?php
declare(strict_types=1);

namespace App\Classes;

class Post extends \App\Classes\DatabaseObject {

  static protected $table_name = 'posts';
  static protected $db_columns = ['id','user_id','topic_id','title','image','body','video_urls','published','proved','created_at','updated_at'];

  public $id;
  public $user_id;
  public $topic_id;
  public $title;
  public $image;
  public $body;
  protected $video_urls;
  public $published;
  public $proved;
  public $created_at;
  public $updated_at;

  protected $image_obj; // File class instance
  
  public $allowable_tags = '<h2><h3><h4><p><br><img><a><strong><em><ul><li><blockquote>';
  public $allowable_hosts = ['www.youtube.com','youtube.com','youtu.be','vimeo.com'];

  public function __construct(array $args=[]) {
    $this->user_id = $args['user_id'] ?? '';
    $this->topic_id = $args['topic_id'] ?? '';
    $this->title = $args['title'] ?? '';
    $this->image = $args['image'] ?? '';
    $this->body = $args['body'] ?? '';
    $this->published = $args['published'] ?? '';
  }

  public function fileInstance(File $image_obj) {
    $this->image_obj = $image_obj;
  }

  public function excerpt($content) {
    return (substr(strip_tags($content), 0, 150) . '...');
  }

  protected function beforeValidation($attr) {
    foreach ($attr as $prop => $value) {
      if ($prop !== 'body') {
        if (!is_null($value)) {
          $this->$prop = strip_tags($value);
          $attr[$prop] = $this->$prop;
        }
      }
      if ($prop === 'video_urls') {
        $this->getBodyVideoUrls();
        $attr[$prop] = $this->video_urls;
      } elseif ($prop === 'published') {
        $this->filterCheckboxValue($prop);
        $attr[$prop] = $this->published;
      }
    }
    return $attr;
  }

  protected function validate() {
    $this->errors = [];

    if (is_blank($this->title)) {
      $this->errors[] = 'Title cannot be blank.';
    } elseif (!has_length($this->title, ['max' => 200])) {
      $this->errors[] = 'Title must be less than 200 characters.';
    }
    if ($this->topic_id == 0) {
      $this->errors[] = 'Please select a topic.';
    }

    if (is_blank($this->body)) {
      $this->errors[] = 'Post content cannot be blank.';
    }
    if (has_unallowed_tag($this->body, $this->allowable_tags)) {
      $this->errors[] = 'Post has not allowed html tag(s).';
    }
    if (has_external_link($this->body, $this->allowable_hosts)) {
      $this->errors[] = 'Post cannot contain external links except YouTube and Vimeo.';
    }
    if (has_length_greater_than($this->body, 65000)) {
      $this->errors[] = 'Post cannot contain more than 65000 characters.';
    }

    if (($count = $this->videoUrlsCount()) > 3) {
      $this->errors[] = 'Post cannot have more than 3 videos.';
    }

    return (empty($this->errors) == true);
  }

  protected function videoUrlsCount() {
    if (isset($this->video_urls)) {
      return count( (array) json_decode($this->video_urls));
    } else {
      return 0;
    }
  }

  public function delete() {
    $this->image_obj->remove($this->image);
    return parent::delete();
  }

  protected function filterCheckboxValue($property) {
    if (in_array($this->$property, ['on','1','checked'])) {
      $this->$property = '1';
    } else {
      $this->$property = '0';
    }
  }

  /**
   * Video embed URL examples:
   * https://www.youtube.com/watch?v=GDeJtgjvXTk
   * https://youtu.be/GDeJtgjvXTk
   * https://vimeo.com/440413540
  */
  protected function getBodyVideoUrls() {
    $links = has_links($this->body, true);
    $data = [];
    foreach($links as $key => $url) {
      $embed_url = "";
      $host = parse_url($url)['host'];
      if ($host === 'www.youtube.com' || $host === 'youtu.be') {
        $embed_url = $this->getYoutubeEmbedUrl($url);
      } elseif ($host === 'vimeo.com') {
        $embed_url = $this->getVimeoEmbedUrl($url);
      }
      if ($embed_url) $data[$url] = $embed_url;
    }
    if (!empty($data)) {
      $this->video_urls = json_encode($data);
    }
  }

  public function save() {
    if (!isset($this->image_obj)) return parent::save();
    $create = (!isset($this->id) == true);
    $update = (isset($this->id) && $this->image_obj->isFileSelected() == true);
    if (!$create && !$update) return parent::save();

    $image_error = $this->image_obj->handleUpload('image');

    if ($image_error !== false) {
      $this->errors[] = $image_error;
      return false;

    } else {
      $old_image = $update ? $this->image : false;
      $this->image = $this->image_obj->getFileInfo()['image'];
      
      if (parent::save()) {
        if ($old_image) $this->image_obj->remove($old_image);
        return true;

      } else {
        $this->image_obj->remove($this->image);
        return false;
      }
    }
  }

  static public function queryProvedPosts() {
    $sql = "SELECT p.*, u.username FROM posts AS p";
    $sql .= " JOIN users AS u ON p.user_id = u.id";
    $sql .= " WHERE p.published = 1 AND p.proved = 1";
    $result = self::$database->query($sql);
    $posts = [];
    while($obj = $result->fetch_object()) {
      $posts[] = $obj;
    }
    $result->free();

    return $posts;
  }

  static public function querySearchPosts($term) {
    $_term = self::$database->escape_string($term);
    $sql = "SELECT p.*, u.username FROM posts AS p";
    $sql .= " JOIN users AS u ON p.user_id = u.id";
    $sql .= " WHERE published = 1";
    $sql .= " AND p.title LIKE '%" . $_term . "%'";
    $sql .= " OR p.body LIKE '%" . $_term . "%'";
    $result = self::$database->query($sql);
    $posts = [];
    while($obj = $result->fetch_object()) {
      $posts[] = $obj;
    }
    $result->free();

    return $posts;
  }

  static public function queryPostsByTopic($topic_id) {
    $_topic_id = self::$database->escape_string($topic_id);
    $sql = "SELECT p.*, u.username FROM posts AS p";
    $sql .= " JOIN users AS u ON p.user_id = u.id";
    $sql .= " WHERE p.published = 1";
    $sql .= " AND topic_id = " . $_topic_id;
    $result = self::$database->query($sql);
    $posts = [];
    while($obj = $result->fetch_object()) {
      $posts[] = $obj;
    }
    $result->free();

    return $posts;    
  }

  static public function queryPostsWithUsernames(array $where, string $end="") {
    $sql = "SELECT p.*, u.username FROM posts AS p";
    $sql .= " LEFT JOIN users AS u ON p.user_id = u.id";
    $where = self::prefixColumnName($where, 'p');
    $sql = parent::concatWhereToSql($sql, $where);

    if ($end != "") $sql .= $end;

    $result = self::$database->query($sql);
    $posts = [];
    while ($obj = $result->fetch_object()) {
      $posts[] = $obj;
    }
    $result->free();

    return $posts; 
  }

  static protected function prefixColumnName($where, $prefix='p') {
    $prefixed = [];
    foreach ($where as $col => $value) {
      $prefixed[$prefix.'.'.$col] = $value;
    }
    return $prefixed;
  }

  public function getBodyWithVideo() {
    if (!isset($this->video_urls)) return $this->body;

    $video_urls = json_decode($this->video_urls);

    if (!empty($video_urls)) {
      foreach ($video_urls as $url => $embed_url) {
        $div = '<div class="embed-responsive embed-responsive-16by9">%s</div>';
        $youtube = '<iframe src="%s" class="embed-responsive-item"';
        $youtube .= ' frameborder="0" allowfullscreen></iframe>';
        $vimeo = '<iframe src="%s" class="embed-responsive-item"';
        $vimeo .= ' frameborder="0" webkitallowfullscreen mozallowfullscreen';
        $vimeo .= ' allowfullscreen></iframe>';
        $host = parse_url($url)['host'];
        if ($host === 'www.youtube.com' || $host === 'youtu.be') {
          $div = sprintf($div, $youtube);
        } elseif ($host === 'vimeo.com') {
          $div = sprintf($div, $vimeo);
        }
        $this->body = str_replace($url, $div, $this->body);
        $this->body = sprintf($this->body, $embed_url);
      }
    }
    return $this->body;
  }

  function getYoutubeEmbedUrl($url) {
    //$short_url_regex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
    //$long_url_regex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))(\w+)/i';
    $both_urls_regex = "#(?<=v=|v\/|vi=|vi\/|youtu.be\/)[a-zA-Z0-9_-]{11}#";

    if (preg_match($both_urls_regex, $url, $matches)) {
      $youtube_id = $matches[count($matches) - 1];
      return 'https://www.youtube.com/embed/' . $youtube_id;
    } else {
      return false;
    }
  }

  function getVimeoEmbedUrl($url) {
    $vimeo_id = substr(parse_url($url, PHP_URL_PATH), 1);
    if ($vimeo_id) {
      return 'https://player.vimeo.com/video/' . $vimeo_id;
    } else {
      return false;
    }
  }

  static public function queryRandomImage() {
    $sql = "SELECT image FROM posts WHERE proved = 1";
    $sql .= " ORDER BY RAND() LIMIT 1";
    $result = self::$database->query($sql);
    $image = $result->fetch_assoc()['image'];
    $result->free();

    return $image;
  }

}
?>