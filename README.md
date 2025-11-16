# XERO Collections

A comprehensive Laravel-based web application for managing invoice collections and contact management with integrated email templating and Xero accounting software integration.

## About

XERO Collections is a full-featured web application built with Laravel 11 and Vue 3, designed to streamline invoice collection management and customer relationship management.

The aim of this application is to follow up on overdue invoices and send a series of legal notices to those who do not respond in time, and record each activity to generate reports.

The application provides:

- **Invoice Management**: Create, manage, and track collection invoices with detailed status monitoring
- **Contact Management**: Maintain and organize customer contact information
- **Email Templates**: Customizable email templates for automated communications
- **Activity Tracking**: Comprehensive user activity logging and audit trails
- **Xero Integration**: Seamless integration with Xero accounting software for real-time data synchronization
- **Role-Based Access Control**: Secure role management with customizable permissions
- **PDF Generation**: Automatic PDF generation for invoices and documents
- **Background Job Processing**: Efficient email delivery and document processing using Laravel queues

## System Requirements

### Server Requirements

- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Node.js**: 16.x or higher
- **npm** or **yarn**: Package manager
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Mail Server**: SMTP configuration for email delivery

### Development Requirements

- Docker & Docker Compose (for containerized development)
- Git
- A modern code editor (VS Code, PHPStorm, etc.)

### Required PHP Extensions

- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- GD
- cURL

## Installation Instructions

### Step 1: Clone the Repository

```bash
git clone https://github.com/muhammad-shariq/xero-collections.git
cd xero-collections
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node.js Dependencies

```bash
npm install
```

### Step 4: Environment Configuration

Copy the `.env.example` file and create your configuration:

```bash
cp .env.example .env
```

Update the following variables in `.env`:

```
APP_NAME="Collections Web App"
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=collections_webapp
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_FROM_ADDRESS=noreply@collections.app

XERO_CLIENT_ID=your_xero_client_id
XERO_CLIENT_SECRET=your_xero_client_secret
XERO_TENANT_ID=your_xero_tenant_id
```

### Step 5: Generate Application Key

```bash
php artisan key:generate
```

### Step 6: Database Setup

Create the database and run migrations:

```bash
php artisan migrate
```

Optionally, seed sample data:

```bash
php artisan db:seed
```

### Step 7: Build Frontend Assets

```bash
npm run build
```

For development with hot module replacement:

```bash
npm run dev
```

### Step 8: Start the Application

Using Laravel's built-in development server:

```bash
php artisan serve
```

Or using Docker Compose:

```bash
docker-compose up -d
```

The application will be available at `http://localhost:8000`

## Demo App URL and Credentials

### Demo Application

- **URL**: [https://app.collections.com.au](https://app.collections.com.au)

### Sample Login Credentials

- **Email**: shariq2k@yahoo.com
- **Password**: DemoPassword123!

### Test Data

The demo application includes sample invoices, contacts, and email templates for testing purposes. Note that Xero integration on the demo account may have limited API calls.

## Contact Us

### Support & Inquiries

- **Email**: shariq2k@yahoo.com
- **GitHub**: [https://github.com/muhammad-shariq/xero-collections](https://github.com/muhammad-shariq/xero-collections)

### Report Issues

For bug reports and feature requests, please visit our [GitHub Issues page](https://github.com/muhammad-shariq/xero-collections/issues).

### Contributing

We welcome contributions! Please feel free to submit a Pull Request with your improvements.

## Security Vulnerabilities

If you discover a security vulnerability within the app, please send an email to Muhammad Shariq via [shariq2k@yahoo.com](mailto:shariq2k@yahoo.com). All security vulnerabilities will be promptly addressed.

## License

The Xero Collections is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

**Built with ❤️ by Muhammad Shariq**