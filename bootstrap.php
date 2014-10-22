<?php
	define('DS', DIRECTORY_SEPARATOR);
	define('WWW_ROOT', __DIR__ . DS);
	// 上传路径
	define('SAVE_PATH', sprintf('/uploads/%s/%s/%s/', date('Y'), date('m'), date('d')));
	define('UPLOAD_PATH', WWW_ROOT . SAVE_PATH);
	// 上传文件限制 5M
	define('UPLOAD_SIZE', 5 * 1024 * 1024);

	// 缩略图像素
	define('IMAGE_WIDTH', 640);
	define('IMAGE_HEIGHT', 640);

	require WWW_ROOT . 'vendor' . DS . 'autoload.php';

	// 数据库连接
	try {
		dibi::connect(
			array(
				'driver' => 'sqlite3',
				'database' => WWW_ROOT . 'data' . DS . 'db.s3db.db',
				//'password' => 'QAZWSX741236985'
			)
		);

	} catch (DibiException $e) {
		echo get_class($e), ': ', $e->getMessage(), "\n";
	}

