// JavaScript đơn giản cho giáo viên
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo các tab
    initTabs();
});

// Quản lý tabs
function initTabs() {
    const tabs = document.querySelectorAll('.tab-button');
    const contents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const target = this.dataset.tab;
            
            // Ẩn tất cả content
            contents.forEach(content => {
                content.style.display = 'none';
            });
            
            // Xóa active class
            tabs.forEach(t => t.classList.remove('active'));
            
            // Hiện content được chọn
            const targetContent = document.getElementById(target);
            if (targetContent) {
                targetContent.style.display = 'block';
            }
            
            // Thêm active class
            this.classList.add('active');
        });
    });
}

// Close modal when clicking outside
window.onclick = function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
}