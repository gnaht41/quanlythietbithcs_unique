<h2>Lịch sử mượn</h2>
<div class="nav">
    <button class="btn btn-primary" onclick="changeMonth(-1)">‹ Tháng trước</button>
    <span id="month-year"></span>
    <button class="btn btn-primary" onclick="changeMonth(1)">Tháng sau ›</button>
</div>

<div class="calendar" id="calendar"></div>

<div id="modal" class="modal">
    <div class="modal-content">
        <h3>Chi tiết phiếu mượn</h3>
        <div id="detail"></div>
        <button class="btn btn-primary" onclick="closeModal()">Đóng</button>
    </div>
</div>

<script>
const API = '../controllers/CT_PhieuMuonAPI.php';
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let allPhieu = [];

// Load dữ liệu từ API phiếu mượn
function loadData() {
    console.log('🔄 Loading data from API...');

    // Thêm timestamp để tránh cache
    const timestamp = new Date().getTime();

    fetch(API + '?action=list&_=' + timestamp)
        .then(r => r.json())
        .then(data => {
            console.log('📊 API Response:', data);

            if (data.success && data.data) {
                allPhieu = data.data;
                console.log('✅ Loaded phiếu:', allPhieu);

                // Debug đặc biệt cho ngày 17
                const phieu17 = allPhieu.filter(p => p.ngaymuon && p.ngaymuon.includes('2024-12-17'));
                console.log('🎯 Phiếu for 2024-12-17:', phieu17);

                if (phieu17.length > 0) {
                    console.log('✅ FOUND phiếu for ngày 17:', phieu17[0].ma);
                } else {
                    console.log('❌ NO phiếu found for ngày 17');
                }

                renderCalendar();
            } else {
                console.error('❌ API Error:', data);
            }
        })
        .catch(error => {
            console.error('💥 Network Error:', error);
        });
}

// Render lịch
function renderCalendar() {
    const calendar = document.getElementById('calendar');
    const months = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9',
        'Tháng 10', 'Tháng 11', 'Tháng 12'
    ];
    const days = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];

    document.getElementById('month-year').textContent = `${months[currentMonth]} ${currentYear}`;
    calendar.innerHTML = '';

    // Headers
    days.forEach(d => {
        const h = document.createElement('div');
        h.className = 'day-header';
        h.textContent = d;
        calendar.appendChild(h);
    });

    // Tính ngày đầu tháng
    const firstDay = new Date(currentYear, currentMonth, 1);
    const startDay = firstDay.getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const prevMonthDays = new Date(currentYear, currentMonth, 0).getDate();

    // Render 42 ô (6 tuần)
    for (let i = 0; i < 42; i++) {
        const cell = document.createElement('div');
        cell.className = 'day';

        let day, month, year;

        if (i < startDay) {
            // Tháng trước
            day = prevMonthDays - startDay + i + 1;
            month = currentMonth - 1;
            year = currentYear;
            if (month < 0) {
                month = 11;
                year--;
            }
        } else if (i < startDay + daysInMonth) {
            // Tháng hiện tại
            day = i - startDay + 1;
            month = currentMonth;
            year = currentYear;
        } else {
            // Tháng sau
            day = i - startDay - daysInMonth + 1;
            month = currentMonth + 1;
            year = currentYear;
            if (month > 11) {
                month = 0;
                year++;
            }
        }

        // Tạo date string YYYY-MM-DD
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        // Day number
        const dayNum = document.createElement('div');
        dayNum.className = 'day-number';
        dayNum.textContent = day;
        cell.appendChild(dayNum);

        // Tìm phiếu cho ngày này
        allPhieu.forEach(phieu => {
            const ngayMuon = phieu.ngaymuon ? phieu.ngaymuon.split(' ')[0] : '';
            const ngayTra = phieu.ngaytra ? phieu.ngaytra.split(' ')[0] : '';

            // Debug cho ngày 17
            if (dateStr === '2024-12-17') {
                console.log(`🔍 Checking 2024-12-17 - Phieu ${phieu.ma}: muon=${ngayMuon}, tra=${ngayTra}`);
            }

            // Phiếu mượn (màu vàng)
            if (ngayMuon === dateStr) {
                const p = document.createElement('div');
                p.className = 'phieu';
                p.textContent = phieu.ma;
                p.title = 'Mượn: ' + phieu.thietbi;
                p.onclick = () => showDetail(phieu);
                cell.appendChild(p);

                if (dateStr === '2024-12-17') {
                    console.log(`✅ ADDED phieu ${phieu.ma} to 2024-12-17 (MUON)`);
                }
            }

            // Phiếu trả (màu xanh)
            if (ngayTra === dateStr) {
                const p = document.createElement('div');
                p.className = 'phieu tra';
                p.textContent = phieu.ma;
                p.title = 'Trả: ' + phieu.thietbi;
                p.onclick = () => showDetail(phieu);
                cell.appendChild(p);

                if (dateStr === '2024-12-17') {
                    console.log(`✅ ADDED phieu ${phieu.ma} to 2024-12-17 (TRA)`);
                }
            }
        });

        calendar.appendChild(cell);
    }
}

function changeMonth(dir) {
    currentMonth += dir;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    renderCalendar();
}

function showDetail(phieu) {
    const formatDate = (d) => {
        if (!d) return '';
        const date = new Date(d);
        return `${date.getDate()}/${String(date.getMonth() + 1).padStart(2, '0')}/${date.getFullYear()}`;
    };

    document.getElementById('detail').innerHTML = `
        <p><b>Mã:</b> ${phieu.ma}</p>
        <p><b>Thiết bị:</b> ${phieu.thietbi}</p>
        <p><b>Mục đích:</b> ${phieu.mucdich}</p>
        <p><b>Ngày mượn:</b> ${formatDate(phieu.ngaymuon)}</p>
        <p><b>Ngày trả:</b> ${formatDate(phieu.ngaytra)}</p>
        <p><b>Trạng thái:</b> ${phieu.trangthai}</p>
    `;
    document.getElementById('modal').style.display = 'block';
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', loadData);
window.onclick = e => e.target.classList.contains('modal') && closeModal();
</script>