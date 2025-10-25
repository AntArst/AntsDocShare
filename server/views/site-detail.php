<?php
$title = 'Site Details - PDGP';
$currentPage = 'dashboard';
$siteId = $_GET['site_id'] ?? null;

ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-building"></i> 
        <span id="siteName">Loading...</span>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/" class="btn btn-sm btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
        <button id="uploadBtn" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="bi bi-upload"></i> Upload Products
        </button>
    </div>
</div>

<div id="alert-container"></div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Site Slug</h6>
                <p class="card-text" id="siteSlug">-</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Status</h6>
                <p class="card-text" id="siteStatus">-</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Total Products</h6>
                <p class="card-text" id="productCount">-</p>
            </div>
        </div>
    </div>
</div>

<h3 class="mb-3">Product Carousel</h3>

<!-- Product Selector and Carousel -->
<div id="carouselContainer" class="mb-5">
    <div class="text-center">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<h3 class="mb-3">All Products</h3>

<div id="productsContainer">
    <div class="text-center">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="csvFile" class="form-label">CSV File</label>
                        <input type="file" class="form-control" id="csvFile" name="csv" accept=".csv" required>
                        <div class="form-text">
                            <a href="/api/template/csv" download>Download CSV template</a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="images" class="form-label">Product Images</label>
                        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
                        <div class="form-text">Select multiple image files for your products</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitUpload">
                    <i class="bi bi-upload"></i> Upload
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

ob_start();
?>

<script>
const token = localStorage.getItem('pdgp_token');
const siteId = <?php echo json_encode($siteId); ?>;

if (!token) {
    window.location.href = '/login';
}

