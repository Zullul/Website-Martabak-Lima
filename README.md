# Martabak Lima Website

Website dinamis untuk bisnis Martabak Lima dengan fitur admin panel dan database integration.

## Features

- **Mobile-Friendly Design**: Responsive website yang mudah digunakan di perangkat mobile
- **Admin Panel**: 
  - Login admin dengan keamanan session
  - Kelola paket menu martabak (CRUD operations)
  - Kelola data topping yang tersedia
- **Customer Features**:
  - Lihat daftar paket menu yang tersedia dari database
  - Fitur custom order dengan memilih topping dari database
  - Shopping cart functionality
  - WhatsApp integration untuk pemesanan

## Tech Stack

- PHP 7.4+ dengan PDO
- MySQL Database
- Bootstrap 5 untuk responsive design
- JavaScript untuk interaktivitas
- FontAwesome untuk icons
- SweetAlert2 untuk notifications

## Installation

1. **Clone repository**
   ```bash
   git clone https://github.com/Zullul/Website-Martabak-Lima.git
   cd Website-Martabak-Lima
   ```

2. **Setup Database**
   - Create MySQL database named `martabak_lima`
   - Import database schema:
   ```bash
   mysql -u root -p martabak_lima < database_schema.sql
   ```

3. **Configure Database**
   - Edit `config/database.php` sesuai dengan setup MySQL Anda
   - Default configuration:
     - Host: localhost
     - Database: martabak_lima
     - Username: root
     - Password: (empty)

4. **Setup Web Server**
   - For development:
   ```bash
   php -S localhost:8000
   ```
   - For production: Setup Apache/Nginx dengan document root ke folder ini

5. **Admin Access**
   - URL: `/admin/login.php`
   - Default login: `admin` / `admin123`

## File Structure

```
├── index.php                 # Halaman utama customer
├── admin/                    # Admin panel
│   ├── login.php            # Login admin
│   ├── dashboard.php        # Dashboard admin
│   ├── menu.php             # Kelola paket menu
│   └── topping.php          # Kelola topping
├── config/
│   └── database.php         # Konfigurasi database
├── includes/
│   └── functions.php        # Fungsi-fungsi umum
├── css/
│   └── style.css           # Styling mobile-friendly
├── js/
│   └── script.js           # JavaScript untuk interaktivitas
├── uploads/                 # Upload directory untuk gambar
└── database_schema.sql      # Schema database
```

## Database Tables

- `admin` - Tabel untuk login admin
- `menu_packages` - Tabel paket menu
- `toppings` - Tabel topping
- `orders` - Tabel pesanan customer

## Demo Features

- Responsive mobile-first design
- Menu filtering (Asin/Manis)
- Interactive martabak builder dengan topping selection
- Shopping cart dengan local storage
- WhatsApp integration untuk checkout
- Admin CRUD untuk menu dan topping
- Session management untuk admin
- CSRF protection
- Image upload untuk menu

## Development

Website ini dikembangkan dengan fokus pada:
- Clean code dan best practices
- Security (CSRF protection, input sanitization)
- Mobile-first responsive design
- User experience yang smooth
- Admin panel yang user-friendly

## License

Copyright © 2025 Martabak Lima. All rights reserved.