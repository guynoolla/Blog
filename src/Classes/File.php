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
	public $allowed_img_ext = ['gif', 'jpg', 'jpeg', 'png'];
	public $error = "";

	protected $resize_dimensions;
	protected $upload_to_temp = false;
	protected $max_file_size;
	protected $file_info = [];
	protected $default = 'default.png';

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

	public function setResizeDimensions($dimensions) {
		$this->resize_dimensions = $dimensions;
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
	 * @return array | string
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
	 * @return array | string
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

		$ext = substr(strrchr($this->file['name'], '.'), 1);
		$id = time().rand(1000, 9999);
		
		if (!in_array($ext, $this->allowed_img_ext)) {
			$this->error .= 'This file extension is not allowed!';
		}

		if (!$this->error) {
			$img = "{$id}.{$ext}";

			if ($this->upload_to_temp == true) {
				$dir_path = "{$this->images_path}/{$_SESSION['file_temporary_dir']}";
				$date_path = "";
			} else {
				$date_path = date('Y') . '/' . date('m') . '/' . date('d');
				$dir_path = "{$this->images_path}/{$date_path}";
			}

			if (!is_dir( $dir_path)) {
				mkdir( $dir_path, 0777, true );
				$filename = "{$dir_path}/{$img}";
			} else {
				$filename = "{$dir_path}/{$img}";
			}

			if (move_uploaded_file($this->file['tmp_name'], $filename)) {
				$this->file_info['date_path'] = $date_path;
				$this->file_info['dir_path'] = $dir_path;
				$this->file_info['filename'] = $filename;
				$this->file_info['id'] = $id;
				$this->file_info['img'] = $img;
				$this->file_info['ext'] = $ext;
				$this->file_info['size'] = $size;
				$this->file_info[$attr] = "/{$date_path}/{$img}";

			} else {
				$this->error .= 'The file could not be moved.';
				@unlink($this->file['tmp_name']);
			}
		}

		return $this->error;
	}

  /**
   * Resizes Image using Imagine\GD\Imagine PHP Library
   * Resizes them according to the widths and heights,
   * which given in resize_dimensions method argument
   *
   * @param array resize_dimensions
   * @return boolean
   */
  public function resizeImage($image, $dest) {
		$filename = "{$dest}/{$image}";
		
		if (file_exists($filename)) {
			list($w, $h) = getimagesize($filename);
			$arr = explode('.', $image);
			$fid = $arr[0];
			$ext = $arr[1];
	
			foreach ($this->resize_dimensions as $d) {
				$max_height = $d['width'];
	
				if ($w > $d['width'] || $h > $max_height) {
					$imagine = new \Imagine\Gd\Imagine();
					$imagine->open($filename)
						->thumbnail(new \Imagine\Image\Box($d['width'], $max_height))
						->save("{$dest}/{$fid}_{$d['width']}.{$ext}");
				}
			}
			
			return true;
		}
  }

	/**
	 * Create user temporary upload dir path
	 *
	 * @param string $unique
	 * @return void
	 */
	public function temporaryDir(string $unique) {
		$this->upload_to_temp = true;
		$_SESSION['file_temporary_dir'] = 'temp/' . $unique;
	}	

	/**
	 * Move images from temporary dir to permanent dir
	 * if file id (fid) is not in images array remove it
	 * The 3rd $rename argument can be 'dir' or 'file'
	 *
	 * @param array $images
	 * @param string $dest
	 * @param string $rename
	 * @return void
	 */
	public function permanentDir(array $images, string $dest, string $rename='dir') {
		$temp = "{$this->images_path}/{$_SESSION['file_temporary_dir']}";
		$dest = "{$this->images_path}/{$dest}";
		$files = [];

		if (is_dir($temp) && !empty($images)) {
			$files = glob("{$temp}/*");

			foreach ($files as $file) {
				$arr = explode('/', $file);
				$fid = $arr[count($arr) - 1];
				
				if (!in_array($fid, $images)) {
					$this->remove("{$_SESSION['file_temporary_dir']}/{$fid}");
				} else {
					if ($rename == 'file') {
						if (!is_dir( $dest)) {
							mkdir( $dest, 0777, true );
						}
						rename("{$temp}/{$fid}", "{$dest}/{$fid}");
					}
				}
			}

			if ($rename == 'dir') rename($temp, $dest);

			foreach ($images as $image) {
				$this->resizeImage($image, $dest);
			}

			if ($rename == 'file') @rmdir($temp);
		}

		unset($_SESSION['file_temporary_dir']);
	}

	/**
	 * Check for images inside permanent directory
	 * if file id is not in images array remove it
	 *
	 * @param array $images
	 * @param string $dest
	 * @return void
	 */
	public function dirInventory(array $images, string $dest) {
		$filename = "{$this->images_path}/{$dest}";

		if (is_dir($filename)) {
			$files = glob("{$filename}/*");

			foreach ($files as $file) {
				$arr = explode('/', $file);
				$fid = $arr[count($arr) - 1];
				$w = "";
				
				if (strpos($fid, "_") !== false) {
					list($noextimg, $ext) = explode('.', $fid);
					list($numbers, $w) = explode("_", $noextimg);
					$fid = "{$numbers}.{$ext}";
				}

				if (!in_array($fid, $images)) {
					if ($w) {
						$fid = "{$numbers}_{$w}.{$ext}";
						$this->remove("{$dest}/{$fid}");
					} else {
						$this->remove("{$dest}/{$fid}");
					}
				}
			}
		}
	}

	/**
	 * Removes file or directory with files
	 * if 2nd argument is true removes directory
	 *
	 * @param string $image
	 * @param bool $is_dir
	 * @return void
	 */
	public function remove(string $image, $is_dir=false) {
		$filename = $this->images_path . '/' . $image;

		if ($is_dir == false) {
			if (file_exists($filename) && is_file($filename)) {
				@unlink($filename);
				return true;
			}
		} else {
			if (is_dir($filename)) {
				foreach (glob("{$filename}/*") as $file) {
					if (is_file($file)) @unlink($file);
				}
				@rmdir($filename);
				return true;
			}
		}

		return false;
	}

	/**
	 * Get SESSION temporary directory
	 *
	 * @return string | error
	 */
	public function getTemporaryDir() {
		if (isset($_SESSION['file_temporary_dir'])) {
			return $_SESSION['file_temporary_dir'];
		} else {
			return false;
		}
	}

	/**
	 * Check if directory is empty
	 *
	 * @param string $dir
	 * @return void
	 */
	protected function dir_is_empty(string $dir) {
		$handle = opendir($dir);
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				closedir($handle);
				return FALSE;
			}
		}
		closedir($handle);
		return TRUE;
	}

}
?>