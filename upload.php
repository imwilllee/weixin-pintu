<?php
	if (empty($_FILES['file'])) {
		exit('error');
	}
	require 'bootstrap.php';
	use Intervention\Image\ImageManagerStatic as Image;

	header('Content-type: application/json');

	$response = array('err_code' => 1);

	$config = array(
		'save_dir' => UPLOAD_PATH,
		'save_name_rule' => 'md5',
		'allow_exts' => array('jpg', 'jpeg', 'gif', 'png'),
		'max_size' => UPLOAD_SIZE
	);

	$uploader = new Uploader();
	$uploader->setConfig($config);
	if ($file = $uploader->upload($_FILES['file'])) {
		$oldSavePath = $file['save_dir'] . $file['save_name'];
		$resizeSavePath = UPLOAD_PATH . 's_' . $file['save_name'];
		Image::configure(array('driver' => 'gd'));
		$img = Image::make($oldSavePath)->resize(IMAGE_WIDTH, IMAGE_HEIGHT);
		if (!$img->save($resizeSavePath)) {
			$response['err_msg'] = '图片缩放失败！';
		} else {
			$data = array(
				'original_file_path' => SAVE_PATH . $file['save_name'],
				'small_file_path' => SAVE_PATH . 's_' . $file['save_name']
			);
			if (dibi::query('INSERT INTO items', $data)) {
				$response['err_code'] = 0;
				$response['item_id'] = dibi::getInsertId();
			} else {
				$response['err_msg'] = '数据保存失败！';
			}
		}
	} else {
		$response['err_msg'] = $uploader->getErrorMessage();
	}
	echo json_encode($response);

	function size_format($bytes, $decimals = 0) {
		$quant = array(
			'TB' => 1099511627776,
			'GB' => 1073741824,
			'MB' => 1048576,
			'kB' => 1024,
			'B ' => 1
		);
		foreach ($quant as $unit => $mag)
			if (doubleval($bytes) >= $mag) {
				return number_format($bytes / $mag, $decimals) . ' ' . $unit;
			}
		return false;
	}

class Uploader {

/**
 * 允许上传文件mime类型
 * @var array
 */
	protected $_allowedMime = array(
		// 图片类型
		'jpg' => array('image/jpg', 'image/jpeg', 'image/pjpeg'),
		'jpeg' => array('image/jpg', 'image/jpeg', 'image/pjpeg'),
		'gif' => array('image/gif'),
		'png' => array('image/png', 'image/x-png')
	);

/**
 * 默认配置项
 * @var array
 */
	protected $_config = array(
		// 保存目录
		'save_dir' => __DIR__,
		// 目录创建权限
		'mkdir_mode' => 0755,
		// 保存文件名规则
		// 支持规则md5、uuid、datetime、timestamp
		// 也可指定其他值，则以该值为文件名保存
		// 默认false为以上传文件名保存
		'save_name_rule' => false,
		// 上传文件大小限制(字节)
		// 默认2M
		'max_size' => 2097152,
		// 允许上传文件扩展名
		// 数组形式 如 array('jpg', 'gif', 'png')
		// 默认为'all' 可以上传任意文件
		'allow_exts' => 'all',
		// 上传文件后预览地址
		// 如'/uploads/avatar/%s'
		// %s代表上传后的文件名
		// 默认false
		'preview_url' => false
	);

/**
 * 单个文件上传错误信息
 * @var string
 */
	protected $_errorMessage = null;

/**
 * 上传文件信息
 * @var array
 */
	protected $_file = null;

/**
 * 多文件信息
 * @var array
 */
	protected $_files = null;

/**
 * 设置上传配置
 *
 * @param array $config 配置项
 * @return void
 */
	public function setConfig($config) {
		$this->_config = array_merge($this->_config, $config);
	}

/**
 * 文件上传
 *
 * @param array $file 上传文件信息
 * @return boolean
 */
	public function upload($file) {
		$this->_file = $file;
		unset($file);
		if ($this->__checkUploadFile()) {
			$fileSavePath = $this->__getFileSavePath();
			if ($fileSavePath !== false) {
				if (move_uploaded_file($this->_file['tmp_name'], $fileSavePath)) {
					unset($this->_file['tmp_name']);
					return $this->_file;
				}
			}
		}
		if (file_exists($this->_file['tmp_name'])) {
			@unlink($this->_file['tmp_name']);
		}
		return false;
	}

/**
 * 返回单个文件上传错误信息
 *
 * @return string
 */
	public function getErrorMessage() {
		return $this->_errorMessage;
	}

/**
 * 上传文件检查
 *
 * @return boolean
 */
	private function __checkUploadFile() {
		// 上传错误
		if ($this->_file['error'] > 0) {
			$this->__getFileErrorMessage();
			return false;
		}
		if (empty($this->_file['tmp_name']) || empty($this->_file['name'])) {
			$this->_errorMessage = '没有选择上传文件。';
			return false;
		}
		if (!is_uploaded_file($this->_file['tmp_name'])) {
			$this->_errorMessage = '非法上传文件！';
			return false;
		}

		// 最大文件大小限制
		if ($this->_file['size'] > $this->_config['max_size']) {
			$this->_errorMessage = sprintf('上传文件大小超出了最大限制%s。', size_format($this->_config['max_size']));
			return false;
		}
		// 上传文件扩展名检查
		$this->_file['extension'] = strtolower(pathinfo($this->_file['name'], PATHINFO_EXTENSION));

		if ($this->_config['allow_exts'] !== 'all') {
			if (is_string($this->_config['allow_exts'])) {
				$this->_config['allow_exts'] = explode(',', $this->_config['allow_exts']);
			}
			if (!in_array($this->_file['extension'], $this->_config['allow_exts'])) {
				$this->_errorMessage = sprintf('只允许上传%s类型的文件。', implode(',', $this->_config['allow_exts']));
				return false;
			}
			// 上传文件mime类型判断
			if (!$this->__checkFileMimeType()) {
				$this->_errorMessage = '上传文件MIME类型不在允许类型范围之内。';
				return false;
			}
		}
		return true;
	}

/**
 * 上传文件mime类型检查
 *
 * @return boolean
 */
	private function __checkFileMimeType() {
		// 如果开启了php_fileinfo扩展
		if (function_exists('finfo_file')) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$this->_file['type'] = finfo_file($finfo, $this->_file['tmp_name']);
			finfo_close($finfo);
		}
		if (!isset($this->_allowedMime[$this->_file['extension']])) {
			return false;
		}
		$this->_file['type'] = strtolower($this->_file['type']);
		if (!in_array($this->_file['type'], $this->_allowedMime[$this->_file['extension']])) {
			return false;
		}
		return true;
	}

