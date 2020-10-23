<?php
declare(strict_types=1);

namespace App\Classes;

/**
 * Class File for Image upload
 * 
 * Class main method moveFile returns an array of uploaded
 * file information or error if for some reason it occures 
 */
class File {

	public $file;
	public $images_path;
	protected $max_file_size;
	protected $file_info = [];
	protected $default = 'default.png';
	public $error = "";

	/**
	 * Class constructor
	 * 
	 * Constructor becomes global $_FILES[filename] as argument
	 * It initializes images_path and max_file_size properties
	 *
	 * @param [type] $file
	 */
	public function __construct($file=null) {
		$this->file = $file;
		$this->images_path = IMAGES_PATH;
		$this->max_file_size = MAX_FILE_SIZE;
	}

	/**
	 * Return uploaded file information
	 *
	 * @return array
	 */
	public function getFileInfo() {
		return $this->file_info;
	}

	/**
	 * Detect if user selected a file for upload
	 *
	 * @return boolean
	 */
	public function isFileSelected() {
		return (!empty($this->file['name']));
	}

	/**
	 * Check if file was uploaded
	 *
	 * @return boolean
	 */
	public function isFileUploaded() {
		return (
			is_uploaded_file($this->file['tmp_name'])
			&& ($this->file['error'] == UPLOAD_ERR_OK)
		);
	}

	/**
	 * Check if an error occured by file upload
	 *
	 * @return string
	 */
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

	/**
	 * Handle file upload
	 *
	 * @param string $field_name
	 * @param array $ratio
	 * @return void
	 */
	public function handleUpload(string $field_name, array $ratio=[]) {
    if (!$this->isFileUploaded()) {
			return $this->getUploadError();
    } else {
      return $this->moveFile($field_name, $ratio);
    }
	}

	/**
	 * Move file to its final destination
	 * 
	 * Check for image size and image ratio
	 * Gather all information into an array
	 *
	 * @param string $attr
	 * @param array $ratio
	 * @return void
	 */
	public function moveFile(string $attr, array $ratio) {
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
				@unlink($this->file['tmp_name']);
			}
		}

		return $this->error;
	}

	/**
	 * Remove file from its location
	 *
	 * @param string $attr_value
	 * @return void
	 */
	public function remove(string $attr_value) {
		$filename = $this->images_path . '/' . $attr_value;

		if (file_exists($filename) && is_file($filename)) {
			@unlink($filename);
		}
	}

}
?>