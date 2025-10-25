<?php
$title = 'Add Site - PDGP';
$currentPage = 'sites';

ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Add New Site</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<div id="alert-container"></div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form id="addSiteForm">
                    <div class="mb-3">
                        <label for="siteName" class="form-label">Site Name</label>
                        <input type="text" class="form-control" id="siteName" name="name" required>
                        <div class="form-text">
                            A unique name for this site/location. A slug will be generated automatically.
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Site
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-info-circle"></i> What is a Site?</h5>
                <p class="card-text">
                    A site represents a client organization, store location, or display configuration. 
                    Each site can have its own products and generated packages.
                </p>
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

if (!token) {
    window.location.href = '/login';
}

document.getElementById('addSiteForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const name = document.getElementById('siteName').value;
    
    try {
        const response = await fetch('/api/sites', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({ name })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('success', 'Site created successfully!');
            setTimeout(() => {
                window.location.href = '/sites/' + data.site.id;
            }, 1500);
        } else {
            showAlert('danger', data.error || 'Failed to create site');
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
</script>

<?php
$scripts = ob_get_clean();

require_once __DIR__ . '/layout.php';
?>

