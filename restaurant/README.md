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
1. **Tạo database MySQL** với tên `restaurant_db`
2. **Copy file cấu hình**:
   ```bash
   cp config/database.example.php config/database.php
   ```
3. **Chỉnh sửa thông tin database** trong `config/database.php`:
   - `host`: Địa chỉ MySQL server (thường là `localhost`)
   - `db_name`: Tên database (đã tạo ở bước 1)
   - `username`: Tên đăng nhập MySQL
   - `password`: Mật khẩu MySQL

### Bước 3: Cấu hình web server

#### **Cách 1: Sử dụng XAMPP/Laragon**
1. **Copy thư mục** vào `htdocs` (XAMPP) hoặc `www` (Laragon)
2. **Truy cập**: `http://localhost/restaurant`

#### **Cách 2: Cấu hình Virtual Host (Apache)**
1. **Tạo file** `restaurant.conf` trong Apache:
   ```apache
   <VirtualHost *:80>
       ServerName restaurant.local
       DocumentRoot "C:/xampp/htdocs/restaurant"
       <Directory "C:/xampp/htdocs/restaurant">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```
2. **Thêm vào hosts**: `127.0.0.1 restaurant.local`
3. **Restart Apache**
4. **Truy cập**: `http://restaurant.local`

### Bước 4: Tạo tài khoản admin
- Đăng nhập với tài khoản mặc định
- Tạo nhân viên qua Admin Panel

## 👤 Tài khoản mặc định

### Admin
- **Username**: `admin`
- **Password**: `123`

### Staff (sau khi tạo)
- **Username**: Số điện thoại
- **Password**: Số điện thoại

## 📝 Ghi chú

- Mật khẩu nhân viên có thể reset qua Admin Panel
- File upload được lưu trong thư mục `uploads/`
- Backup database thường xuyên

## 🤝 Đóng góp

1. Fork repository
2. Tạo feature branch
3. Commit changes
4. Push to branch
5. Tạo Pull Request

## 📄 License

MIT License

---

**Tác giả**: [Tên của bạn]
**Phiên bản**: 1.0.0
**Ngày tạo**: 2024 