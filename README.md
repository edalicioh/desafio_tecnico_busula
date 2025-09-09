# Project Readme

This project consists of a Laravel backend and a Nuxt.js frontend, orchestrated with Docker.

## Prerequisites

- Docker
- Docker Compose

## Getting Started

1.  **Clone the repository:**

    ```bash
    git clone git@github.com:edalicioh/desafio_tecnico_busula.git
    cd desafio_tecnico_busula
    ```

2.  **Environment Files:**

    -   There is a `.env-back` file in the `devops/` directory for the backend. If you need to customize the backend environment, you can modify this file.

3.  **Build and run the application:**

    Navigate to the `devops/` directory and run the following command:

    ```bash
    docker-compose up --build
    ```

    This will build the Docker images for the frontend and backend and start the services.

4.  **Access the application:**

    -   **Frontend:** [http://localhost:3000](http://localhost:3000)
    -   **Backend API:** [http://localhost:8001](http://localhost:8001)

5.  **Backend setup:**

    After starting the containers, you need to run the database migrations and seeders for the backend.

    ```bash
    docker-compose exec backend php artisan migrate --seed
    ```

## Services

-   `backend`: Laravel 12 application running on PHP 8.4.
-   `frontend`: Nuxt.js application.
-   `database`: PostgreSQL 16 database.

## Stopping the application

To stop the application, run the following command in the `devops/` directory:

```bash
docker-compose down
```

## Running without Docker

### Backend (Laravel)

1.  **Navigate to the backend directory:**
    ```bash
    cd backend
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Create the environment file:**
    ```bash
    cp .env.example .env
    ```

4.  **Generate the application key:**
    ```bash
    php artisan key:generate
    ```

5.  **Configure your `.env` file** with your database credentials and any other necessary settings.

6.  **Run database migrations and seeders:**
    ```bash
    php artisan migrate --seed
    ```

7.  **Start the development server:**
    ```bash
    php artisan serve
    ```
    The backend will be available at `http://localhost:8000`.

### Frontend (Nuxt.js)

1.  **Navigate to the frontend directory:**
    ```bash
    cd frontend
    ```

2.  **Install Node.js dependencies:**
    ```bash
    npm install
    ```

3.  **Start the development server:**
    ```bash
    npm run dev
    ```
    The frontend will be available at `http://localhost:3000`.