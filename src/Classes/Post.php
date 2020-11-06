<?php
declare(strict_types=1);

namespace App\Classes;

/**
 * Class Post
 * 
 * Post formats are image and video, allowed youtube and vimeo links
 * Post object set as property instance of File Class for image upload
 * Post object executes queries joined to users and categories tables
 */
class Post extends \App\Classes\DatabaseObject {

  static protected $table_name = "`posts`";
  static protected $db_columns = ['id','user_id','category_id','title','meta_desc','format','image','video','body','video_urls','published','approved','published_at','created_at','updated_at'];

  public $id;
  public $user_id;
  public $category_id;
  public $title;
  public $meta_desc;
  public $format;
  public $image;
  public $video;
  public $body;
  public $published;
  public $approved;
  public $published_at;
  public $created_at;
  public $updated_at;
  protected $video_urls;

  protected $image_obj; // File class instance
  protected $form_edit_scenario = [];
  
  public $allowable_tags = '<h2><h3><h4><p><br><img><a><strong><em><ul><li><blockquote>';
  public $allowable_hosts = ['www.youtube.com','youtube.com','youtu.be','vimeo.com'];

  protected $image_aspect_ratio = ['min'=>1.4, 'max'=>1.8];

  static public $resize_dimensions = [
              ['width' => 400, 'height' => 230],
              ['width' => 640, 'height' => 365],
              ['width' => 800, 'height' => 450],
              ['width' => 1025, 'height' => 580]
            ];

  // Relational data by foreign key in posts
  public $category = '';      // category->name
  public $username = '';      // user->username
  public $user_email = '';    // user->email
  public $ue_confirmed = '';  // user->email_confirmed

  /**
   * Class constructor
   * 
   * Initializes propertiec by creation a new Post Object
   *
   * @param array $args
   */
  public function __construct(array $args=[]) {
    $this->user_id = $args['user_id'] ?? '';
    $this->category_id = $args['category_id'] ?? '';
    $this->title = $args['title'] ?? '';
    $this->meta_desc = $args['meta_desc'] ?? '';
    $this->format = $args['format'] ?? '';
    $this->image = $args['image'] ?? '';
    $this->video = $args['video'] ?? '';
    $this->body = $args['body'] ?? '';
    $this->published = $args['published'] ?? '';
  }

  /**
   * Set File object to handle image upload and remove
   *
   * @param File $image_obj
   * @return void
   */
  public function fileInstance(File $image_obj) {
    $this->image_obj = $image_obj;
  }

  /**
   * Set Post form edit scenario
   * it can be 'create' or 'update'
   *
   * @param string $scenario
   * @param string $sess_user_id
   * @return void
   */
  public function formEditScenario(string $scenario, string $sess_user_id) {
    $scenarios = ['create','update'];
    if (in_array($scenario, $scenarios)) {
      $this->form_edit_scenario = [$scenario, $sess_user_id];
    }
  }

  /**
   * Set status of the Post
   *
   * @param string $cmd
   * @return boolean
   */
  public function setStatus(string $cmd) {
    switch($cmd) {
      case 'approve':
              $this->approved = '1';
              return true;
      case 'disapprove': 
              $this->approved = '0';
              return true;
      case 'publish': ;
              $this->published = '1';
              return true;
      case 'unpublish': ;
              $this->approved = '0';
              $this->published = '0';
              return true;
      default:
              return false;
    }
  }

  /**
   * Make post's excerpt of particular length
   *
   * @return string
   */
  public function excerpt(int $length=150) {
    return (substr(strip_tags($this->body), 0, $length) . '...');
  }

