# Learner Progress Dashboard

A Laravel + Vue.js application for tracking learner progress across courses.

## Features

- View learner progress statistics
- Filter learners by enrolled courses
- Sort learners by progress percentage
- Responsive design

## Getting Started

### Prerequisites

Make sure you have these installed on your Linux machine:

- PHP 8.0+
- Composer
- Node.js 14+
- npm
- SQLite

### Installation

1. **Clone the repository**:
```bash
git clone https://github.com/Pieter1254/learner-progress.git
cd learner-progress
```

2. **Install PHP dependencies**:
```bash
composer install
```

3. **Install JavaScript dependencies**:
```bash
npm install
```

4. **Set up SQLite database**:
```bash
mkdir -p database
touch database/database.sqlite
chmod 755 database
chmod 644 database/database.sqlite
```

5. **Configure environment**:
```bash
cp .env.example .env
```

6. **Edit .env and set these values**:
```.env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/your/learner-progress/database/database.sqlite
```

7. **Generate application key**:
```bash
php artisan key:generate
```

8. **Run database migrations and seed data**:
```bash
php artisan migrate --seed
```

9. **Build frontend assets (development)**:
```bash
npm run dev
```

10. **Start the development server**:
```bash
    php artisan serve
```

Running the Application
Access the application at:
http://localhost:8000

Database Structure
The SQLite database is located at:
/database/database.sqlite