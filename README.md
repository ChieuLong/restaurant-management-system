# 🍽️ Restaurant Management System
Hệ thống quản lý nhà hàng được xây dựng bằng PHP thuần với kiến trúc MVC.
## ✨ Tính năng chính
### 👨‍💼 **Admin Panel**
- **Dashboard**: Thống kê tổng quan
- **Quản lý món ăn**: Thêm, sửa, xóa món ăn
- **Quản lý bàn ăn**: Quản lý khu vực và bàn
- **Quản lý đơn hàng**: Xem và quản lý đơn hàng
- **Quản lý nhân viên**: Thêm, sửa, xóa nhân viên
- **Báo cáo doanh thu**: Thống kê theo thời gian
### 👨‍💻 **Staff Panel**
- **Dashboard**: Xem bàn trống và đơn hàng
- **Gọi món**: Tạo đơn hàng mới
- **Quản lý đơn hàng**: Xem và cập nhật trạng thái
- **Thanh toán**: Xử lý thanh toán đơn hàng
## 🛠️ Công nghệ sử dụng
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5
- **Icons**: Font Awesome 6

## 📁 Cấu trúc thư mục

```
restaurant/
├── config/          # Cấu hình database
├── controllers/     # Controllers (MVC)
├── models/         # Models (MVC)
├── views/          # Views (MVC)
│   ├── admin/      # Giao diện admin
│   └── staff/      # Giao diện nhân viên
├── uploads/        # File upload
└── includes/       # File include chung
```

## 🚀 Cài đặt

### Yêu cầu hệ thống
- PHP 7.4 trở lên
- MySQL 5.7 trở lên
- Web server (Apache/Nginx)

### Bước 1: Clone repository
```bash
git clone https://github.com/your-username/restaurant-management-system.git
cd restaurant-management-system
```

### Bước 2: Cấu hình database
1. Tạo database MySQL
2. Import file SQL (nếu có)
3. Cập nhật thông tin database trong `config/database.php`

### Bước 3: Cấu hình web server
- Đặt thư mục vào web root
- Cấu hình virtual host (nếu cần)

### Bước 4: Tạo tài khoản admin
- Đăng nhập với tài khoản mặc định
- Tạo nhân viên qua Admin Panel

## 👤 Tài khoản mặc định

### Admin
- **Username**: `0123456789`
- **Password**: `123`

### Staff (sau khi tạo)
- **Username**: Số điện thoại
- **Password**: Số điện thoại



**Tác giả**: [Chiều Long]
**Phiên bản**: 1.0.0
**Ngày tạo**: 2025