/**
 * 上传文件错误检查
 *
 * @return void
 */
	private function __getFileErrorMessage() {
		switch ($this->_file['error']) {
			case 1:
				$this->_errorMessage = '上传的文件超过了 php.ini中 upload_max_filesize 选项的限制。';
				break;
			case 2:
				$this->_errorMessage = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项的限制。';
				break;
			case 3:
				$this->_errorMessage = '文件只有部分被上传。';
				break;
			case 4:
				$this->_errorMessage = '没有文件被上传。';
				break;
			case 6:
				$this->_errorMessage = '找不到临时文件夹。';
				break;
			case 7:
				$this->_errorMessage = '文件写入失败。';
				break;
			case 8:
				$this->_errorMessage = 'php扩展导致上传终止。';
				break;
			default:
				$this->_errorMessage = '未知上传错误。';
		}
	}

/**
 * 取得上传文件保存目录
 *
 * @return string
 */
	private function __getFileSavePath() {
		if (!is_dir($this->_config['save_dir'])) {
			if (!mkdir($this->_config['save_dir'], $this->_config['mkdir_mode'], true)) {
				$this->_errorMessage = '文件保存目录创建失败。';
				return false;
			}
		}
		if (!is_writeable($this->_config['save_dir'])) {
			$this->_errorMessage = '文件保存目录没有写入权限。';
			return false;
		}
		$this->_file['save_dir'] = $this->_config['save_dir'];
		$saveName = $this->__getFileSaveName();
		$this->_file['save_name'] = $saveName;
		return $this->_config['save_dir'] . $saveName;
	}

/**
 * 取得保存文件名
 *
 * @return string
 */
	private function __getFileSaveName() {
		if ($this->_config['save_name_rule'] === false) {
			return $this->_file['name'];
		}
		$saveName = null;
		switch ($this->_config['save_name_rule']) {
			case 'md5':
				$saveName = md5_file($this->_file['tmp_name']);
				break;
			case 'datetime':
				$saveName = date('YmdHis');
				break;
			case 'timestamp':
				$saveName = time();
				break;
			default:
				$saveName = $this->_config['save_name_rule'];
				break;
		}
		if ($this->_config['save_name_rule'] != 'md5') {
			$this->_file['md5_hash'] = md5_file($this->_file['tmp_name']);
		} else {
			$this->_file['md5_hash'] = $saveName;
		}
		return $saveName . '.' . $this->_file['extension'];
	}
}
