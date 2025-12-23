<?php
// File: controllers/KeHoachThanhLyController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền
function checkPermission($allowedRoles = [3, 4, 5])
{
    if (!isset($_SESSION['maND'])) {
        return ['success' => false, 'message' => 'Vui lòng đăng nhập để tiếp tục'];
    }

    if (!empty($allowedRoles) && !in_array($_SESSION['maVT'], $allowedRoles)) {
        return ['success' => false, 'message' => 'Bạn không có quyền thực hiện chức năng này'];
    }

    return ['success' => true];
}

require_once __DIR__ . '/../models/VV_KeHoachThanhLy.php';

class KeHoachThanhLyController
{
    private $model;

    public function __construct()
    {
        $this->model = new KeHoachThanhLyModel();
    }

    public function getDanhSach(): array
    {
        $permissionCheck = checkPermission([3, 4, 5]);
        if (!$permissionCheck['success']) return $permissionCheck;

        $danhSach = $this->model->getDanhSachKeHoach();

        return ['success' => true, 'data' => $danhSach];
    }

    public function getChiTiet($maTL): array
    {
        $permissionCheck = checkPermission([3, 4, 5]);
        if (!$permissionCheck['success']) return $permissionCheck;

        if (empty($maTL) || !is_numeric($maTL)) {
            return ['success' => false, 'message' => 'Mã kế hoạch không hợp lệ'];
        }

        $keHoach = $this->model->getChiTietKeHoach($maTL);
        if (!$keHoach) {
            return ['success' => false, 'message' => 'Không tìm thấy kế hoạch thanh lý'];
        }

        return ['success' => true, 'data' => $keHoach];
    }

    public function getThietBiCanThanhLy(): array
    {
        $permissionCheck = checkPermission([3, 4, 5]);
        if (!$permissionCheck['success']) return $permissionCheck;

        $dsThietBi = $this->model->getThietBiCanThanhLy();
        return ['success' => true, 'data' => $dsThietBi];
    }

    public function kiemTraSoLuong($maTB, $soLuongYeuCau): array
    {
        $permissionCheck = checkPermission([3, 4, 5]);
        if (!$permissionCheck['success']) return $permissionCheck;

        if (empty($maTB) || empty($soLuongYeuCau)) {
            return ['success' => false, 'message' => 'Thiếu thông tin thiết bị'];
        }

        return $this->model->kiemTraSoLuong($maTB, $soLuongYeuCau);
    }

