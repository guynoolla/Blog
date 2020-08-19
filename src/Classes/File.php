<?php
declare(strict_types=1);

namespace App\Classes;

class File {

	public $file;
	public $images_path;
	protected $max_file_size;
	protected $file_info = [];
	protected $default = 'default.png';
	protected $cache = false;

	public function __construct($file=null) {
		$this->file = $file;
		$this->images_path = IMAGES_PATH;
		$this->max_file_size = MAX_FILE_SIZE;
	}

	// 604800 seconds is one week 60*60*24*7
	public function cache($cache_time=604800) {
		$this->cache = $cache_time;
	}

	public function getFileInfo() {
		return $this->file_info;
	}

	public function isFileSelected() {
		return (!empty($this->file['name']));
	}

	public function isFileUploaded() {
		return (
			is_uploaded_file($this->file['tmp_name'])
			&& ($this->file['error'] == UPLOAD_ERR_OK)
		);
	}

	public function getUploadError() {
		switch($this->file['error']) {
			case 1:
			case 2:
				return 'The uploaded file was too large.';
			case 3:
				return 'The file was only partially uploaded.';
			case 6:
			case 7:
			case 8:
				return 'The file could not be uploaded due to a system error.';
			case 4:
			default:
				return 'No file was uploaded.';
		}
	}

	public function moveFile($attr) {
		$error = false;

		$size = ROUND($this->file['size']/1024);
		$limit = $this->max_file_size/1024;
		if ($size > $limit) {
			$error = 'The uploaded file must not be larger than ' . $limit . 'KB.';
		}

		if (!$error) {
			$file_ext = substr(strrchr($this->file['name'], '.'), 1);
			$file_name = time().rand(1000, 9999) . '.' . $file_ext;
			$date_path = date('Y') . '/' . date('m') . '/' . date('d');
			$dir_path = $this->images_path . '/' . $date_path;
			if (!is_dir( $dir_path)) {
				mkdir( $dir_path, 0777, true );
				$file_path = $dir_path. '/' . $file_name;
			} else {
				$file_path = $dir_path . '/' . $file_name;
			}

			if (move_uploaded_file($this->file['tmp_name'], $file_path)) {
				$this->file_info['dir_path'] = $dir_path;
				$this->file_info[$attr] = "/$date_path/$file_name";
				$this->file_info['file_path'] = $file_path;
				$this->file_info['file_name'] = $file_name;
				$this->file_info['date_path'] = $date_path;
				$this->file_info['extension'] = $file_ext;
				$this->file_info['size'] = $size;
		
			} else {
				$error = 'The file could not be moved.';
				@unlink( $this->file['tmp_name'] );
			}
		}

		return $error;
	}

	public function remove($attr_value) {
		$dest = $this->images_path . '/' . $attr_value;

		if (file_exists($dest) && is_file($dest)) {
			unlink($dest);
		}
	}

	public function render($attr_value) {
		$dest = $this->images_path . '/' . $attr_value;

		if (!file_exists($dest)) { $dest = $this->images_path . '/' . $this->default; }

		$file_ext = substr(strrchr($attr_value, '.'), 1);

		if ($this->cache) {
			header("Cache-Control: private, max-age=" . $this->cache);
			header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $this->cache) . ' GMT');
		}
		header("Content-Type: image/$file_ext");
		header('Content-Length: ' . filesize($dest));

		readfile($dest);
	}

}
?>