async function loadSiteDetails() {
    try {
        const response = await fetch(`/api/sites/${siteId}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        if (response.status === 401) {
            localStorage.removeItem('pdgp_token');
            window.location.href = '/login';
            return;
        }
        
        const data = await response.json();
        
        if (data.success) {
            displaySiteInfo(data.site);
            displayProducts(data.products);
        } else {
            showAlert('danger', 'Failed to load site details');
        }
    } catch (error) {
        showAlert('danger', 'Connection error: ' + error.message);
    }
}

function displaySiteInfo(site) {
    document.getElementById('siteName').textContent = site.name;
    document.getElementById('siteSlug').textContent = site.slug;
    document.getElementById('siteStatus').innerHTML = site.active 
        ? '<span class="badge bg-success">Active</span>' 
        : '<span class="badge bg-danger">Inactive</span>';
}

let allProducts = [];

function displayProducts(products) {
    allProducts = products;
    const container = document.getElementById('productsContainer');
    const carouselContainer = document.getElementById('carouselContainer');
    document.getElementById('productCount').textContent = products.length;
    
    if (products.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info">
                No products yet. Upload a CSV file to add products.
            </div>
        `;
        carouselContainer.innerHTML = `
            <div class="alert alert-info">
                No products to display in carousel.
            </div>
        `;
        return;
    }
    
    // Display Carousel
    displayCarousel(products);
    
    // Display Table
    container.innerHTML = `
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Assets</th>
                    </tr>
                </thead>
                <tbody>
                    ${products.map(product => `
                        <tr>
                            <td>${product.item_name}</td>
                            <td>${product.image_name || '-'}</td>
                            <td>${product.price ? '$' + product.price : '-'}</td>
                            <td>${product.description || '-'}</td>
                            <td><code>${JSON.stringify(product.assets || {})}</code></td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

function displayCarousel(products) {
    const carouselContainer = document.getElementById('carouselContainer');
    
    carouselContainer.innerHTML = `
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="productSelector" class="form-label">Select Product:</label>
                <select id="productSelector" class="form-select">
                    ${products.map((product, index) => `
                        <option value="${index}">${product.item_name}</option>
                    `).join('')}
                </select>
            </div>
        </div>
        
        <div class="card bg-dark">
            <div class="card-body">
                <div id="productCarousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner">
                        ${products.map((product, index) => `
                            <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        ${product.image_name ? `
                                            <img src="/storage/assets/${siteId}/images/${product.image_name}" 
                                                 class="d-block w-100 rounded" 
                                                 alt="${product.item_name}"
                                                 style="max-height: 500px; object-fit: contain; background-color: #0d1117;"
                                                 onerror="this.src='data:image/svg+xml,${encodeURIComponent(getPlaceholderSVG(product.item_name))}'">
                                        ` : `
                                            <img src="data:image/svg+xml,${encodeURIComponent(getPlaceholderSVG(product.item_name))}" 
                                                 class="d-block w-100 rounded" 
                                                 alt="${product.item_name}"
                                                 style="max-height: 500px; object-fit: contain; background-color: #0d1117;">
                                        `}
                                    </div>
                                    <div class="col-md-6">
                                        <h2 class="text-primary mb-3">${product.item_name}</h2>
                                        ${product.price ? `
                                            <h3 class="text-success mb-3">$${parseFloat(product.price).toFixed(2)}</h3>
                                        ` : ''}
                                        ${product.description ? `
                                            <p class="text-light mb-3">${product.description}</p>
                                        ` : ''}
                                        ${product.assets && Object.keys(product.assets).length > 0 ? `
                                            <div class="mb-3">
                                                <h5 class="text-info">Product Details:</h5>
                                                <ul class="list-unstyled">
                                                    ${Object.entries(product.assets).map(([key, value]) => `
                                                        <li><strong>${key}:</strong> ${value}</li>
                                                    `).join('')}
                                                </ul>
                                            </div>
                                        ` : ''}
                                        <div class="text-muted small">
                                            Product ${index + 1} of ${products.length}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev" style="width: 5%;">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next" style="width: 5%;">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Set up dropdown event listener
    document.getElementById('productSelector').addEventListener('change', (e) => {
        const index = parseInt(e.target.value);
        const carousel = new bootstrap.Carousel(document.getElementById('productCarousel'));
        carousel.to(index);
    });
    
    // Update dropdown when carousel slides
    const carouselElement = document.getElementById('productCarousel');
    carouselElement.addEventListener('slid.bs.carousel', (e) => {
        const activeIndex = Array.from(e.target.querySelectorAll('.carousel-item')).indexOf(e.relatedTarget);
        document.getElementById('productSelector').value = activeIndex;
    });
}

function getPlaceholderSVG(productName) {
    return `<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">
        <rect width="800" height="600" fill="%23161b22"/>
        <text x="400" y="280" font-family="Arial, sans-serif" font-size="24" fill="%2358a6ff" text-anchor="middle">No Image Available</text>
        <text x="400" y="320" font-family="Arial, sans-serif" font-size="18" fill="%238b949e" text-anchor="middle">${productName}</text>
        <circle cx="400" cy="200" r="60" fill="%2330363d" stroke="%2358a6ff" stroke-width="2"/>
        <path d="M 380 200 L 400 220 L 420 180" stroke="%2358a6ff" stroke-width="3" fill="none" stroke-linecap="round"/>
    </svg>`;
}

document.getElementById('submitUpload').addEventListener('click', async () => {
    const formData = new FormData(document.getElementById('uploadForm'));
    formData.append('site_id', siteId);
    
    try {
        const response = await fetch('/api/upload', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('success', `Successfully uploaded ${data.products_count} products!`);
            bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
            loadSiteDetails();
        } else {
            showAlert('danger', data.error || 'Upload failed');
        }
    } catch (error) {
        showAlert('danger', 'Connection error: ' + error.message);
    }
});

function showAlert(type, message) {
    const alertContainer = document.getElementById('alert-container');
    alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
}

// Load site details on page load
loadSiteDetails();
</script>

<?php
$scripts = ob_get_clean();

require_once __DIR__ . '/layout.php';
?>

