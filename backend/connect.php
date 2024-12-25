<!-- 

<?php

/**
 * Create a secure and professional database connection
 * install composer and phpmailer
 
 * Install "composer require vlucas/phpdotenv" in the root directory to use the .env file
 * composer require phpmailer/phpmailer
 * 
 * composer install: This command will create the vendor folder and generate the vendor/autoload.php file.
 

 // The path to require the autoload.php file is static

//  require_once '../vendor/autoload.php';

// The path to require the autoload.php file is dynamic

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables from the .env file

Dotenv::createImmutable(dirname(__DIR__))->load();;





// Use getenv() for environment variables
define('DB_SERVER', getenv('DB_SERVER') ?: 'localhost');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
define('DB_DATABASE', getenv('DB_DATABASE') ?: 'ecommerce');



// Create a secure and professional database connection
class Database
{
    private $connection;

    public function __construct()
    {
        $this->connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

        if ($this->connection->connect_error) {
            error_log("Database connection failed: " . $this->connection->connect_error);
            die("Connection failed. Please try again later.");
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function closeConnection()
    {
        $this->connection->close();
    }
}

// Usage example
$db = new Database();
$connection = $db->getConnection();

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
*/



require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables from the .env file
Dotenv::createImmutable(dirname(__DIR__))->load();

// Use getenv() for environment variables with safety checks
if (!defined('DB_SERVER')) {
    define('DB_SERVER', getenv('DB_SERVER') ?: 'localhost');
}

if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
}

if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'Haftey100%WG');
}

if (!defined('DB_DATABASE')) {
    define('DB_DATABASE', getenv('DB_DATABASE') ?: 'lisaboutique');
}

// Create a secure and professional database connection
if (!class_exists('Database')) {
    class Database
    {
        private $connection;

        public function __construct()
        {
            $this->connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

            if ($this->connection->connect_error) {
                error_log("Database connection failed: " . $this->connection->connect_error);
                die("Connection failed. Please try again later.");
            }
        }

        public function getConnection()
        {
            return $this->connection;
        }

        public function closeConnection()
        {
            $this->connection->close();
        }
    }
}

// Usage example
$db = new Database();
$connection = $db->getConnection();

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

?>



