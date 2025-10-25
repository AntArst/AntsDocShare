<?php
$title = 'Dashboard - PDGP';
$currentPage = 'dashboard';

ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/sites/add" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Site
        </a>
    </div>
</div>

<div id="alert-container"></div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-building"></i> Total Sites</h5>
                <h2 id="totalSites">-</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-check-circle"></i> Active Sites</h5>
                <h2 id="activeSites">-</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-box-seam"></i> Total Products</h5>
                <h2 id="totalProducts">-</h2>
            </div>
        </div>
    </div>
</div>

<h3 class="mb-3">Your Sites</h3>

<div id="sitesContainer" class="row">
    <div class="col-12 text-center">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

ob_start();
?>

<script>
const token = localStorage.getItem('pdgp_token');

if (!token) {
    window.location.href = '/login';
}

async function loadSites() {
    try {
        const response = await fetch('/api/sites', {
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
            displaySites(data.sites);
            updateStats(data.sites);
        } else {
            showAlert('danger', 'Failed to load sites');
        }
    } catch (error) {
        showAlert('danger', 'Connection error: ' + error.message);
    }
}

function displaySites(sites) {
    const container = document.getElementById('sitesContainer');
    
    if (sites.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-info">
                    No sites yet. <a href="/sites/add">Create your first site</a>
                </div>
            </div>
        `;
        return;
    }
    
    container.innerHTML = sites.map(site => `
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card ${site.active ? '' : 'border-danger'}">
                <div class="card-body">
                    <h5 class="card-title">
                        ${site.name}
                        ${site.active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}
                    </h5>
                    <p class="card-text">
                        <small class="text-muted">
                            <i class="bi bi-link-45deg"></i> ${site.slug}
                        </small>
                    </p>
                    <p class="card-text">
                        <small class="text-muted">
                            Created: ${new Date(site.created_at).toLocaleDateString()}
                        </small>
                    </p>
                    <a href="/sites/${site.id}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> View Details
                    </a>
                </div>
            </div>
        </div>
    `).join('');
}

function updateStats(sites) {
    document.getElementById('totalSites').textContent = sites.length;
    document.getElementById('activeSites').textContent = sites.filter(s => s.active).length;
    // Total products would need a separate API call or be included in the sites response
    document.getElementById('totalProducts').textContent = '0';
}

function showAlert(type, message) {
    const alertContainer = document.getElementById('alert-container');
    alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
}

// Load sites on page load
loadSites();
</script>

<?php
$scripts = ob_get_clean();

require_once __DIR__ . '/layout.php';
?>

