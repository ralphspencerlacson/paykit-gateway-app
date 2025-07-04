# Dockerized Laravel + Frontend + MySQL Setup

This project is a modular Docker setup with separated folders for the database(MySQL/phpMyAdmin), backend (Laravel), and frontend (React/Vite).

## Project Structure

```
project-root/
│
├── pga-backend/                        # Laravel
│   └── app/
│   ├── Dockerfile
│   └── docker-compose.yml
│
├── pga-frontend/                       # React + Vite
│   └── app/
│   ├── Dockerfile
│   └── docker-compose.yml
│
├── pga-database/                       # MySQL + phpMyAdmin
│   └── docker-compose.yml
│
├── README.md
```

## Quick Start

### 1. Clone the Project
```bash
git clone https://github.com/your-username/your-project.git
```
- cd your-project

### 2. Setup Database
```bash
cd pga-database
```
- Customize docker-compose.yml (e.g., MySQL password, database name).

Then run:

```bash
docker-compose up -d
```

### 3. Setup Backend (Laravel)

```bash
cd ../pga-backend
```

- Customize docker-compose.yml and .env:
    - Set DB host, port, user, password (match database container).

Then run:

```bash
docker-compose up -d
```

Enter the container and run migrations:

```bash
docker exec -it pga-laravel-api php artisan migrate
```

### 4. Setup Frontend

```bash
cd ../pga-frontend
```

- Customize docker-compose.yml and .env (e.g., API base URL).

Then run:

```bash
docker-compose up -d
```

You're Done!
Now access your services:

- Backend API: http://localhost:30002
- phpMyAdmin: http://localhost:30001
- Frontend App: (depends on your configured port, e.g., http://localhost:30003)

Customize folders, container names, ports, and .env settings to match your local environment or project name.
