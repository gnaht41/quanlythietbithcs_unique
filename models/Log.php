<?php
// models/Log.php
class LogModel {
    private mysqli $conn;

    public function __construct(mysqli $db) {
        $this->conn = $db;
    }

    /** Lấy danh sách log có lọc */
    public function getLogs(array $filter = []): array {
        // filter: q, from, to, role, action
        $q      = trim($filter['q'] ?? '');
        $from   = trim($filter['from'] ?? '');
        $to     = trim($filter['to'] ?? '');
        $role   = trim($filter['role'] ?? '');
        $action = trim($filter['action'] ?? '');

        $sql = "
            SELECT
                l.maLog,
                l.thoiGian,
                nd.email,
                nd.maVT,
                vt.tenVT,
                l.hanhDong,
                l.doiTuong
            FROM bangghilog l
            LEFT JOIN nguoidung nd ON nd.maND = l.nguoiDung
            LEFT JOIN vaitro vt ON vt.maVT = nd.maVT
            WHERE 1=1
        ";

        $params = [];
        $types  = '';

        if ($q !== '') {
            $sql .= " AND (nd.email LIKE ? OR l.hanhDong LIKE ? OR l.doiTuong LIKE ?) ";
            $kw = "%{$q}%";
            $params[] = $kw; $params[] = $kw; $params[] = $kw;
            $types .= 'sss';
        }

        if ($from !== '') {
            $sql .= " AND l.thoiGian >= ? ";
            $params[] = $from . " 00:00:00";
            $types .= 's';
        }

        if ($to !== '') {
            $sql .= " AND l.thoiGian <= ? ";
            $params[] = $to . " 23:59:59";
            $types .= 's';
        }

        if ($role !== '') {
            $sql .= " AND nd.maVT = ? ";
            $params[] = (int)$role;
            $types .= 'i';
        }

        if ($action !== '') {
            $sql .= " AND l.hanhDong = ? ";
            $params[] = $action;
            $types .= 's';
        }

        $sql .= " ORDER BY l.thoiGian DESC, l.maLog DESC ";

        if (empty($params)) {
            $rs = $this->conn->query($sql);
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $rs = $stmt->get_result();
            $stmt->close();
        }

        $rows = [];
        if ($rs) {
            while ($r = $rs->fetch_assoc()) {
                $r['ketQua'] = 'Thành công'; // bảng không có cột ketQua -> mặc định hiển thị
                $rows[] = $r;
            }
        }
        return $rows;
    }
}
