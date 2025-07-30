<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Đăng nhập | Quán Ăn</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
      margin: 0;
    }

    .bg-cover {
      background-image: url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1950&q=80');
      background-size: cover;
      background-position: center;
      position: relative;
      height: 100%;
    }

    .bg-overlay {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(0,0,0,0.5); 
    }

    .login-wrapper {
      position: relative;
      z-index: 2;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      background: #ffffff;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.4);
      max-width: 400px;
      width: 100%;
      text-align: center;
      animation: fadeIn 1s ease;
    }

    .login-box h2 {
      margin-bottom: 30px;
      color: #333;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="bg-cover">
    <div class="bg-overlay"></div>
    <div class="login-wrapper">
      <div class="login-box">
        <h2>🍽️ Chào mừng đến với Quán Ăn</h2>
        <form action="index.php?action=login" method="POST">
          <div class="mb-3 text-start">
            <label for="username" class="form-label">Tên đăng nhập</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Nhập username" required>
          </div>
          <div class="mb-4 text-start">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
