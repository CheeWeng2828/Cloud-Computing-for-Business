<?php
include '../base.php';
auth();
$id = req('id');
setOrderID($id);

if (is_post()) {
    $status = req('orderStatus');
    $stm = $_db->prepare("UPDATE payment SET status = ? WHERE order_id = ?");
    $stm->execute([$status, $id]);
    temp('info', 'Update Status Successful');
}

$stm = $_db->prepare('SELECT * FROM `order` WHERE id = ?');
$stm->execute([$id]);
$o = $stm->fetch();
if (!$o) redirect('history.php');

$stm = $_db->prepare('
SELECT i.*,p.name,p.photo,y.datetime,y.status
FROM item AS i LEFT JOIN payment AS y ON i.order_id = y.order_id
JOIN product AS p ON i.product_id = p.id
WHERE i.order_id = ?
');
$stm->execute([$id]);
$arr = $stm->fetchAll();

$stm = $_db->prepare('SELECT status FROM payment WHERE order_id = ?');
$stm->execute([$id]);
$status = $stm->fetch();

$stm = $_db->prepare('SELECT u.address FROM `order` AS o JOIN user AS u ON o.user_id = u.id WHERE o.id = ?');
$stm->execute([$id]);
$ad = $stm->fetch();

$_title = "Order Detail";
include '../head.php';
?>

<style>
.order-detail-wrapper {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.order-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.order-header h1 {
    margin: 0 0 1rem 0;
    font-size: 1.75rem;
    font-weight: 600;
}

.order-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.order-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.order-meta-item svg {
    width: 20px;
    height: 20px;
    opacity: 0.9;
}

.order-total {
    text-align: right;
}

.order-total-label {
    font-size: 0.875rem;
    opacity: 0.9;
    margin-bottom: 0.25rem;
}

.order-total-amount {
    font-size: 2rem;
    font-weight: 700;
}

.status-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.status-card h2 {
    font-size: 1.25rem;
    margin: 0 0 1rem 0;
    color: #1a202c;
}

.status-badge {
    display: inline-block;
    padding: 0.5rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-processing { background: #dbeafe; color: #1e40af; }
.status-ready { background: #e0e7ff; color: #4338ca; }
.status-delivered { background: #d1fae5; color: #065f46; }
.status-cancel { background: #fee2e2; color: #991b1b; }

.status-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: border-color 0.2s;
}

.status-select:focus {
    outline: none;
    border-color: #667eea;
}

.address-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.address-card h2 {
    font-size: 1.25rem;
    margin: 0 0 1rem 0;
    color: #1a202c;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.address-card svg {
    width: 24px;
    height: 24px;
    color: #667eea;
}

.address-text {
    color: #4b5563;
    line-height: 1.6;
}

.items-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.items-card h2 {
    font-size: 1.25rem;
    margin: 0 0 1.5rem 0;
    color: #1a202c;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
}

.items-table thead {
    background: #f9fafb;
}

.items-table th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.items-table td {
    padding: 1rem;
    border-top: 1px solid #f3f4f6;
    color: #1f2937;
}

.items-table tbody tr:hover {
    background: #f9fafb;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.product-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
    background: #f3f4f6;
}

.product-name {
    font-weight: 500;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn {
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

@media (max-width: 768px) {
    .order-meta {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .order-total {
        text-align: left;
        width: 100%;
    }
    
    .items-table {
        font-size: 0.875rem;
    }
    
    .items-table th,
    .items-table td {
        padding: 0.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="order-detail-wrapper">
    <!-- Order Header -->
    <div class="order-header">
        <h1>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 0.5rem;">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            Order #<?= $o->id ?>
        </h1>
        <div class="order-meta">
            <div>
                <div class="order-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span><?= $o->datetime ?></span>
                </div>
                <div class="order-meta-item" style="margin-top: 0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="8" y1="6" x2="21" y2="6"></line>
                        <line x1="8" y1="12" x2="21" y2="12"></line>
                        <line x1="8" y1="18" x2="21" y2="18"></line>
                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                    </svg>
                    <span><?= $o->count ?> Items</span>
                </div>
            </div>
            <div class="order-total">
                <div class="order-total-label">Total Amount</div>
                <div class="order-total-amount">RM <?= number_format($o->total, 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Order Status -->
    <div class="status-card">
        <h2>Order Status</h2>
        <?php foreach ($status as $s): ?>
            <?php if ($_user?->role == "Admin" && $s != "CANCEL" && $s != "DELIVERED"): ?>
                <form method="post">
                    <?= html_select('orderStatus', $_orderStatus, $s, 'class="status-select"') ?>
                </form>
            <?php else: ?>
                <span class="status-badge status-<?= strtolower(str_replace(' ', '', $s)) ?>">
                    <?= $s ?>
                </span>
            <?php endif ?>
        <?php endforeach ?>
    </div>

    <!-- Delivery Address -->
    <div class="address-card">
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
            Delivery Address
        </h2>
        <div class="address-text"><?= $ad->address ?></div>
    </div>

    <!-- Order Items -->
    <div class="items-card">
        <h2>Order Items</h2>
        <div style="overflow-x: auto;">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($arr as $h): ?>
                        <tr>
                            <td>
                                <div class="product-info">
                                    <?php if (!empty($h->photo)): ?>
                                        <img src="/product_img/<?= $h->photo ?>" alt="<?= $h->name ?>" class="product-image">
                                    <?php else: ?>
                                        <div class="product-image"></div>
                                    <?php endif ?>
                                    <div>
                                        <div class="product-name"><?= $h->name ?></div>
                                        <div style="font-size: 0.875rem; color: #6b7280;">ID: <?= $h->product_id ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>RM <?= number_format($h->price, 2) ?></td>
                            <td><?= $h->unit ?></td>
                            <td style="text-align: right; font-weight: 600;">RM <?= number_format($h->subtotal, 2) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <?php if ($_user?->role == "Member" && $h->status == "PENDING"): ?>
            <button class="btn btn-primary" data-get="paymentCard.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                    <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
                Make Payment
            </button>
        <?php endif ?>
        
        <?php if ($_user?->role == "Member" && $h->status != "PENDING"): ?>
            <button class="btn btn-success" data-get="reorder.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                </svg>
                Reorder
            </button>
        <?php endif ?>
        
        <?php if ($h->status != "CANCEL" && $h->status != "READY TO SHIP" && $h->status != "DELIVERED"): ?>
            <button class="btn btn-danger" data-get="cancelOrder.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
                Cancel Order
            </button>
        <?php endif ?>
    </div>
</div>

<script>
    $('select').on('change', e => e.target.form.submit());
</script>

<?php include '../foot.php'; ?>