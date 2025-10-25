<?php
$title = 'Login - PDGP';
$hideNav = true;

ob_start();
?>

<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body p-5">
                <h2 class="text-center mb-4">
                    <i class="bi bi-box-seam"></i> PDGP Login
                </h2>
                
                <div id="alert-container"></div>

                <form id="loginForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>

                <div class="text-center mt-3">
                    <small class="text-muted">Default credentials: admin / changeme</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

ob_start();
?>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    try {
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username, password })
        });
        
        const data = await response.json();
        
        if (data.success) {
            localStorage.setItem('pdgp_token', data.token);
            localStorage.setItem('pdgp_user', JSON.stringify(data.user));
            window.location.href = '/';
        } else {
            showAlert('danger', data.error || 'Login failed');
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

