<?php
include_once 'models/Table.php';
include_once 'models/Menu.php';
include_once 'models/Area.php';

$tableModel = new Table($db);
$menuModel = new Menu($db);
$areaModel = new Area($db);

$preselected_table_id = $_GET['table_id'] ?? null;
$preselected_table_info = null;
if ($preselected_table_id) {
    $stmt = $db->prepare("
        SELECT t.id, t.name, a.name AS area_name 
        FROM tables t 
        JOIN areas a ON t.area_id = a.id 
        WHERE t.id = ?
    ");
    $stmt->execute([$preselected_table_id]);
    $preselected_table_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Debug
    error_log('preselected_table_id: ' . $preselected_table_id);
    error_log('preselected_table_info: ' . print_r($preselected_table_info, true));
}

$areas = $areaModel->getAll();
$menuItems = $menuModel->getAll();

$tableArr = [];
if (!$preselected_table_info) {
    $tables2 = $tableModel->getAll();
    while ($tb = $tables2->fetch(PDO::FETCH_ASSOC)) {
        if ($tb['status'] == 'available') {
            $tableArr[] = $tb;
        }
    }
}
$menuItemsArr = [];
$menuItemsForJs = [];
$menuItems->execute(); 
while ($item = $menuItems->fetch(PDO::FETCH_ASSOC)) {
    $menuItemsArr[$item['id']] = $item['price'];
    $menuItemsForJs[$item['id']] = $item;
}

// Debug: Kiểm tra menuItemsForJs
error_log('menuItemsForJs count: ' . count($menuItemsForJs));
if (empty($menuItemsForJs)) {
    error_log('WARNING: menuItemsForJs is empty!');
}
?>

<style>
.menu-card {
  border: 2px solid #dee2e6;
  border-radius: 18px;
  box-shadow: 0 2px 8px rgba(52,58,64,0.06);
  transition: box-shadow 0.2s, border-color 0.2s;
  background: #fff;
  position: relative;
  min-height: 240px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  overflow: hidden;
  cursor: pointer;
}
.menu-card.selected {
  border-color: #0d6efd;
  box-shadow: 0 4px 16px rgba(13,110,253,0.12);
}
.menu-card .menu-img-wrap {
  position: relative;
  width: 100%;
  height: 120px;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
}
.menu-card .menu-img {
  border-radius: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  background: #f8f9fa;
}
.menu-card .menu-price-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: rgba(0,0,0,0.65);
  color: #fff;
  font-weight: bold;
  font-size: 1.05rem;
  border-radius: 8px;
  padding: 4px 12px;
  z-index: 11;
  box-shadow: 0 1px 4px rgba(0,0,0,0.10);
}
.menu-card .menu-title {
  font-size: 1.13rem;
  font-weight: 600;
  color: #343a40;
  margin: 12px 0 2px 0;
  text-align: center;
  line-height: 1.2;
}
.menu-card .menu-desc {
  color: #6c757d;
  font-size: 0.97rem;
  min-height: 36px;
  text-align: center;
  margin-bottom: 8px;
}
.menu-card .menu-tickbox {
  position: absolute;
  top: 12px;
  left: 12px;
  width: 24px;
  height: 24px;
  border: 2px solid #0d6efd;
  border-radius: 6px;
  background: #fff;
  z-index: 12;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  color: #0d6efd;
  font-weight: bold;
  transition: background 0.15s;
}
.menu-card.selected .menu-tickbox::after {
  content: '\2713';
  font-size: 1.1rem;
  color: #198754;
}
.menu-card .menu-checkbox { display: none; }
@media (max-width: 767px) {
  .menu-card { min-height: 120px; }
  .menu-card .menu-img-wrap { height: 60px; }
}
.order-list-table td, .order-list-table th { vertical-align: middle; }
.sticky-footer {
  position: sticky;
  bottom: 0;
  left: 0;
  z-index: 10;
  background: #f8f9fa;
  border-top: 1px solid #dee2e6;
  padding: 16px 0 0 0;
}
</style>

