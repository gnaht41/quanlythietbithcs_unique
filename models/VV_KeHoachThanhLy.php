<?php
require_once 'QT_Database.php';

class KeHoachThanhLyModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

public function getThietBiCanThanhLy()
{
    $query = "
        SELECT 
            tb.maTB,
            tb.tenTB,
            tb.donVi,
            tb.soLuongTong,
            tb.soLuongKhaDung,
            tb.tinhTrang,
            tb.lop,
            mh.tenMonHoc,
            CASE
                -- Trạng thái đang sửa / hư nhẹ / hư nặng + tổng = khả dụng
                WHEN tb.tinhTrang IN ('Đang sửa', 'Hư nhẹ', 'Hư nặng') AND tb.soLuongTong = tb.soLuongKhaDung
                    THEN tb.soLuongKhaDung
                -- Trạng thái đang mượn / hư nhẹ / hư nặng + khả dụng < tổng
                WHEN tb.tinhTrang IN ('Đang sửa', 'Hư nhẹ', 'Hư nặng') AND tb.soLuongKhaDung < tb.soLuongTong
                    THEN tb.soLuongTong - tb.soLuongKhaDung 
                ELSE 0
            END AS soLuongCanThanhLy
        FROM thietbi tb
        LEFT JOIN monhoc mh ON tb.maMH = mh.maMH
        WHERE tb.isHidden = 0
        HAVING soLuongCanThanhLy > 0
        ORDER BY tb.tenTB
    ";

    $result = $this->db->query($query);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}


    // Kiểm tra số lượng khả dụng của một thiết bị
    public function kiemTraSoLuong($maTB, $soLuongYeuCau)
    {
        $sql = "SELECT soLuongKhaDung, tenTB, donVi FROM thietbi WHERE maTB = ? AND isHidden = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $maTB);
        $stmt->execute();
        $result = $stmt->get_result();
        $tb = $result->fetch_assoc();

        if (!$tb) {
            return ['success' => false, 'message' => 'Thiết bị không tồn tại hoặc đã bị ẩn'];
        }

        if ($tb['soLuongKhaDung'] < $soLuongYeuCau) {
            return [
                'success' => false,
                'message' => "Chỉ còn {$tb['soLuongKhaDung']} {$tb['donVi']} {$tb['tenTB']} khả dụng"
            ];
        }

        return ['success' => true, 'khaDung' => $tb['soLuongKhaDung']];
    }

    // Lấy danh sách kế hoạch thanh lý (mới nhất lên đầu, ưu tiên Chờ duyệt)
    public function getDanhSachKeHoach()
    {
        $sql = "
            SELECT 
                kh.maTL, 
                kh.ngayLap, 
                kh.trangThai, 
                kh.phuongPhapThanhLy, 
                kh.ghiChu,
                kh.ngayDuyet,
                kh.ngayHoanThanh,
                nd.hoTen AS nguoiLap,
                nd2.hoTen AS nguoiDuyet,
                (SELECT COUNT(*) FROM chitietthanhly ct WHERE ct.maTL = kh.maTL) AS soThietBi
            FROM kehoachthanhly kh
            LEFT JOIN nguoidung nd ON kh.nguoiLap = nd.maND
            LEFT JOIN nguoidung nd2 ON kh.nguoiDuyet = nd2.maND
            ORDER BY 
                (kh.trangThai = 'Chờ duyệt') DESC,
                kh.maTL DESC
        ";
        
        $result = $this->db->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Lấy chi tiết một kế hoạch thanh lý
    public function getChiTietKeHoach($maTL)
    {
        $sql = "SELECT kh.*, nd.hoTen AS nguoiLap, nd2.hoTen AS nguoiDuyet, nd2.maND AS maNDDuyet
                FROM kehoachthanhly kh
                LEFT JOIN nguoidung nd ON kh.nguoiLap = nd.maND
                LEFT JOIN nguoidung nd2 ON kh.nguoiDuyet = nd2.maND
                WHERE kh.maTL = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $maTL);
        $stmt->execute();
        $result = $stmt->get_result();
        $keHoach = $result->fetch_assoc();

        if (!$keHoach) return null;

        // Chi tiết thiết bị
        $sqlCT = "SELECT ct.*, tb.tenTB, tb.donVi, mh.tenMonHoc
                  FROM chitietthanhly ct
                  JOIN thietbi tb ON ct.maTB = tb.maTB
                  LEFT JOIN monhoc mh ON tb.maMH = mh.maMH
                  WHERE ct.maTL = ?";
        $stmtCT = $this->db->prepare($sqlCT);
        $stmtCT->bind_param("i", $maTL);
        $stmtCT->execute();
        $resultCT = $stmtCT->get_result();
        $keHoach['chiTiet'] = $resultCT->fetch_all(MYSQLI_ASSOC);

        return $keHoach;
    }

    // Tạo kế hoạch thanh lý mới
    public function taoKeHoachThanhLy($data, $chiTietList, $maND)
    {
        $this->db->autocommit(false);

        try {
            $ngayLap = $data['ngayLap'] ?? date('Y-m-d');
            $phuongPhap = trim($data['phuongPhapThanhLy'] ?? '');
            if (empty($phuongPhap)) {
                $phuongPhap = 'Bán phế liệu'; // mặc định theo ENUM
            }
            $ghiChu = $data['ghiChu'] ?? null;

            $sql = "INSERT INTO kehoachthanhly 
                    (ngayLap, trangThai, nguoiLap, phuongPhapThanhLy, ghiChu) 
                    VALUES (?, 'Chờ duyệt', ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("siss", $ngayLap, $maND, $phuongPhap, $ghiChu);
            $stmt->execute();
            $maTL = $this->db->insert_id;

            foreach ($chiTietList as $ct) {
                $maTB = $ct['maTB'];
                $soLuong = $ct['soLuong'];
                $lyDo = $ct['lyDo'];
                $tinhTrang = trim($ct['tinhTrang'] ?? '');

                if (empty($tinhTrang)) {
                    throw new Exception("Thiếu tình trạng cho thiết bị ID: $maTB");
                }

                // Kiểm tra số lượng
                $check = $this->kiemTraSoLuong($maTB, $soLuong);
                if (!$check['success']) {
                    throw new Exception($check['message']);
                }

                $sqlCT = "INSERT INTO chitietthanhly 
                          (maTL, maTB, soLuong, lyDo, tinhTrang, trangThaiThucHien) 
                          VALUES (?, ?, ?, ?, ?, 'Chờ xử lý')";
                $stmtCT = $this->db->prepare($sqlCT);
                $stmtCT->bind_param("iiiss", $maTL, $maTB, $soLuong, $lyDo, $tinhTrang);
                $stmtCT->execute();
            }

            // Ghi log
            $sqlLog = "INSERT INTO bangghilog (maND, hanhDong, doiTuong, doiTuongId) 
                       VALUES (?, 'INSERT', 'KeHoachThanhLy', ?)";
            $stmtLog = $this->db->prepare($sqlLog);
            $stmtLog->bind_param("ii", $maND, $maTL);
            $stmtLog->execute();

            $this->db->commit();
            return $maTL;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        } finally {
            $this->db->autocommit(true);
        }
    }

    // Cập nhật trạng thái (Duyệt / Từ chối)
    public function capNhatTrangThai($maTL, $trangThai, $maNDDuyet, $ghiChu = null)
    {
        $this->db->autocommit(false);
        try {
            $ngayDuyet = date('Y-m-d');

            $sql = "UPDATE kehoachthanhly 
                    SET trangThai = ?, nguoiDuyet = ?, ngayDuyet = ?, 
                        ghiChu = COALESCE(?, ghiChu)
                    WHERE maTL = ? AND trangThai = 'Chờ duyệt'";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sissi", $trangThai, $maNDDuyet, $ngayDuyet, $ghiChu, $maTL);
            $stmt->execute();

            if ($stmt->affected_rows == 0) {
                throw new Exception("Không thể cập nhật: Kế hoạch không tồn tại hoặc đã được xử lý");
            }

            if ($trangThai === 'Đã duyệt') {
                $sqlCT = "SELECT maTB, soLuong FROM chitietthanhly 
                          WHERE maTL = ? AND trangThaiThucHien = 'Chờ xử lý'";
                $stmtCT = $this->db->prepare($sqlCT);
                $stmtCT->bind_param("i", $maTL);
                $stmtCT->execute();
                $resultCT = $stmtCT->get_result();

                while ($row = $resultCT->fetch_assoc()) {
                    $maTB = $row['maTB'];
                    $soLuong = $row['soLuong'];

                    $sqlUpdate = "UPDATE thietbi 
                                  SET soLuongTong = soLuongTong - ?, 
                                      soLuongKhaDung = soLuongKhaDung - ? 
                                  WHERE maTB = ? AND soLuongKhaDung >= ?";
                    $stmtUpdate = $this->db->prepare($sqlUpdate);
                    $stmtUpdate->bind_param("iiii", $soLuong, $soLuong, $maTB, $soLuong);
                    $stmtUpdate->execute();

                    if ($stmtUpdate->affected_rows == 0) {
                        throw new Exception("Không đủ số lượng khả dụng cho thiết bị ID: $maTB");
                    }

                    $sqlDone = "UPDATE chitietthanhly 
                                SET trangThaiThucHien = 'Đã xử lý', ngayXuLy = ? 
                                WHERE maTL = ? AND maTB = ?";
                    $stmtDone = $this->db->prepare($sqlDone);
                    $stmtDone->bind_param("sii", $ngayDuyet, $maTL, $maTB);
                    $stmtDone->execute();
                }

                $sqlHoanThanh = "UPDATE kehoachthanhly SET ngayHoanThanh = ? WHERE maTL = ?";
                $stmtHT = $this->db->prepare($sqlHoanThanh);
                $stmtHT->bind_param("si", $ngayDuyet, $maTL);
                $stmtHT->execute();
            }

            // Ghi log
            $hanhDong = $trangThai === 'Đã duyệt' ? 'DUYET' : 'TỪ CHỐI';
            $sqlLog = "INSERT INTO bangghilog (maND, hanhDong, doiTuong, doiTuongId) 
                       VALUES (?, ?, 'KeHoachThanhLy', ?)";
            $stmtLog = $this->db->prepare($sqlLog);
            $stmtLog->bind_param("isi", $maNDDuyet, $hanhDong, $maTL);
            $stmtLog->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        } finally {
            $this->db->autocommit(true);
        }
    }

    // Xóa kế hoạch (chỉ khi Chờ duyệt)
    public function xoaKeHoach($maTL, $maND, $isAdmin = false)
    {
        $this->db->autocommit(false);
        try {
            $sqlCheck = "SELECT trangThai, nguoiLap FROM kehoachthanhly WHERE maTL = ?";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->bind_param("i", $maTL);
            $stmtCheck->execute();
            $res = $stmtCheck->get_result()->fetch_assoc();

            if (!$res || $res['trangThai'] !== 'Chờ duyệt') {
                throw new Exception("Chỉ có thể xóa kế hoạch đang ở trạng thái Chờ duyệt");
            }

            if ($res['nguoiLap'] != $maND && !$isAdmin) {
                throw new Exception("Bạn không có quyền xóa kế hoạch này");
            }

            $sqlDelCT = "DELETE FROM chitietthanhly WHERE maTL = ?";
            $stmtDelCT = $this->db->prepare($sqlDelCT);
            $stmtDelCT->bind_param("i", $maTL);
            $stmtDelCT->execute();

            $sqlDel = "DELETE FROM kehoachthanhly WHERE maTL = ?";
            $stmtDel = $this->db->prepare($sqlDel);
            $stmtDel->bind_param("i", $maTL);
            $stmtDel->execute();

            $sqlLog = "INSERT INTO bangghilog (maND, hanhDong, doiTuong, doiTuongId) 
                       VALUES (?, 'DELETE', 'KeHoachThanhLy', ?)";
            $stmtLog = $this->db->prepare($sqlLog);
            $stmtLog->bind_param("ii", $maND, $maTL);
            $stmtLog->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        } finally {
            $this->db->autocommit(true);
        }
    }

    // Debug kết nối
    public function testConnection()
    {
        $query = "SELECT COUNT(*) as total FROM thietbi";
        $result = $this->db->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            return "Kết nối thành công. Tổng thiết bị: " . $row['total'];
        }
        return "Lỗi kết nối: " . $this->db->error;
    }
}
?>