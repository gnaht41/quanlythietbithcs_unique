<?php
// controllers/PhieuMuonController.php

require_once __DIR__ . '/../models/VV_PhieuMuon.php';

class PhieuMuonController
{
    private $model;

    public function __construct()
    {
        $this->model = new PhieuMuonModel();
    }

    /* ==============================
       HIỂN THỊ DANH SÁCH PHIẾU MƯỢN
       ============================== */
    public function index()
{
    $keyword   = $_GET['keyword']   ?? '';
    $trangThai = $_GET['trangThai'] ?? '';
    $tuNgay    = $_GET['tuNgay']    ?? '';
    $denNgay   = $_GET['denNgay']   ?? '';

    $model = new PhieuMuonModel();

    // Nếu có lọc
    if ($keyword || $trangThai || $tuNgay || $denNgay) {
        $danhSach = $model->getDanhSachLoc($keyword, $trangThai, $tuNgay, $denNgay);
    } else {
        $danhSach = $model->getAll();
    }

    return [
        'danhSach' => $danhSach
    ];
}


    /* ==============================
       XEM CHI TIẾT PHIẾU MƯỢN
       ============================== */
    public function chiTiet()
    {
        $maPhieu = $_GET['maPhieu'] ?? 0;
        if ($maPhieu <= 0) {
            die('Mã phiếu không hợp lệ');
        }

        $phieu = $this->model->getById($maPhieu);
        $thietBi = $this->model->getChiTiet($maPhieu);

        return [
            'phieu'   => $phieu,
            'thietbi' => $thietBi
        ];
    }

    /* ==============================
       DUYỆT PHIẾU
       ============================== */
    public function duyet()
    {
        $maPhieu = $_GET['maPhieu'] ?? 0;
        if ($maPhieu <= 0) return;

        $this->model->updateTrangThai($maPhieu, 'Đã duyệt');

        $_SESSION['success'] = "Đã duyệt phiếu #$maPhieu";
        header("Location: nhan-vien-thiet-bi.php?tab=phieu-muon");
        exit;
    }

    /* ==============================
       TỪ CHỐI PHIẾU
       ============================== */
    public function tuChoi()
    {
        $maPhieu = $_GET['maPhieu'] ?? 0;
        if ($maPhieu <= 0) return;

        $this->model->updateTrangThai($maPhieu, 'Từ chối');

        $_SESSION['success'] = "Đã từ chối phiếu #$maPhieu";
        header("Location: nhan-vien-thiet-bi.php?tab=phieu-muon");
        exit;
    }

    /* ==============================
       TRẢ THIẾT BỊ
       ============================== */
    public function traThietBi()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

    $maPhieu   = $_POST['maPhieu'] ?? 0;
    $tinhTrang = $_POST['tinhTrang'] ?? [];

    if ($maPhieu <= 0 || empty($tinhTrang)) {
        $_SESSION['error'] = "Dữ liệu không hợp lệ";
        header("Location: nhan-vien-thiet-bi.php?tab=phieu-muon");
        exit;
    }

    // ENUM hợp lệ từ CSDL
    $dsTinhTrangHopLe = ['Tốt', 'Hư', 'Mất'];

    foreach ($tinhTrang as $maTB => $tt) {

        if (!in_array($tt, $dsTinhTrangHopLe)) {
            $_SESSION['error'] = "Tình trạng thiết bị không hợp lệ";
            header("Location: nhan-vien-thiet-bi.php?tab=phieu-muon");
            exit;
        }

        // Update từng thiết bị
        $this->model->updateTinhTrangKhiTra(
            $maPhieu,
            $maTB,
            $tt
        );
    }

    // Update phiếu mượn
    $ngayTra = date('Y-m-d');
    $this->model->traThietBi($maPhieu, $ngayTra);

    $_SESSION['success'] = "Đã trả thiết bị cho phiếu #$maPhieu";
    header("Location: nhan-vien-thiet-bi.php?tab=phieu-muon");
    exit;
}


    /* ==============================
       TẠO PHIẾU MƯỢN (NẾU CẦN)
       ============================== */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $ngayMuon = $_POST['ngayMuon'];
        $ngayTraDuKien = $_POST['ngayTraDuKien'];
        $maND = $_SESSION['maND'];

        $maPhieu = $this->model->insert($ngayMuon, $ngayTraDuKien, $maND);

        if (!empty($_POST['thietBi'])) {
            foreach ($_POST['thietBi'] as $maTB => $soLuong) {
                $this->model->insertChiTiet($maPhieu, $maTB, $soLuong);
            }
        }

        $_SESSION['success'] = "Tạo phiếu mượn thành công";
        header("Location: index.php");
        exit;
    }

    /* ==============================
       XÓA PHIẾU MƯỢN
       ============================== */
    public function delete()
    {
        $maPhieu = $_GET['maPhieu'] ?? 0;
        if ($maPhieu <= 0) return;

        $this->model->delete($maPhieu);

        $_SESSION['success'] = "Đã xóa phiếu #$maPhieu";
        header("Location: index.php");
        exit;
    }
public function duyetNhieu()
{
    $dsMaPhieu = $_POST['maPhieu'] ?? [];
    if (empty($dsMaPhieu)) return;

    $dem = 0;

    foreach ($dsMaPhieu as $maPhieu) {
        // CHỈ duyệt phiếu đang chờ duyệt
        if ($this->model->isChoDuyet($maPhieu)) {
            $this->model->updateTrangThai($maPhieu, 'Đã duyệt');
            $dem++;
        }
    }

    if ($dem > 0) {
        $_SESSION['success'] = "Đã duyệt $dem phiếu hợp lệ.";
    }

    header("Location: ?tab=phieu-muon");
    exit;
}
public function tuChoiNhieu()
{
    if (empty($_POST['maPhieu'])) {
        header('Location: ?tab=phieu-muon');
        exit;
    }

    $dsMaPhieu = $_POST['maPhieu'];
    $dem = 0;

    foreach ($dsMaPhieu as $maPhieu) {
        // CHỈ từ chối nếu đang CHỜ DUYỆT
        if ($this->model->isChoDuyet($maPhieu)) {
            $this->model->updateTrangThai($maPhieu, 'Từ chối');
            $dem++;
        }
    }

    if ($dem > 0) {
        $_SESSION['success'] = "Đã từ chối $dem phiếu hợp lệ.";
    }

    header('Location: ?tab=phieu-muon');
    exit;
}


}
