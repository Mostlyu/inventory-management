CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  description VARCHAR(150)
);

CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(150),
    category_id INT NOT NULL,
    sku VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

CREATE TABLE stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    type ENUM('in', 'out') NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    note VARCHAR(150) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE RESTRICT
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin', 'lagermitarbeiter') NOT NULL DEFAULT 'lagermitarbeiter',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

-- Seed data (runs on a fresh init) --------------------------------
INSERT INTO categories (name, description) VALUES
  ('Schrauben', 'Schrauben und Muttern'),
  ('Werkzeuge', 'Hand- und Elektrowerkzeuge');

-- Local-dev admin: username 'admin', password 'admin123'.
-- (Hardcoding a known-password account is fine for a learning DB only;
--  a real app would seed users through a setup script / first-run flow.)
INSERT INTO users (username, password_hash, role) VALUES
  ('admin', '$2y$10$PHaZMYhb70mYC1pUBNl/xei2/97.EDpSmGG2H6jGWueSb6DvX9CbW', 'admin'),
  ('lager', '$2y$10$0MA8BofsHKg21v9PwiOXQ.1Nz4eP6Gg4CG3Rd3b8NuhZTngmU7O4i', 'lagermitarbeiter');