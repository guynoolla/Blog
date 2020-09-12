<?php
declare(strict_types=1);

namespace App\Classes;

class Post extends \App\Classes\DatabaseObject {

  static protected $table_name = 'posts';
  static protected $db_columns = ['id','user_id','topic_id','title','format','image','video','body','video_urls','published','approved','created_at','updated_at'];

  public $id;
  public $user_id;
  public $topic_id;
  public $title;
  public $format;
  public $image;
  public $video;
  public $body;
  protected $video_urls;
  public $published;
  public $approved;
  public $created_at;
  public $updated_at;

  protected $image_obj; // File class instance
  
  public $allowable_tags = '<h2><h3><h4><p><br><img><a><strong><em><ul><li><blockquote>';
  public $allowable_hosts = ['www.youtube.com','youtube.com','youtu.be','vimeo.com'];

  protected $image_aspect_ratio = ['min'=>1.4, 'max'=>1.8];

  static public $resize_dimensions = [
              ['width' => 420, 'height' => 240],
              ['width' => 640, 'height' => 365],
              ['width' => 800, 'height' => 450],
              ['width' => 1025, 'height' => 580]
            ];

  // Relational data by foreign key in posts
  public $username = '';  // user->username
  public $topic = '';     // topic->name

  public function __construct(array $args=[]) {
    $this->user_id = $args['user_id'] ?? '';
    $this->topic_id = $args['topic_id'] ?? '';
    $this->title = $args['title'] ?? '';
    $this->format = $args['format'] ?? '';
    $this->image = $args['image'] ?? '';
    $this->video = $args['video'] ?? '';
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
      } elseif ($prop === 'video') {
        if (is_array($value)) {
          $this->videoMerger();
          $attr[$prop] = $this->video;
        } elseif ($value && !json_decode($value)) {
          $this->getEntryVideoUrl();
          $attr[$prop] = $this->video;
        }
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

    if (!in_array($this->format, ['image', 'video'])) {
      $this->errors[] = 'Video must have image or video format';

    } else {
      if ($this->format == 'video') {
        if (is_blank($this->video)) {
          $this->errors[] = 'Post video url is not set.';
        }
        if (has_external_link($this->video, $this->allowable_hosts)) {
          $this->errors[] = 'Videos except YouTube and Vimeo are not allowed.';
        }
      } elseif ($this->format == 'image') {
        // Image validation is in save method
      }
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

  protected function filterCheckboxValue($property) {
    if (in_array($this->{$property}, ['on','1','checked'])) {
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

  protected function getEntryVideoUrl() {
    $host = parse_url($this->video)['host'];
    if ($host === 'www.youtube.com' || $host === 'youtu.be') {
      $embed_url = $this->getYoutubeEmbedUrl($this->video);
    } elseif ($host === 'vimeo.com') {
      $embed_url = $this->getVimeoEmbedUrl($this->video);
    }
    $this->video = json_encode([$this->video => $embed_url]);
  }

  public function save() {
    if (is_null($this->format) || $this->format == 'video') {
      return parent::save();

    } elseif ($this->format == 'image') {
      if (!isset($this->image_obj)) return parent::save();

      $create = (!isset($this->id) == true);
      $update = (isset($this->id) && $this->image_obj->isFileSelected() == true);

      if ($create || $update) {
        $this->image_obj->handleUpload('image', $this->image_aspect_ratio);

        if ($this->image_obj->error) {
          $this->errors[] = $this->image_obj->error;
          return false;

        } else {
          $file_info = $this->image_obj->getFileInfo();
          $old_image = isset($this->id) ? $this->image : false;
  
          $this->image = $file_info['image'];
          $this->resizeImage($file_info);
  
          if (parent::save()) {
            if ($old_image) $this->deleteImages($old_image);
            return true;
          } else {
            $this->deleteImages($this->image);
            return false;
          }
        }
      } else {
        return parent::save();
      }
    }
  }

  public function delete() {
    $this->deleteImages($this->image);
    return parent::delete();
  }

  protected function deleteImages($image) {
    $images_path = $this->image_obj->images_path;
    $filename = $images_path . '/' . $image;
    list($noextimg, $ext) = explode('.', $image);
    $this->image_obj->remove($image);

    foreach (self::$resize_dimensions as $d) {
      $resized = "/{$noextimg}_{$d['width']}.{$ext}";
      $this->image_obj->remove($resized);
    }
    return true;
  }

  protected function resizeImage($file) {
    list($w, $h) = getimagesize($file['filename']);

    foreach (self::$resize_dimensions as $d) {
      if ($w > $d['width']) {
        $imagine = new \Imagine\Gd\Imagine();
        $imagine->open($file['filename'])
          ->thumbnail(new \Imagine\Image\Box($d['width'], $d['height']))
          ->save("{$file['dir_path']}/{$file['id']}_{$d['width']}.{$file['ext']}");
      }
    }
    return true;
  }

  static public function queryApprovedPosts(int $per_page, int $offset) {
    $cond = <<<SQL
            WHERE p.approved = '1'
            ORDER BY p.created_at DESC
SQL;
    return self::selectWithJoins($cond, $per_page, $offset);
  }

  static public function querySearchPosts($term, int $per_page, int $offset) {
    $term = self::$database->escape_string($term);
    $cond = <<<SQL
            WHERE p.approved = 1
              AND ( p.title LIKE '%$term%' OR p.body LIKE '%$term%' )
            ORDER BY p.created_at DESC
SQL;
    return self::selectWithJoins($cond, $per_page, $offset);
  }

  static public function queryPostsByTopic($topic_id, $per_page, $offset) {
    $tid = parent::escape($topic_id);
    $cond = <<<SQL
            WHERE p.approved = '1' AND p.topic_id = $tid
            ORDER BY p.created_at DESC
SQL;
    return self::selectWithJoins($cond, $per_page, $offset);
  }

  static public function queryPostsByAuthor($user_id, $per_page, $offset) {
    $uid = parent::escape($user_id);
    $cond = <<<SQL
            WHERE p.approved = 1 AND p.user_id = $uid
            ORDER BY p.created_at DESC
SQL;
    return self::selectWithJoins($cond, $per_page, $offset);
  }

  static public function queryPostsByDatePub(array $dates, $per_page, $offset) {
    $cond = <<<SQL
            WHERE p.approved = '1'
            AND ( p.created_at >= '{$dates['date_min']}'
            AND p.created_at < '{$dates['date_max']}' )
SQL;
    return self::selectWithJoins($cond, $per_page, $offset);
  }

  static protected function selectWithJoins($conditions='', $per_page, $offset) {
    $sql = <<<SQL
      SELECT p.*, u.username, t.name as topic
      FROM posts AS p
      LEFT JOIN users AS u ON p.user_id = u.id
      LEFT JOIN topics AS t ON p.topic_id = t.id
SQL;
    $sql .= $conditions . " LIMIT {$per_page} OFFSET {$offset}";

    return self::findBySql($sql);
  }

  public function getEntryVideo() {
    $this->videoSplitter();
    $url = $this->video['url'];
    $embed_url = $this->video['embed'];

    if ($this->format == 'video') {
      $youtube = '<iframe src="%s" class="embed-responsive-item"';
      $youtube .= ' frameborder="0" allowfullscreen></iframe>';
      $vimeo = '<iframe src="%s" class="embed-responsive-item"';
      $vimeo .= ' frameborder="0" webkitallowfullscreen mozallowfullscreen';
      $vimeo .= ' allowfullscreen></iframe>';
      $host = parse_url($url)['host'];
      if ($host === 'www.youtube.com' || $host === 'youtu.be') {
        $iframe = $youtube;
      } elseif ($host === 'vimeo.com') {
        $iframe = $vimeo;
      }
      $output = sprintf($iframe, $embed_url);
      return $output;
    }
  }

  public function getBodyWithVideo() {
    if (!isset($this->video_urls)) return $this->body;

    $video_urls = json_decode($this->video_urls);

    if (!empty($video_urls)) {
      foreach ($video_urls as $url => $embed_url) {
        $div = '<div class="embed-responsive embed-responsive-16by9">%s</div>';
        $youtube = '<iframe src="%s" class="embed-responsive-item"';
        $youtube .= ' controls="0" showinfo="0" frameborder="0" allowfullscreen></iframe>';
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

  function videoSplitter() {
    if (isset($this->video) && $this->video != "") {
      $arr = (array) json_decode($this->video);
      $url = key($arr);
      $this->video = [];
      $this->video['url'] = $url;
      $this->video['embed'] = $arr[$url];
      return $this->video;
    }
    return "";
  }

  function videoMerger() {
    if (is_array($this->video)) {
      $this->video = json_encode([
        $this->video['url'] => $this->video['embed']
      ]);
      return $this->video;
    }
  }
  
  static public function responsive(string $image, $images_path, $depth = 0) {
    $src_value = '';
    $depth = ($depth == 0) ? count(self::$resize_dimensions) : $depth;
    $arr_max = $depth - 1;
    foreach (self::$resize_dimensions as $k => $v) {
      $src_value .= url_for("render_img.php?img={$image}&w={$v['width']}");
      $src_value .= $k < $arr_max ? " {$v['width']}w, " : " {$v['width']}w"; 
      if (($k + 1) == $depth) return $src_value;
    }
    return $src_value;
  }

}
?>