  /**
   * Overrides the parent's beforeValidation method
   * to manipulate or modify some Post attributes
   *
   * @param array $attr
   * @return array
   */
  protected function beforeValidation(array $attr) {
    foreach ($attr as $prop => $value) {
      if ($prop !== 'body') {
        if (!is_null($value)) {
          $this->$prop = trim(strip_tags($value));
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
    return parent::beforeValidation($attr);
  }

  /**
   * Overrides the parent's beforeSave method
   *  to manipulate or modify some attributes
   *
   * @param array $attr
   * @return array
   */
  protected function beforeSave(array $attr) {
    foreach ($attr as $prop => $value) {
      if ($prop == "published" && $value == '1') {

        $attr['published_at'] = is_null($this->published_at)
        ? date('Y-m-d H:i:s', time()) : $this->published_at; 
      }
    }
    return parent::beforeSave($attr);
  }

  /**
   * Validate the Post attributes that come from Post Form
   * Errors if they exists go into parent's errors property
   *
   * @return boolean
   */
  protected function validate() {
    $this->errors = [];

    if (is_blank($this->title)) {
      $this->errors[] = 'Title can not be blank.';
    } elseif (!has_length($this->title, ['max' => 200])) {
      $this->errors[] = 'Title must be less than 200 characters.';
    }
    if ($this->category_id == 0) {
      $this->errors[] = 'Please select a category.';
    }
    if (!is_blank($this->meta_desc)) {
      if (!has_length($this->meta_desc, ['max' => 160])) {
        $this->errors[] = 'Meta description can not be more than 160 character.';
      }
    }
    
    if (is_blank($this->body)) {
      $this->errors[] = 'Post content can not be blank.';
    }
    if (has_unallowed_tag($this->body, $this->allowable_tags)) {
      $this->errors[] = 'Post has not allowed html tag(s).';
    }
    if (has_external_link($this->body, $this->allowable_hosts)) {
      $this->errors[] = 'Post can not contain external links except YouTube and Vimeo.';
    }
    if (has_length_greater_than($this->body, 65000)) {
      $this->errors[] = 'Post can not contain more than 65000 characters.';
    }
    if (count(self::bodyShortcodeImage($this->body)) > POST_IMG_MAX_NUM) {
      $this->errors[] = 'Post can not contain more than ' . POST_IMG_MAX_NUM . ' images.';
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
      $this->errors[] = 'Post can not have more than 3 videos.';
    }

    return (empty($this->errors) == true);
  }

  /**
   * Returns number of video urls
   *
   * @return number
   */
  protected function videoUrlsCount() {
    if (isset($this->video_urls)) {
      return count( (array) json_decode($this->video_urls));
    } else {
      return 0;
    }
  }

  /**
   * Convert checkbox value to number
   *
   * @param string $property
   * @return void
   */
  protected function filterCheckboxValue(string $property) {
    if (in_array($this->{$property}, ['on','1','checked'])) {
      $this->$property = '1';
    } else {
      $this->$property = '0';
    }
  }

  /**
   * Find all links from the Post content
   * Parse them for youtube and vimeo video urls
   * The found video urls convert into embed urls
   * Keep data in json string as object property
   *
   * @return void
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

  /**
   * Get embed video url for post entry
   * 
   * @return void
   */
  protected function getEntryVideoUrl() {
    $host = parse_url($this->video)['host'];
    if ($host === 'www.youtube.com' || $host === 'youtu.be') {
      $embed_url = $this->getYoutubeEmbedUrl($this->video);
    } elseif ($host === 'vimeo.com') {
      $embed_url = $this->getVimeoEmbedUrl($this->video);
    }
    $this->video = json_encode([$this->video => $embed_url]);
  }

  /**
   * The save method overides parent save method
   * Before 'Post Save' it cares the file Upload
   * which is handled by $image_obj File instance
   * 
   * @return boolean
   */
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

  /**
   * Overrides parent's afterSave method
   *
   * @param int $id
   * @return void
   */
  protected function afterSave(int $id) {
    if (!empty($this->form_edit_scenario)) {
      $this->fill($id);
      if (!isset($this->image_obj)) $this->fileInstance(new File);
      
      $this->image_obj->temporaryDir($this->form_edit_scenario[1]);
      $this->image_obj->setResizeDimensions(self::$resize_dimensions);
      $images = self::bodyShortcodeImage($this->body);
      $dest = $this->bodyImagesDir();
      
      if ($this->form_edit_scenario[0] == 'create') {
        $this->image_obj->permanentDir($images, $dest, 'dir');
      } else if ($this->form_edit_scenario[0] == 'update') {
        $this->image_obj->permanentDir($images, $dest, 'file');
        $this->image_obj->dirInventory($images, $dest);
      }
    }
  }

  /**
   * Post body images destination
   * relative to images directory
   *
   * @return string
   */
  protected function bodyImagesDir() {
    $d = date('Y-m-d', strtotime($this->created_at));
    return str_replace('-', '/', $d) . '/' . $this->id;
  }

  /**
   * Deletes object of type Post
   *
   * @return boolean
   */
  public function delete() {
    if ($this->image) $this->deleteImages($this->image);
    $this->deleteImages($this->bodyImagesDir(), true);

    return parent::delete();
  }

  /**
   * Delete images of the Post
   *
   * @param string $image
   * @return void
   */
  protected function deleteImages(string $image, $is_dir=false) {
    if ($is_dir == false) {
      $this->image_obj->remove($image);
      list($noextimg, $ext) = explode('.', $image);

      foreach (self::$resize_dimensions as $d) {
        $resized = "/{$noextimg}_{$d['width']}.{$ext}";
        $this->image_obj->remove($resized);
      }

    } else {
      $this->image_obj->remove($image, true);
    }
  }

  /**
   * Resizes Image using Imagine\GD\Imagine PHP Library
   * Resizes them according to the widths and heights,
   * which given in $resize_dimensions Class property
   *
   * @param array $file
   * @return boolean
   */
  protected function resizeImage(array $file) {
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

  /**
   * Query the Approved Posts
   * Supports pagination
   *
   * @param integer $per_page
   * @param integer $offset
   * @return object[]
   */
  static public function queryApprovedPosts(int $per_page, int $offset) {
    $sql = self::getJoins();
    $sql .= <<<SQL
            WHERE p.approved = '1'
            ORDER BY p.published_at DESC
SQL;
    $sql .= " LIMIT {$per_page} OFFSET {$offset}";
    return self::findBySql($sql);
  }

  /**
   * Query the Search Posts
   * Supports pagination
   *
   * @param string $term
   * @param integer $per_page
   * @param integer $offset
   * @return object[]
   */
  static public function querySearchPosts(string $term, int $per_page, int $offset) {
    $term = self::$database->escape_string($term);
    $sql = self::getJoins();
    $sql .= <<<SQL
            WHERE p.approved = 1
              AND ( p.title LIKE '%$term%' OR p.body LIKE '%$term%' )
            ORDER BY p.published_at DESC
SQL;
    $sql .= " LIMIT {$per_page} OFFSET {$offset}";
    return self::findBySql($sql);
  }

  /**
   * Query the Category Posts
   * Supports pagination
   *
   * @param integer $category_id
   * @param integer $per_page
   * @param integer $offset
   * @return object[]
   */
  static public function queryPostsByCategory(int $category_id, int $per_page, int $offset) {
    $cid = parent::escape($category_id);
    $sql = self::getJoins();
    $sql .= <<<SQL
            WHERE p.approved = '1' AND p.category_id = $cid
            ORDER BY p.published_at DESC
SQL;
    $sql .= " LIMIT {$per_page} OFFSET {$offset}";
    return self::findBySql($sql);
  }

  /**
   * Query The Author Posts
   * Supports pagination
   *
   * @param integer $user_id
   * @param integer $per_page
   * @param integer $offset
   * @return object[]
   */
  static public function queryPostsByAuthor(int $user_id, int $per_page, int $offset) {
    $uid = parent::escape($user_id);
    $sql = self::getJoins();
    $sql .= <<<SQL
            WHERE p.approved = 1 AND p.user_id = $uid
            ORDER BY p.published_at DESC
SQL;
    $sql .= " LIMIT {$per_page} OFFSET {$offset}";
    return self::findBySql($sql);
  }

  /**
   * Query the Posts by date published
   * Supports pagination
   *
   * @param array $dates
   * @param integer $per_page
   * @param integer $offset
   * @return object[]
   */
  static public function queryPostsByDatePub(array $dates, int $per_page, int $offset) {
    $sql = self::getJoins();
    $sql .= <<<SQL
            WHERE p.approved = '1'
            AND ( p.published_at >= '{$dates['date_min']}'
            AND p.published_at < '{$dates['date_max']}' )
SQL;
    $sql .= " LIMIT {$per_page} OFFSET {$offset}";
    return self::findBySql($sql);
  }

  /**
   * Query Post by particular ids
   * Supports pagination
   *
   * @param [type] $ids
   * @param integer $per_page
   * @param integer $offset
   * @return object[]
   */
  static public function queryAllWhere($ids, int $per_page, int $offset) {
    foreach ($ids as $key => $pid) {
      $pid = strval($pid);
      $ids[$key] = parent::escape($pid);
    }
    $ids_str = implode(",", $ids);

    $sql = self::getJoins();
    $sql .= <<<SQL
          WHERE p.id in ($ids_str) AND p.approved = '1'
          ORDER BY p.published_at DESC
SQL;
    $sql .= " LIMIT {$per_page} OFFSET {$offset}";
    return self::findBySql($sql);
  }

  /**
   * Query Posts which have image format
   * 
   * @param integer $count
   * @return object[]
   */
  static public function queryImageFormatPosts(int $count=6) {
    $sql = self::getJoins();
    $sql .= <<<SQL
            WHERE p.approved = '1' AND format = 'image'
            ORDER BY p.published_at DESC
            LIMIT {$count};
SQL;
    return self::findBySql($sql);
  } 

  /**
   * Returns the common join part of sql query 
   * Join to the `users` and `categories` table
   * 
   * @return string
   */
  static protected function getJoins() {
    return <<<SQL
      SELECT p.*, u.username, t.name as category
      FROM `posts` AS p
      LEFT JOIN `users` AS u ON p.user_id = u.id
      LEFT JOIN `categories` AS t ON p.category_id = t.id
SQL;
  }

  /**
   * Create iframe for embed video url
   * Youtube/Vimeo
   *
   * @return string
   */
  public function getEntryVideo() {
    $this->videoSplitter();

    $url = $this->video['url'];
    $embed_url = $this->video['embed'];

    if ($this->format == 'video') {
      $youtube = '<iframe data-src="%s" class="embed-responsive-item lazyload"';
      $youtube .= ' frameborder="0" allowfullscreen></iframe>';
      $vimeo = '<iframe data-src="%s" class="embed-responsive-item lazyload"';
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

  /**
   * Prepare post's images and video to render
   *
   * @return string
   */
  public function renderPostContent() {
    $this->shortcodeToHtml();
    $this->videoUrlsToHtml();
    
    return $this->body;
  }

  /**
   * Convert image shortcode to image tag
   *
   * @return string
   */
  public function shortcodeToHtml() {
    $matches = self::bodyShortcodeImage($this->body, true)[1];
    $dir = $this->bodyImagesDir();

    foreach ($matches[1] as $i => $image) {
      $src = "/{$dir}/{$image}";
      $img = $matches[0][$i];
      $img = str_replace("[", "<", $img);
      $img = str_replace("]", ">", $img);
      $img = str_replace("src", "srcset", $img);
      $img = str_replace($image, self::responsive($src), $img);
      $this->body = str_replace($matches[0][$i], $img, $this->body);
    }

    return $this->body;
  }

  /**
   * Replace video urls in the Post content with video iframes
   * Youtube/Vimeo
   *
   * @return string
   */
  public function videoUrlsToHtml() {
    if (!isset($this->video_urls)) return $this->body;

    $video_urls = json_decode($this->video_urls);

    if (!empty($video_urls)) {
      foreach ($video_urls as $url => $embed_url) {
        $div = '<div class="embed-responsive embed-responsive-16by9">%s</div>';
        $youtube = '<iframe data-src="%s" class="embed-responsive-item lazyload"';
        $youtube .= ' controls="0" showinfo="0" frameborder="0" allowfullscreen></iframe>';
        $vimeo = '<iframe data-src="%s" class="embed-responsive-item lazyload"';
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

  /**
   * Converts youtube url into youtube embed url
   *
   * @param string $url
   * @return string | error
   */
  function getYoutubeEmbedUrl(string $url) {
    $both_urls_regex = "#(?<=v=|v\/|vi=|vi\/|youtu.be\/)[a-zA-Z0-9_-]{11}#";

    if (preg_match($both_urls_regex, $url, $matches)) {
      $youtube_id = $matches[count($matches) - 1];
      return 'https://www.youtube.com/embed/' . $youtube_id;
    } else {
      return false;
    }
  }

  /**
   * Converts vimeo url into youtube embed url
   *
   * @param string $url
   * @return string | error
   */
  function getVimeoEmbedUrl(string $url) {
    $vimeo_id = substr(parse_url($url, PHP_URL_PATH), 1);
    if ($vimeo_id) {
      return 'https://player.vimeo.com/video/' . $vimeo_id;
    } else {
      return false;
    }
  }

  /**
   * Converts $video attribute's json string value into array
   * which will contain url, embed url and provider of video
   *
   * @return void
   */
  function videoSplitter() {
    if (isset($this->video) && !empty($this->video)) {
      $arr = (array) json_decode($this->video);
      $url = key($arr);
      $this->video = [];
      $this->video['url'] = $url;
      $this->video['embed'] = $arr[$url];
      $host = parse_url($url)['host'];
      switch($host) {
        case "www.youtube.com":
        case "youtu.be":
              $this->video['provider'] = 'youtube';
              break;
        case 'vimeo.com':
              $this->video['provider'] = 'vimeo';
              break;
      }
    }
  }

  /**
   * Converts $video attribute array value into json string
   * where array key is video url, value is video embed url
   * 
   * @return string
   */
  function videoMerger() {
    if (is_array($this->video)) {
      $this->video = json_encode([
        $this->video['url'] => $this->video['embed']
      ]);
      return $this->video;
    }
  }
  
  /**
   * Creates img srcset attribute value with different sizes
   * It gets proper sizes from $resize_dimensions property 
   *
   * @param string $image
   * @param integer $depth
   * @return string
   */
  static public function responsive(string $image, int $depth = 0) {
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

  /**
   * Filter image format posts from array with posts
   *
   * @param array $posts
   * @return array
   */
  static public function filterImageFormat(array $posts) {
    $arr = [];
    foreach ($posts as $post) {
      if ($post->format == 'image') {
        $arr[] = $post;
      }
    }
    return $arr;
  }

  /**
   * Create Post body image shortcode
   *
   * @param string $image
   * @return string
   */
  static public function shortcode(string $image) {
    return "[img src=\"{$image}\" alt=\"\"]";
  }

  /**
   * Parse for images shortcodes in Post content
   *
   * @param string $body
   * @param boolean $m
   * @return void
   */
  static public function bodyShortcodeImage(string $body, bool $m=false) {
		$matches = [];
    preg_match_all('/\[img src=\"([^"]+).*?\]/', $body, $matches);
		$images = [];

		foreach($matches[1] as $match) {
			$arr = explode('.', $match);
			if (is_numeric($arr[0]) && strlen($arr[0]) == 14) {
				$images[] = implode('.', $arr);
			}
    }
    
    return ($m == false) ? $images : [$images, $matches];
  }
  
}
?>