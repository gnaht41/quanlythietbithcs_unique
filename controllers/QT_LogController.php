<?php
require_once __DIR__ . '/../models/QT_Log.php';

class LogController
{
    public function index()
    {
        // Chỉ Admin mới được xem log
        if (!isset($_SESSION['maVT']) || $_SESSION['maVT'] != 1) {
            header("Location: index.php");
            exit();
        }

        $logModel = new Log();
        $logs = $logModel->getAllLogs();

        require_once __DIR__ . '/../views/pages_quan-tri-vien/nhat-ky.php';
    }
}