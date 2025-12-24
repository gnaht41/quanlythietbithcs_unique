<?php
// File: controllers/ThietBiController.php
require_once __DIR__ . '/../models/VV_QLThietBi.php'; // Đảm bảo đường dẫn đúng đến model

class ThietBiController
{
    private $thietBiModel;

    public function __construct()
    {
        $this->thietBiModel = new ThietBiModel();
    }

    // Xử lý thêm thiết bị mới
    public function addDevice($data)
    {
        $tenTB           = trim($data['tenTB'] ?? '');
        $maMH            = $data['maMH'] ?? null;
        $donVi           = trim($data['donVi'] ?? '');
        $lop             = trim($data['lop'] ?? '');
        $soLuongTong     = (int)($data['soLuongTong'] ?? 0);
        $soLuongKhaDung  = (int)($data['soLuongKhaDung'] ?? $soLuongTong); // Mặc định khả dụng = tổng
        $tinhTrang       = trim($data['tinhTrang'] ?? 'Tốt');
        $isHidden        = isset($data['isHidden']) ? true : false;

        // Validation
        if (empty($tenTB)) {
            return ['success' => false, 'message' => 'Tên thiết bị là bắt buộc.'];
        }
        if (empty($maMH)) {
            return ['success' => false, 'message' => 'Vui lòng chọn môn học.'];
        }
        if ($soLuongTong < 0 || $soLuongKhaDung < 0 || $soLuongKhaDung > $soLuongTong) {
            return ['success' => false, 'message' => 'Số lượng không hợp lệ.'];
        }

        $result = $this->thietBiModel->addDevice(
            $tenTB,
            $maMH,
            $donVi,
            $lop,
            $soLuongTong,
            $soLuongKhaDung,
            $tinhTrang,
            $isHidden
        );

        return $result; // Model đã trả về ['success', 'message', ...]
    }

    // Xử lý cập nhật thiết bị
    public function updateDevice($data)
    {
        $maTB            = (int)($data['maTB'] ?? 0);
        $tenTB           = trim($data['tenTB'] ?? '');
        $maMH            = $data['maMH'] ?? null;
        $donVi           = trim($data['donVi'] ?? '');
        $lop             = trim($data['lop'] ?? '');
        $soLuongTong     = (int)($data['soLuongTong'] ?? 0);
        $soLuongKhaDung  = (int)($data['soLuongKhaDung'] ?? 0);
        $tinhTrang       = trim($data['tinhTrang'] ?? 'Tốt');
        $isHidden        = isset($data['isHidden']) ? true : false;

        // Validation
        if (empty($maTB)) {
            return ['success' => false, 'message' => 'Mã thiết bị không hợp lệ.'];
        }
        if (empty($tenTB)) {
            return ['success' => false, 'message' => 'Tên thiết bị là bắt buộc.'];
        }
        if (empty($maMH)) {
            return ['success' => false, 'message' => 'Vui lòng chọn môn học.'];
        }
        if ($soLuongTong < 0 || $soLuongKhaDung < 0 || $soLuongKhaDung > $soLuongTong) {
            return ['success' => false, 'message' => 'Số lượng khả dụng không được lớn hơn tổng số lượng.'];
        }

        $result = $this->thietBiModel->updateDevice(
            $maTB,
            $tenTB,
            $maMH,
            $donVi,
            $lop,
            $soLuongTong,
            $soLuongKhaDung,
            $tinhTrang,
            $isHidden
        );

        return $result;
    }

    // Xử lý xóa thiết bị
    public function deleteDevice($maTB)
    {
        $maTB = (int)$maTB;

        if ($maTB <= 0) {
            return ['success' => false, 'message' => 'Mã thiết bị không hợp lệ.'];
        }

        return $this->thietBiModel->deleteDevice($maTB);
        // Model sẽ kiểm tra ràng buộc và trả về thông báo chi tiết
    }

    // Lấy thông tin một thiết bị
    public function getDevice($maTB)
    {
        $maTB = (int)$maTB;

        if ($maTB <= 0) {
            return ['success' => false, 'message' => 'Mã thiết bị không hợp lệ.'];
        }

        $device = $this->thietBiModel->getDeviceById($maTB);

        if ($device) {
            return ['success' => true, 'data' => $device];
        }

        return ['success' => false, 'message' => 'Không tìm thấy thiết bị.'];
    }

    // Tìm kiếm và lọc danh sách thiết bị
    public function searchDevices($filters = [])
    {
        // Nếu không truyền filters, tự động lấy từ GET/POST
        if (empty($filters)) {
            $filters = [
                'tenTB'      => trim($_REQUEST['tenTB'] ?? ''),
                'maMH'       => $_REQUEST['maMH'] ?? '',
                'tinhTrang'  => trim($_REQUEST['tinhTrang'] ?? ''),
                'lop'        => trim($_REQUEST['lop'] ?? '')
            ];
        }

        // Loại bỏ các filter rỗng (trừ khi cần giữ giá trị 0 hoặc false)
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        return $this->thietBiModel->searchDevices($filters);
    }

    // Lấy danh sách môn học
    public function getMonHocList()
    {
        $monHocs = $this->thietBiModel->getMonHoc();
        return ['success' => true, 'data' => $monHocs];
    }

    // Lấy danh sách tình trạng (theo ENUM trong CSDL)
    public function getTinhTrangList()
    {
        $tinhTrangs = $this->thietBiModel->getTinhTrangList();
        return ['success' => true, 'data' => $tinhTrangs];
    }

    // Phương thức tổng quát xử lý action
    public function handleAction($action, $data = [])
    {
        switch ($action) {
            case 'add':
                return $this->addDevice($data);
            case 'update':
                return $this->updateDevice($data);
            case 'delete':
                $maTB = $data['maTB'] ?? ($_GET['maTB'] ?? '');
                return $this->deleteDevice($maTB);
            case 'getDevice':
                $maTB = $data['maTB'] ?? 0;
                return $this->getDevice($maTB);
            case 'search':
                $filters = $data['filters'] ?? [];
                return $this->searchDevices($filters);
            case 'getMonHoc':
                return $this->getMonHocList();
            case 'getTinhTrang':
                return $this->getTinhTrangList();
            default:
                return ['success' => false, 'message' => 'Action không hợp lệ.'];
        }
    }

    // Phương thức cũ để tương thích (nếu view cũ vẫn gọi theo cách này)
    public function handleRequest($action)
    {
        $data = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
        return $this->handleAction($action, $data);
    }
}