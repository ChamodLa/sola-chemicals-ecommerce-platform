<?php
session_start();
$is_logged_in = isset($_COOKIE['user_id']);
$cart_count = 0; // Initialize cart count

if ($is_logged_in) {
    require_once $_SERVER['DOCUMENT_ROOT']."/context/connect.php";
    $user_id = $_COOKIE['user_id'];
    
    // Get user data
    $stmt = $conn->prepare("SELECT first_name, last_name, image FROM user WHERE id = ?");
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get cart count
    $sql = "SELECT COUNT(ci.id) AS item_count
            FROM cart_item ci
            JOIN cart c ON ci.cart_id = c.id
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row) {
        $cart_count = $row['item_count'];
    }
}
?>
<style>
.dropdown-menu {
    min-width: 260px;
    max-width: 90vw;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid rgba(0,0,0,.08);
    box-shadow: 0 4px 16px rgba(0,0,0,.1);
}

@media (max-width: 991.98px) {
    .dropdown-menu {
        position: fixed !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        top: auto !important;
        transform: none !important;
        width: 100% !important;
        margin: 0;
        border-radius: 1rem 1rem 0 0;
        border-bottom: 0;
        max-width: 100%;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .auth-btn {
        padding: 0.75rem 1rem;
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
}
.auth-icon {
    font-size: 2rem;
    color: #0078d4;
    margin-bottom: 0.5rem;
}
.auth-btn {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    border-radius: 4px;
    width: 100%;
    margin-bottom: 0.5rem;
    transition: all 0.2s ease;
}
.auth-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,.1);
}
.profile-image {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

@media (max-width: 991.98px) {
    .profile-image {
        width: 28px;
        height: 28px;
    }
}
</style>
<nav class="navbar navbar-expand-lg navbar-light bg-white py-4 fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex justify-content-between align-items-center order-lg-0" href="./index.php">
            <img src="/public/Main-Logo.svg" alt="site icon">
        </a>

        <!-- Mobile Button Group -->
        <div class="order-lg-2 nav-btns d-lg-none">
            <button type="button" class="btn position-relative" title="Search" data-bs-toggle="modal" data-bs-target="#searchModal">
                <i class="fa fa-search"></i>
            </button> 
            <?php if ($is_logged_in): ?>
                <button type="button" class="btn position-relative" title="Cart" data-bs-toggle="modal" data-bs-target="#cartModal">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge bg-primary"><?php echo $cart_count; ?></span>
                </button>
            <?php endif; ?>
            <!-- Mobile Profile Dropdown -->
            <div class="dropdown d-inline-block">
                <button class="btn p-0" type="button" id="mobileProfileDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                    <?php if ($is_logged_in): ?>
                        <img src="/uploads/user/<?= $user_data['image'] ?: 'null.png' ?>" alt="Profile" class="profile-image">
                    <?php else: ?>
                        <i class="fa fa-user"></i>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="mobileProfileDropdown">
                    <?php if ($is_logged_in): ?>
                        <div class="text-center">
                            <i class="bi bi-person-circle auth-icon"></i>
                            <h6 class="mb-2">Hello, <?= htmlspecialchars($user_data['first_name']) ?></h6>
                            <p class="text-muted small mb-3">Welcome back!</p>
                            <div class="d-grid gap-2">
                                <a href="./pages/profile.php" class="btn btn-outline-primary auth-btn">
                                    <i class="bi bi-person-circle me-2"></i>My Profile
                                </a>
                                <a href="./pages/OrderHistory.php" class="btn btn-outline-primary auth-btn">
                                    <i class="bi bi-clock-history me-2"></i>Order History
                                </a>
                                <a href="./pages/logout.php" class="btn btn-light auth-btn">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <i class="bi bi-person-circle auth-icon"></i>
                            <h6 class="mb-2">Welcome Guest</h6>
                            <p class="text-muted small mb-3">Please sign in to continue</p>
                            <div class="d-grid gap-2">
                                <a href="./pages/signin.php" class="btn btn-primary auth-btn">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                </a>
                                <a href="./pages/signup.php" class="btn btn-outline-primary auth-btn">
                                    <i class="bi bi-person-plus me-2"></i>Sign Up
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" title="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-lg-1" id="navMenu">
            <!-- Left-Aligned Navigation Links -->
            <ul class="navbar-nav me-auto text-start">
                <li class="nav-item px-2 py-2">
                    <a class="nav-link text-uppercase text-dark" href="./index.php?page=home">Home</a>
                </li>
                <li class="nav-item px-2 py-2">
                    <a class="nav-link text-uppercase text-dark" href="./index.php?page=about">About</a>
                </li>
                <li class="nav-item px-2 py-2">
                    <a class="nav-link text-uppercase text-dark" href="./index.php?page=product">Product</a>
                </li>
            </ul>

            <!-- Search Box for Larger Screens -->
            <form class="d-none d-lg-flex me-lg-3" role="search" id="searchForm" onsubmit="handleSearch(event)">
                <input class="form-control me-2" type="search" name="q" placeholder="Search products..." aria-label="Search">
                <button class="btn btn-outline-primary" type="submit" title="Search">
                    <i class="fa fa-search"></i>
                </button>
            </form>

            <!-- Desktop Button Group -->
            <div class="order-lg-2 nav-btns d-none d-lg-flex align-items-center">
                <?php if ($is_logged_in): ?>
                    <?php
                    require_once $_SERVER['DOCUMENT_ROOT']."/context/connect.php";
                    global $conn;
                    // Get cart items count for logged in user
                    $cart_count = 0;
                    $user_id = $_COOKIE['user_id'];
                    $sql = "SELECT COUNT(ci.id) AS item_count
                            FROM cart_item ci
                            JOIN cart c ON ci.cart_id = c.id
                            WHERE c.user_id = ?;";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(1, $user_id, PDO::PARAM_INT); 
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if($row) {
                        $cart_count = $row['item_count'];
                    }
                    ?>
                    <button type="button" class="btn position-relative" title="History" onclick="window.location.href='./pages/OrderHistory.php'">
                        <i class="fas fa-history fa-lg"></i>
                    </button>
                    <button type="button" class="btn position-relative" title="Cart" data-bs-toggle="modal" data-bs-target="#cartModal">
                        <i class="fa fa-shopping-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge bg-primary">
                            <?php echo $cart_count; ?>
                        </span>
                    </button>
                <?php endif; ?>
                <!-- Desktop Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="profileDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                        <?php if ($is_logged_in): ?>
                            <img src="/uploads/user/<?= $user_data['image'] ?: 'null.png' ?>" alt="Profile" class="profile-image">
                        <?php else: ?>
                            <i class="fa fa-user fa-lg"></i>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <?php if ($is_logged_in): ?>
                            <div class="text-center">
                                <h6 class="mb-2">Hello, <?= htmlspecialchars($user_data['first_name']) ?></h6>
                                <p class="text-muted small mb-3">Welcome back!</p>
                                <div class="d-grid gap-2">
                                    <a href="./pages/profile.php" class="btn btn-outline-primary auth-btn">
                                        <i class="bi bi-person-circle me-2"></i>My Profile
                                    </a>
                                    <a href="./pages/logout.php" class="btn btn-light auth-btn">
                                        <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <i class="bi bi-person-circle auth-icon"></i>
                                <h6 class="mb-2">Welcome Guest</h6>
                                <p class="text-muted small mb-3">Please sign in to continue</p>
                                <a href="./pages/signin.php" class="btn btn-primary auth-btn">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
                                </a>
                                <a href="./pages/signup.php" class="btn btn-light auth-btn">
                                    <i class="bi bi-person-plus me-1"></i>Sign Up
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Search Modal for Mobile -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel">Search Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form onsubmit="handleSearch(event)" id="mobileSearchForm">
                    <div class="input-group">
                        <input type="search" class="form-control" name="q" placeholder="Search products...">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="cartModalLabel">
                    <i class="bi bi-cart3 me-2"></i>Shopping Cart
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <?php
                if ($is_logged_in) {
                    $user_id = $_COOKIE['user_id'];
                    // First get the cart items count
                    $sql = "SELECT COUNT(ci.id) AS item_count 
                           FROM cart_item ci 
                           JOIN cart c ON ci.cart_id = c.id 
                           WHERE c.user_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $count_result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($count_result['item_count'] > 0) {
                        // Get cart items details
                        $sql = "SELECT i.name, i.image, i.UP, ci.quantity, ci.id as cart_item_id
                               FROM cart_item ci 
                               JOIN cart c ON ci.cart_id = c.id
                               JOIN item i ON ci.item_id = i.id
                               WHERE c.user_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $total = 0;

                        foreach ($cart_items as $item) {
                            $subtotal = $item['UP'] * $item['quantity'];
                            $total += $subtotal;
                            ?>
                            <div class="card mb-3 shadow-sm" id="cart-item-<?= $item['cart_item_id'] ?>">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-2 col-sm-3 mb-2 mb-md-0">
                                            <img src="/uploads/product/<?= htmlspecialchars($item['image']) ?>" 
                                                 class="img-fluid rounded" alt="<?= htmlspecialchars($item['name']) ?>"
                                                 style="max-height: 80px; object-fit: cover;">
                                        </div>
                                        <div class="col-md-4 col-sm-9 mb-2 mb-md-0">
                                            <h6 class="mb-1 text-primary"><?= htmlspecialchars($item['name']) ?></h6>
                                            <p class="mb-0 text-muted">Unit Price: Rs. <?= number_format($item['UP'], 2) ?></p>
                                            <p class="mb-0 text-success">Subtotal: Rs. <?= number_format($subtotal, 2) ?></p>
                                        </div>
                                        <div class="col-md-4 col-sm-8 mb-2 mb-md-0">
                                            <div class="input-group">
                                                <button class="btn btn-outline-secondary" type="button"
                                                        onclick="updateCartQuantity(<?= $item['cart_item_id'] ?>, 'decrease')">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <input type="text" class="form-control text-center" value="<?= $item['quantity'] ?>" 
                                                       readonly style="max-width: 60px;">
                                                <button class="btn btn-outline-secondary" type="button"
                                                        onclick="updateCartQuantity(<?= $item['cart_item_id'] ?>, 'increase')">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-4 text-end">
                                            <button class="btn btn-danger btn-sm" 
                                                    onclick="removeFromCart(<?= $item['cart_item_id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="card mt-4 bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">Total Amount</h5>
                                        <small class="text-muted"><?= $count_result['item_count'] ?> items</small>
                                    </div>
                                    <h4 class="mb-0 text-primary">Rs. <?= number_format($total, 2) ?></h4>
                                </div>
                                <hr>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button class="btn btn-secondary me-md-2" data-bs-dismiss="modal">
                                        Continue Shopping
                                    </button>
                                    <button class="btn btn-primary" onclick="window.location.href='/pages/checkout.php'">
                                        <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Your cart is empty</h4>
                            <p class="text-muted mb-4">Browse our products and add some items to your cart!</p>
                            <button class="btn btn-primary" data-bs-dismiss="modal">
                                <i class="bi bi-shop me-2"></i>Continue Shopping
                            </button>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="text-center py-5">
                        <i class="bi bi-person-x text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">Please sign in first</h4>
                        <p class="text-muted mb-4">You need to sign in to view your cart</p>
                        <a href="/pages/signin.php" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                        </a>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileButton = document.getElementById('profileButton');
    const profileMenu = document.getElementById('profileMenu');

    const profileButtonDesktop = document.getElementById('profileButtonDesktop');
    const profileMenuDesktop = document.getElementById('profileMenuDesktop');

    profileButton.addEventListener('click', function() {
        profileMenu.classList.toggle('show');
        adjustDropdownPosition(profileMenu);
    });

    profileButtonDesktop.addEventListener('click', function() {
        profileMenuDesktop.classList.toggle('show');
        adjustDropdownPosition(profileMenuDesktop);
    });

    document.addEventListener('click', function(event) {
        if (!profileButton.contains(event.target) && !profileMenu.contains(event.target)) {
            profileMenu.classList.remove('show');
        }
        if (!profileButtonDesktop.contains(event.target) && !profileMenuDesktop.contains(event.target)) {
            profileMenuDesktop.classList.remove('show');
        }
    });

    window.addEventListener('scroll', function() {
        profileMenu.classList.remove('show');
        profileMenuDesktop.classList.remove('show');
    });

    function adjustDropdownPosition(menu) {
        const rect = menu.getBoundingClientRect();
        if (rect.right > window.innerWidth) {
            menu.style.left = 'auto';
            menu.style.right = '0';
        } else {
            menu.style.left = '';
            menu.style.right = '';
        }
    }
});

function updateCartQuantity(cartItemId, action) {
    fetch('/api/cart/update.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cartItemId: cartItemId,
            action: action
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function removeFromCart(cartItemId) {
    if(confirm('Are you sure you want to remove this item from cart?')) {
        $.ajax({
            url: '/handlers/remove-cart-item.php',
            type: 'POST',
            data: {
                cart_item_id: cartItemId
            },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    // Remove the item element from DOM
                    $(`#cart-item-${cartItemId}`).fadeOut(300, function() {
                        $(this).remove();
                        // Reload page to update cart count and totals
                        location.reload();
                    });
                } else {
                    alert('Error: ' + (response.error || 'Could not remove item'));
                }
            },
            error: function() {
                alert('Error connecting to server');
            }
        });
    }
}

function updateCartCount() {
    $.ajax({
        url: 'handlers/cart-handler.php',
        type: 'POST',
        data: {
            action: 'count'
        },
        success: function(response) {
            if(response.count !== undefined) {
                $('.cart-count').text(response.count);
            }
        }
    });
}

function handleSearch(event) {
    event.preventDefault();
    const form = event.target;
    const searchQuery = form.querySelector('input[name="q"]').value;
    
    // Show loading state
    document.querySelector('.content').innerHTML = '<div class="text-center mt-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Close modal if open
    const searchModal = bootstrap.Modal.getInstance(document.getElementById('searchModal'));
    if (searchModal) {
        searchModal.hide();
    }

    // Fetch search results
    fetch(`/handlers/search-handler.php?q=${encodeURIComponent(searchQuery)}`)
        .then(response => response.text())
        .then(html => {
            document.querySelector('.content').innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.querySelector('.content').innerHTML = '<div class="alert alert-danger">Error loading search results</div>';
        });
}

function loadProductDetails(productId) {
    // Show loading state
    document.querySelector('.content').innerHTML = '<div class="text-center mt-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Fetch product details
    fetch(`/handlers/product-handler.php?id=${productId}`)
        .then(response => response.text())
        .then(html => {
            document.querySelector('.content').innerHTML = html;
            // Update URL without page reload
            history.pushState({}, '', `/pages/ProductOverview.php?id=${productId}`);
        })
        .catch(error => {
            console.error('Error:', error);
            document.querySelector('.content').innerHTML = '<div class="alert alert-danger">Error loading product details</div>';
        });
}
</script>