    /**
     * Lập kế hoạch thanh lý mới
     */
    public function lapKeHoach($inputData = null): array
    {
        $permissionCheck = checkPermission([3, 4, 5]);
        if (!$permissionCheck['success']) return $permissionCheck;

        $maND = $_SESSION['maND'] ?? null;
        if (!$maND) {
            return ['success' => false, 'message' => 'Không xác định được người dùng.'];
        }

        $data = $inputData ?? $_POST;

        // Thông tin kế hoạch chính
        $phuongPhap = trim($data['phuongPhapThanhLy'] ?? '');
        $allowedPhuongPhap = ['Bán phế liệu', 'Chuyển giao', 'Tiêu hủy', 'Khác'];
        if (!in_array($phuongPhap, $allowedPhuongPhap)) {
            return ['success' => false, 'message' => 'Phương pháp thanh lý không hợp lệ.'];
        }

        $keHoach = [
            'ngayLap'           => $data['ngayLap'] ?? date('Y-m-d'),
            'phuongPhapThanhLy' => $phuongPhap,
            'ghiChu'            => trim($data['ghiChu'] ?? '')
        ];

        // Xử lý chi tiết thiết bị
        $chiTietList = [];

        if (!empty($data['chiTiet']) && is_array($data['chiTiet'])) {
            // AJAX cũ
            foreach ($data['chiTiet'] as $item) {
                $soLuong = (int)($item['soLuong'] ?? 0);
                $tinhTrang = trim($item['tinhTrang'] ?? '');
                if ($soLuong > 0 && !empty($tinhTrang)) {
                    $chiTietList[] = [
                        'maTB'      => (int)$item['maTB'],
                        'soLuong'   => $soLuong,
                        'lyDo'      => trim($item['lyDo'] ?? ''),
                        'tinhTrang' => $tinhTrang
                    ];
                }
            }
        } elseif (!empty($data['maTB']) && is_array($data['maTB'])) {
            // Form table mới
            foreach ($data['maTB'] as $maTB) {
                $maTB = (int)$maTB;
                if ($maTB <= 0) continue;

                $soLuong   = (int)($data['soLuong'][$maTB] ?? 0);
                $lyDo      = trim($data['lyDo'][$maTB] ?? '');
                $tinhTrang = trim($data['tinhTrang'][$maTB] ?? '');

                if (empty($tinhTrang)) {
                    continue; // Bỏ qua nếu thiếu tình trạng
                }

                if ($soLuong > 0 && !empty($lyDo)) {
                    $chiTietList[] = [
                        'maTB'      => $maTB,
                        'soLuong'   => $soLuong,
                        'lyDo'      => $lyDo,
                        'tinhTrang' => $tinhTrang
                    ];
                }
            }
        }

        if (empty($chiTietList)) {
            return ['success' => false, 'message' => 'Vui lòng chọn ít nhất một thiết bị hợp lệ với số lượng, lý do và tình trạng.'];
        }

        try {
            $maTL = $this->model->taoKeHoachThanhLy($keHoach, $chiTietList, $maND);

            $result = [
                'success' => true,
                'message' => "Lập kế hoạch thanh lý thành công! Mã kế hoạch: TL" . sprintf("%04d", $maTL),
                'data'    => ['maTL' => $maTL]
            ];

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                $_SESSION['success'] = $result['message'];
                header("Location: ?tab=ke-hoach-thanh-ly");
                exit;
            }

            return $result;
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi khi lưu kế hoạch: ' . $e->getMessage()];
        }
    }

    public function capNhatTrangThai($maTL, $trangThai, $ghiChu = null): array
    {
        $permissionCheck = checkPermission([4, 5]);
        if (!$permissionCheck['success']) return $permissionCheck;

        if (empty($maTL) || !is_numeric($maTL)) {
            return ['success' => false, 'message' => 'Mã kế hoạch không hợp lệ'];
        }

        if (!in_array($trangThai, ['Đã duyệt', 'Từ chối'])) {
            return ['success' => false, 'message' => 'Trạng thái không hợp lệ'];
        }

        $maNDDuyet = $_SESSION['maND'] ?? null;
        if (!$maNDDuyet) {
            return ['success' => false, 'message' => 'Không xác định được người duyệt'];
        }

        try {
            $result = $this->model->capNhatTrangThai($maTL, $trangThai, $maNDDuyet, $ghiChu);

            $message = $trangThai === 'Đã duyệt'
                ? 'Đã duyệt kế hoạch thanh lý thành công'
                : 'Đã từ chối kế hoạch thanh lý';

            return ['success' => true, 'message' => $message];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()];
        }
    }

    public function xoaKeHoach($maTL): array
    {
        $permissionCheck = checkPermission([3, 5]);
        if (!$permissionCheck['success']) return $permissionCheck;

        if (empty($maTL) || !is_numeric($maTL)) {
            return ['success' => false, 'message' => 'Mã kế hoạch không hợp lệ'];
        }

        $maND = $_SESSION['maND'] ?? null;
        if (!$maND) {
            return ['success' => false, 'message' => 'Không xác định được người dùng'];
        }

        $isAdmin = ($_SESSION['maVT'] ?? 0) == 1; // Admin có quyền xóa bất kỳ
        try {
            $result = $this->model->xoaKeHoach($maTL, $maND, $isAdmin);

            return ['success' => true, 'message' => 'Đã xóa kế hoạch thanh lý thành công'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function testConnection(): array
    {
        try {
            $result = $this->model->testConnection();
            return ['success' => true, 'message' => $result];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi kết nối: ' . $e->getMessage()];
        }
    }
}
?>