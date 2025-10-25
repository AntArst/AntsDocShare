-- Seed data for development

-- Default admin user (password: changeme)
-- Password hash for 'changeme' using PHP password_hash with bcrypt
INSERT INTO users (username, email, password_hash, role) VALUES
('admin', 'admin@example.com', '$2y$10$cgo0i5KetxFZItTkroa10u7fXKPRdS1pa/QJXrBycoYkFrXZgTgp.', 'admin'),
('testuser', 'user@example.com', '$2y$10$cgo0i5KetxFZItTkroa10u7fXKPRdS1pa/QJXrBycoYkFrXZgTgp.', 'user');

-- Sample sites
INSERT INTO sites (name, slug, owner_user_id, active) VALUES
('Demo Store', 'demo-store', 1, TRUE),
('Test Location', 'test-location', 1, TRUE);

-- Sample products for demo site
INSERT INTO products (site_id, item_name, image_name, price, description, assets, sample_image) VALUES
(1, 'Sample Product 1', 'product1.jpg', 19.99, 'This is a sample product description', '{"color": "blue", "size": "medium", "weight": "500g"}', 'sample1.jpg'),
(1, 'Sample Product 2', 'product2.jpg', 29.99, 'Another sample product', '{"color": "red", "size": "large", "weight": "750g"}', 'sample2.jpg'),
(1, 'Sample Product 3', 'product3.jpg', 39.99, 'Premium sample product', '{"color": "green", "size": "small", "weight": "300g"}', 'sample3.jpg');

-- Sample upload record
INSERT INTO uploads (site_id, user_id, status) VALUES
(1, 1, 'completed');

-- Sample generated package
INSERT INTO generated_packages (upload_id, site_id, package_path, version) VALUES
(1, 1, '/storage/packages/1/package_v1.tar.gz', 'v1.0.0');

