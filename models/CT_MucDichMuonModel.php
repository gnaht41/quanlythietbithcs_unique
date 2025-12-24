<?php
// models/CT_MucDichMuonModel.php
require_once 'QT_Database.php';

class CT_MucDichMuonModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy danh sách mục đích thường dùng (hardcoded cho đơn giản)
    public function layDanhSachMucDich($limit = 10)
    {
        $data = [
            ['maMucDich' => 1, 'tenMucDich' => 'Dạy học', 'moTa' => 'Sử dụng cho hoạt động giảng dạy', 'soLanSuDung' => 0],
            ['maMucDich' => 2, 'tenMucDich' => 'Họp phụ huynh', 'moTa' => 'Sử dụng trong các buổi họp phụ huynh', 'soLanSuDung' => 0],
            ['maMucDich' => 3, 'tenMucDich' => 'Hội nghị', 'moTa' => 'Sử dụng trong các hội nghị, họp', 'soLanSuDung' => 0],
            ['maMucDich' => 4, 'tenMucDich' => 'Thi cử', 'moTa' => 'Sử dụng trong các kỳ thi, kiểm tra', 'soLanSuDung' => 0],
            ['maMucDich' => 5, 'tenMucDich' => 'Hoạt động ngoại khóa', 'moTa' => 'Sử dụng cho hoạt động ngoại khóa', 'soLanSuDung' => 0]
        ];

        return ['success' => true, 'data' => array_slice($data, 0, $limit)];
    }

    // Tìm kiếm mục đích theo từ khóa
    public function timKiemMucDich($keyword, $limit = 5)
    {
        $allData = $this->layDanhSachMucDich(100)['data'];
        $filtered = array_filter($allData, function ($item) use ($keyword) {
            return stripos($item['tenMucDich'], $keyword) !== false ||
                stripos($item['moTa'], $keyword) !== false;
        });

        return ['success' => true, 'data' => array_slice($filtered, 0, $limit)];
    }

    // Các phương thức khác có thể bỏ qua vì không cần thiết cho hệ thống hiện tại
    public function themMucDichMoi($tenMucDich, $moTa = '')
    {
        return ['success' => false, 'message' => 'Chức năng này chưa được hỗ trợ'];
    }

    public function capNhatSoLanSuDung($tenMucDich)
    {
        // Không cần cập nhật vì dùng danh sách cố định
        return true;
    }

    public function layThongKeMucDich($limit = 5)
    {
        return ['success' => true, 'data' => []];
    }
}
