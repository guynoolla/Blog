<?php
declare(strict_types=1);

namespace App\Classes;

class File {

	public $file;
	public $images_path;
	protected $max_file_size;
	protected $file_info = [];
	protected $default = 'default.png';
	public $error = "";

	public function __construct($file=null) {
		$this->file = $file;
		$this->images_path = IMAGES_PATH;
		$this->max_file_size = MAX_FILE_SIZE;
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
						$this->error = 'The uploaded file was too large.';
						break;
			case 3:
						$this->error = 'The file was only partially uploaded.';
						break;
			case 6:
			case 7:
			case 8:
						$this->error = 'The file could not be uploaded due to a system error.';
						break;
			case 4:
			default:
					$this->error = 'No file was uploaded.';
					break;
		}
		return $this->error;
	}

	public function handleUpload($field_name, array $ratio=[]) {
    if (!$this->isFileUploaded()) {
			return $this->getUploadError();
    } else {
      return $this->moveFile($field_name, $ratio);
    }
	}

	public function moveFile($attr, $ratio) {
		list ($w, $h) = getimagesize($this->file['tmp_name']);
		$img_ratio = $w/$h;

		if (!empty($ratio)) {
			if ($img_ratio < $ratio['min'] || $img_ratio > $ratio['max'] ) {
				$this->error .= 'Image aspect ratio (width x height) must be between 7x5 9x5) ';
			}
		}

		$size = ROUND($this->file['size']/1024);
		$limit = $this->max_file_size/1024;
		if ($size > $limit) {
			$this->error .= 'The uploaded file must not be larger than ' . $limit . 'KB. ';
		}

		if (!$this->error) {
			$ext = substr(strrchr($this->file['name'], '.'), 1);
			$id = time().rand(1000, 9999);
			$img = $id . '.' . $ext;
			$date_path = date('Y') . '/' . date('m') . '/' . date('d');
			$dir_path = $this->images_path . '/' . $date_path;
			if (!is_dir( $dir_path)) {
				mkdir( $dir_path, 0777, true );
				$filename = $dir_path . '/' . $img;
			} else {
				$filename = $dir_path . '/' . $img;
			}

			if (move_uploaded_file($this->file['tmp_name'], $filename)) {
				$this->file_info['date_path'] = $date_path;
				$this->file_info['dir_path'] = $dir_path;
				$this->file_info['filename'] = $filename;
				$this->file_info['id'] = $id;
				$this->file_info['img'] = $img;
				$this->file_info['ext'] = $ext;
				$this->file_info['size'] = $size;
				$this->file_info[$attr] = "/$date_path/$img";

			} else {
				$this->error .= 'The file could not be moved.';
				@unlink( $this->file['tmp_name'] );
			}
		}

		return $this->error;
	}

	public function remove($attr_value) {
		$filename = $this->images_path . '/' . $attr_value;

		if (file_exists($filename) && is_file($filename)) {
			unlink($filename);
		}
	}

}
?>