<?php

require_once 'config.php';

if (class_exists('PDO')) {
    //echo 'Được phép sử dụng PDO';

    try {
        $dns = DRIVER.':dbname='.DB.';host='.HOST;

        $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", //Hỗ trợ tiếng việt
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION //Đẩy lỗi ra ngoại lệ khi gặp lỗi truy vấn (Sai lệnh câu lệnh SQL)
        ];
        $conn = new PDO(
            $dns,
            USER,
            PASS,
            $options
        );
    } catch(Exception $e) {
        require_once 'error.php';
        die();
    }
} else {
    echo 'Vui lòng kích hoạt PDO';
}
