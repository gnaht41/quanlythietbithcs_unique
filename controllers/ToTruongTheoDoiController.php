<?php
require_once __DIR__ . '/../models/ToTruongTheoDoiModel.php';

class ToTruongTheoDoiController
{
    private $model;

    public function __construct()
    {
        $this->model = new ToTruongTheoDoiModel();
    }

    public function index()
    {
        $maMH = $_GET['maMH'] ?? null;

        // Lấy danh sách môn học
        $monHoc = $this->model->getDanhSachMonHoc();

        // Lấy danh sách theo dõi
        $ds = $this->model->getDanhSachTheoDoi($maMH);

        require_once __DIR__ . '/../views/pages_to-truong/theo-doi-thiet-bi.php';
    }
}
