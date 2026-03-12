// Admin JavaScript

// Xác nhận xóa
document.addEventListener('DOMContentLoaded', function() {
    // Hiển thị hình ảnh khi chọn file
    const imageInputs = document.querySelectorAll('.image-input');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.getElementById(this.dataset.preview);
            if (preview) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            }
        });
    });
    
    // Xác nhận xóa
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const name = this.dataset.name || 'mục này';
            const url = this.getAttribute('href');
            
            Swal.fire({
                title: 'Xác nhận xóa',
                text: `Bạn có chắc chắn muốn xóa ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
    
    // Hiển thị thông báo thành công hoặc lỗi
    const showAlert = (type, message) => {
        Swal.fire({
            icon: type,
            title: type === 'success' ? 'Thành công' : 'Lỗi',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    };
    
    // Kiểm tra và hiển thị thông báo từ URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        showAlert('success', urlParams.get('success'));
    }
    if (urlParams.has('error')) {
        showAlert('error', urlParams.get('error'));
    }
}); 