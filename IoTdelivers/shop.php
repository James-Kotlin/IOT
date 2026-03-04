<?php
/**
 * IoTdelivers - Shop (Product Listing)
 */

require_once 'config.php';
require_once 'includes/db_functions.php';

$page_title = 'Shop - Browse Our Products';

// Get filter parameters
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);

// Get categories for filter
$categories = getAllCategories();

// Pagination
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

// Get total product count
$total_products = getProductCount($category_id);
$total_pages = ceil($total_products / $limit);

// Get products
$products = getProducts($category_id, $limit, $offset);

// Get current category info
$current_category = null;
if ($category_id) {
    $current_category = getCategoryById($category_id);
}
?>

<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section style="background: linear-gradient(135deg, <?php echo PRIMARY_COLOR; ?> 0%, #5a0a9d 100%);" class="py-4 text-white">
    <div class="container">
        <h1 class="mb-0">
            <i class="fas fa-shopping-bag"></i>
            <?php echo $current_category ? htmlspecialchars($current_category['name']) : 'All Products'; ?>
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mt-2 mb-0 bg-transparent">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                <li class="breadcrumb-item active text-light">Shop</li>
            </ol>
        </nav>
    </div>
</section>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar - Categories Filter -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Categories</h5>
                </div>
                <div class="card-body">
                    <a href="shop.php" class="d-block mb-2 text-decoration-none <?php echo !$category_id ? 'fw-bold' : 'text-muted'; ?>" style="color: <?php echo !$category_id ? PRIMARY_COLOR : '#6c757d'; ?>;">
                        All Products
                    </a>
                    <?php foreach ($categories as $cat): ?>
                    <a href="shop.php?category=<?php echo $cat['id']; ?>" class="d-block mb-2 text-decoration-none <?php echo $category_id == $cat['id'] ? 'fw-bold' : 'text-muted'; ?>" style="color: <?php echo $category_id == $cat['id'] ? PRIMARY_COLOR : '#6c757d'; ?>;">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Price Range Filter -->
            <div class="card shadow-sm mt-4">
                <div class="card-header" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                    <h5 class="mb-0"><i class="fas fa-money-bill"></i> Price Range</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Filter by price range coming soon</p>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Results Info -->
            <div class="mb-4">
                <p class="text-muted">
                    Showing <strong><?php echo count($products); ?></strong> of <strong><?php echo $total_products; ?></strong> products
                </p>
            </div>

            <?php if (!empty($products)): ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card product-card h-100 shadow-sm">
                        <!-- Product Image -->
                        <div class="product-image" style="background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); height: 250px; display: flex; align-items: center; justify-content: center; position: relative;">
                            <i class="fas fa-box fa-5x text-muted"></i>
                            <?php if ($product['is_featured']): ?>
                            <span class="badge bg-danger position-absolute" style="top: 10px; right: 10px;">Featured</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars(substr($product['description'], 0, 80) . '...'); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="h5 mb-0" style="color: <?php echo PRIMARY_COLOR; ?>;">
                                    <?php echo formatCurrency($product['price']); ?>
                                </span>
                                <span class="badge <?php echo $product['stock_quantity'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $product['stock_quantity'] > 0 ? 'In Stock' : 'Out'; ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm w-100" style="background-color: <?php echo PRIMARY_COLOR; ?>; color: white;">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="shop.php<?php echo $category_id ? '?category=' . $category_id . '&' : '?'; ?>page=1">First</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="shop.php<?php echo $category_id ? '?category=' . $category_id . '&' : '?'; ?>page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i >= $page - 2 && $i <= $page + 2): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="shop.php<?php echo $category_id ? '?category=' . $category_id . '&' : '?'; ?>page=<?php echo $i; ?>" style="<?php echo $i === $page ? 'background-color: ' . PRIMARY_COLOR . '; border-color: ' . PRIMARY_COLOR . ';' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="shop.php<?php echo $category_id ? '?category=' . $category_id . '&' : '?'; ?>page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="shop.php<?php echo $category_id ? '?category=' . $category_id . '&' : '?'; ?>page=<?php echo $total_pages; ?>">Last</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>

            <?php else: ?>
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle"></i> No products found in this category. Try browsing other categories.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