<div class="container-fluid">
  <h2 class="mb-4 text-primary">
    <i class="fas fa-plus-circle"></i> Tạo Đơn Hàng Mới
  </h2>
  <?php 
  if (!empty($order_message)) {
      echo $order_message;
  } elseif (isset($_GET['error'])) {
      echo '<div class="alert alert-danger">Vui lòng chọn ít nhất một món!</div>';
  }
  ?>

  <form method="POST" action="index.php?action=save_order_staff" id="orderForm">
    <!-- Card chọn bàn/khu vực -->
    <div class="row mb-3">
      <div class="col-md-8 mx-auto">
        <div class="card shadow-sm mb-0">
          <div class="card-body py-3">
            <div class="row">
              <?php if ($preselected_table_info): ?>
                <div class="col-md-6 mb-2">
                  <label class="form-label fw-bold">Khu Vực</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($preselected_table_info['area_name']); ?>" readonly>
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label fw-bold">Bàn</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($preselected_table_info['name']); ?>" readonly>
                </div>
                <input type="hidden" name="table_id" value="<?php echo $preselected_table_info['id']; ?>">
              <?php else: ?>
                <div class="col-md-6 mb-2">
                  <label for="area_id" class="form-label">Chọn Khu vực</label>
                  <select name="area_id" id="area_id" class="form-select" required>
                    <option value="">-- Chọn khu vực --</option>
                    <?php while ($a = $areas->fetch(PDO::FETCH_ASSOC)) : ?>
                      <option value="<?php echo $a['id']; ?>">Khu vực: <?php echo htmlspecialchars($a['name']); ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="table_id" class="form-label">Chọn Bàn (Chỉ hiện bàn trống)</label>
                  <select name="table_id" id="table_id" class="form-select" required>
                    <option value="">-- Chọn bàn --</option>
                    <?php foreach ($tableArr as $t) : ?>
                      <option value="<?php echo $t['id']; ?>" data-area="<?php echo $t['area_id']; ?>">
                        Bàn <?php echo htmlspecialchars($t['name']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Cột trái: Danh sách món đã chọn -->
      <div class="col-md-5 mb-4">
        <div class="card h-100 shadow-sm d-flex flex-column">
          <div class="card-header bg-primary text-white">
            <i class="fas fa-list"></i> Danh sách món đã chọn
          </div>
          <div class="card-body p-2 flex-grow-1">
            <div id="orderListEmpty" class="text-center text-muted my-4">Chưa có món nào được chọn</div>
            <div class="table-responsive">
              <table class="table table-sm order-list-table mb-0" id="orderListTable" style="display:none;">
                <thead>
                  <tr>
                    <th>Món</th>
                    <th class="text-center">SL</th>
                    <th class="text-end">Đơn giá</th>
                    <th class="text-end">Thành tiền</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr>
                    <td colspan="3" class="text-end fw-bold">Tổng cộng:</td>
                    <td class="text-end fw-bold" id="orderTotal">0</td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
            </div>
            <div class="mt-3">
              <label for="note" class="form-label">Ghi chú cho bếp/khách (nếu có)</label>
              <textarea name="note" id="note" class="form-control" rows="2" placeholder="Ví dụ: Không hành, ít cay, mang về..."></textarea>
            </div>
          </div>
          <div class="card-footer sticky-footer text-end">
            <button type="button" class="btn btn-secondary btn-staff me-2" onclick="resetOrderList();">
              <i class="fas fa-times"></i> Hủy
            </button>
            <button type="submit" class="btn btn-success btn-staff">
              <i class="fas fa-save"></i> Tạo Đơn
            </button>
          </div>
        </div>
      </div>
      <!-- Cột phải: Thực đơn -->
      <div class="col-md-7 mb-4">
        <div class="card h-100 shadow-sm">
          <div class="card-header bg-success text-white">
            <i class="fas fa-utensils"></i> Thực đơn
          </div>
          <div class="card-body">
            <div class="row" id="menuGrid">
              <?php foreach ($menuItemsForJs as $item): ?>
                <div class="col-md-4 col-sm-6 mb-3">
                  <div class="menu-card p-2 h-100" data-id="<?php echo $item['id']; ?>">
                    <div class="menu-img-wrap">
                      <?php if (!empty($item['image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" class="menu-img" alt="<?php echo htmlspecialchars($item['name']); ?>">
                      <?php endif; ?>
                      <span class="menu-price-badge"><?php echo number_format($item['price']); ?> VND</span>
                    </div>
                    <div class="menu-title"><?php echo htmlspecialchars($item['name']); ?></div>
                    <div class="menu-desc mb-1"><?php echo htmlspecialchars($item['description']); ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <input type="hidden" name="quantities_json" id="quantities_json">
  </form>
</div>

<script>
// Khởi tạo biến toàn cục
let menuItems = {};
let orderList = {};

// Load menu items
try {
    menuItems = <?php echo json_encode($menuItemsForJs); ?>;
    console.log('Menu items loaded successfully:', Object.keys(menuItems).length);
} catch (error) {
    console.error('Error loading menu items:', error);
    menuItems = {};
}

console.log('Order list initialized');

function renderOrderList() {
  const tbody = document.querySelector('#orderListTable tbody');
  tbody.innerHTML = '';
  let total = 0;
  let hasItem = false;
  
  for (const id in orderList) {
    const item = orderList[id];
    if (item && item.quantity > 0) {
      hasItem = true;
      const itemTotal = item.quantity * item.price;
      total += itemTotal;
      tbody.innerHTML += `
        <tr>
          <td><strong>${item.name}</strong></td>
          <td class="text-center">
            <button type="button" class="btn btn-sm btn-light border px-2 py-0" onclick="changeQty('${id}', -1)">-</button>
            <span class="mx-2">${item.quantity}</span>
            <button type="button" class="btn btn-sm btn-light border px-2 py-0" onclick="changeQty('${id}', 1)">+</button>
          </td>
          <td class="text-end">${item.price.toLocaleString()} VND</td>
          <td class="text-end">${itemTotal.toLocaleString()} VND</td>
          <td class="text-end">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeItem('${id}')"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
      `;
    }
  }
  
  document.getElementById('orderTotal').textContent = total.toLocaleString() + ' VND';
  document.getElementById('orderListTable').style.display = hasItem ? '' : 'none';
  document.getElementById('orderListEmpty').style.display = hasItem ? 'none' : '';
}

function addToOrder(id) {
  console.log('addToOrder called with id:', id);
  console.log('menuItems[id]:', menuItems[id]);
  
  if (!menuItems[id]) {
    console.log('menuItems[id] is falsy, returning');
    return;
  }
  
  if (!orderList[id]) {
    orderList[id] = {
      id: id,
      name: menuItems[id].name,
      price: parseInt(menuItems[id].price),
      quantity: 1
    };
    console.log('Created new order item:', orderList[id]);
  } else {
    orderList[id].quantity++;
    console.log('Increased quantity for existing item:', orderList[id]);
  }
  
  console.log('orderList after update:', orderList);
  renderOrderList();
}

function changeQty(id, delta) {
  if (!orderList[id]) return;
  orderList[id].quantity += delta;
  if (orderList[id].quantity <= 0) {
    delete orderList[id];
  }
  renderOrderList();
}

function removeItem(id) {
  if (orderList[id]) {
    delete orderList[id];
    renderOrderList();
  }
}

function resetOrderList() {
  orderList = {};
  renderOrderList();
}

document.querySelectorAll('.menu-card').forEach(function(card) {
  card.addEventListener('click', function() {
    console.log('Menu card clicked!');
    const id = card.getAttribute('data-id');
    console.log('Card ID:', id);
    console.log('menuItems[id]:', menuItems[id]);
    addToOrder(id);
    card.classList.add('selected');
    setTimeout(() => card.classList.remove('selected'), 200);
  });
});

console.log('Event listeners attached to', document.querySelectorAll('.menu-card').length, 'menu cards');

// Lọc bàn theo khu vực
const areaSelect = document.getElementById('area_id');
if (areaSelect) {
  areaSelect.addEventListener('change', function() {
    const selectedAreaId = this.value;
    const tableSelect = document.getElementById('table_id');
    const tableOptions = tableSelect.querySelectorAll('option');
    
    // Ẩn tất cả options trừ option đầu tiên
    tableOptions.forEach((option, index) => {
      if (index === 0) return; 
      
      if (selectedAreaId === '' || option.getAttribute('data-area') === selectedAreaId) {
        option.style.display = '';
      } else {
        option.style.display = 'none';
      }
    });
    
    tableSelect.value = '';
  });
}

const orderForm = document.getElementById('orderForm');
orderForm.addEventListener('submit', function(e) {
  let hasItems = false;
  const quantities = {};
  
  for (const id in orderList) {
    if (orderList[id] && orderList[id].quantity > 0) {
      hasItems = true;
      quantities[id] = orderList[id].quantity;
    }
  }
  
  if (!hasItems) {
    alert('Vui lòng chọn ít nhất một món!');
    e.preventDefault();
    return false;
  }
  
  document.getElementById('quantities_json').value = JSON.stringify(quantities);
});

renderOrderList();
</script>
