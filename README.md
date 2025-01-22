# Laravel Project with Docker and AMQP

This project uses Laravel along with Docker, AMQP, Inertia.js, and Jetstream. Below are the steps to install and run the project locally.

## Requirements

Before you begin, ensure you have the following tools installed on your machine:

- PHP 8.2 or higher
- Composer
- Docker
- Docker Compose

## Installation

Follow the steps below to install and set up the project:

### 1. Clone the repository

First, clone the repository to your local machine:

```bash
git clone <repository_url>
cd <project_directory_name>
2. Install dependencies with Composer
Run the following command to install the project's dependencies:

bash
Copiar
composer install
3. Set up the .env file
Copy the .env.example file to .env:

bash
Copiar
cp .env.example .env
Then, open the .env file and adjust the database configuration and other settings according to your environment.

4. Run migrations and seed the database
Once your environment is set up, run the migrations and seed the database (if needed):

bash
Copiar
php artisan migrate
php artisan db:seed
5. Start the development server
Now, you can start the Laravel development server:

bash
Copiar
php artisan serve
The server will be available at http://localhost:8000.

6. Start Docker containers
If the project includes Docker containers, start the services with Docker Compose:

bash
Copiar
docker-compose up
This will start all the services defined in the docker-compose.yml file.

7. Access the application
Once the server is running, you can access the application in your browser at http://localhost:8000 (or the port you have configured).

Additional Commands
Run tests
To run the project's tests, use the following command:

bash
Copiar
php artisan test
Clear the application cache
If you need to clear the application's cache, use the following command:

bash
Copiar
php artisan optimize:clear
Notes
If you're using Docker, ensure that the containers are up and running before attempting to access the application.
If you need to update Composer dependencies, simply run composer update.
License
This project is licensed under the MIT License.

yaml
Copiar

---

### How to save the file:

1. Open any text editor (like Notepad, VS Code, or Sublime Text).
2. Copy the content above.
3. Paste it into the text editor.
4. Save the file as `README.md` in the root directory of your project.

If you need any more help with this, feel free to ask!