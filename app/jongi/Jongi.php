<?php

/**
 * Jongi class 
 */

defined('CPATH') or exit('Access Denied!');


class Jongi
{
	private $version = '1.1.0';
	// Create classes in the comand line
	public function db($argv)
	{
		$mode 		= $argv[1] ?? null;
		$param1 	= $argv[2] ?? null;

		// Restrict input by the user - allowed characters
		$param1 = preg_replace("/[^a-zA-Z0-9_]+/", "", $param1);
		// Restrict input by the user - avoid database names starting with numbers
		if (preg_match("/^[0-9]+/", $param1)) {
			die("\n\rDB/Table name cannot start with a number!\n\r");
		}

		switch ($mode) {
			case 'db:create':

				// Check if $param1 is not empty
				if (empty($param1))
					die("\n\rPlease provide a database name\n\r");

				$db = new CLIDB;
				$query = "CREATE DATABASE IF NOT EXISTS " . $param1;
				$db->query($query);

				echo "\n\rGood News!! Database Created Successfully :)\n\r";

				break;

			case 'db:table':
				// Check if $param1 is not empty
				if (empty($param1))
					die("\n\rPlease provide a table name\n\r");

				$db = new CLIDB;
				$query = "DESCRIBE " . $param1;
				$result = $db->query($query);

				if ($result) {
					print_r($result);
				} else {
					echo "\n\rCould not find data for table: $param1\n\r";
				}

				die;

				break;

			case 'db:drop':
				// Check if $param1 is not empty
				if (empty($param1))
					die("\n\rPlease provide a database name\n\r");

				$db = new CLIDB;
				$query = "DROP DATABASE " . $param1;
				$db->query($query);

				echo "\n\rDatabase deleted\n\r";
				break;

			case 'db:seed':
				echo 'Make Seed';
				break;

			default:
				die("\n\rUnknown command '$argv[1]'!");
				break;
		}
	}

	public function list($argv)
	{
		$mode       = $argv[1] ?? null;

		switch ($mode) {
			case 'list:migrations':

				$folder = 'app' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;
				if (!file_exists($folder)) {
					die("\n\rNo migration files were found\n\r");
				}

				$files = glob($folder . "*.php");
				echo "\n\rAvailable migration files: " . count($files) . "\n\r";

				foreach ($files as $file) {
					echo basename($file) . "\n\r";
				}
				break;

			default:
				// code...
				break;
		}
	}

	public function make($argv)
	{
		$mode 		= $argv[1] ?? null;
		$classname 	= $argv[2] ?? null;
		if (empty($classname))
			die("\n\rPlease provide a class name\n\r");
		// Restrict input by the user - allowed characters
		$classname = preg_replace("/[^a-zA-Z0-9_]+/", "", $classname);
		// Restrict input by the user - avoid controller names starting with numbers
		if (preg_match("/^[0-9]+/", $classname)) {
			die("\n\rController name cannot start with a number!\n\r");
		}


		switch ($mode) {
			case 'make:controller':
				// Create File Name (Controller)
				$filename = 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . ucfirst($classname) . '.php';
				// Check if the Controller exists already or not
				if (file_exists($filename))
					die("\n\rThat Controller already exists.\n\r");
				// Load the content of the sample controller file
				$sample_file_path = 'app' . DIRECTORY_SEPARATOR . 'jongi' . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'controller-sample.php';
				$sample_file = file_get_contents($sample_file_path);

				// Replace placeholders with the actual class name
				$sample_file = preg_replace("/CLASSNAME/", ucfirst($classname), $sample_file);
				$sample_file = preg_replace("/\{classname\}/", strtolower($classname), $sample_file);

				// Write the modified content to the new controller file
				file_put_contents($filename, $sample_file);
				if (file_put_contents($filename, $sample_file)) {
					echo "\n\rGood News!! Controller Created Successfully :)\n\r";
				} else {
					echo "\n\rAn error occured! Failed to create Controller\n\r";
				}
				break;
			case 'make:model':
				// Create File Name (Controller)
				$filename = 'app' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . ucfirst($classname) . '.php';
				// Check if the Model exists already or not
				if (file_exists($filename))
					die("\n\rThat Controller already exists.\n\r");
				// Load the content of the sample model file
				$sample_file_path = 'app' . DIRECTORY_SEPARATOR . 'jongi' . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'model-sample.php';
				$sample_file = file_get_contents($sample_file_path);

				// Replace placeholders with the actual class name
				$sample_file = preg_replace("/CLASSNAME/", ucfirst($classname), $sample_file);
				if (!preg_match("/[s]$/", $classname)) {
					$sample_file = preg_replace("/\{table\}/", strtolower($classname) . 's', $sample_file);
				} else {
					$sample_file = preg_replace("/\{table\}/", strtolower($classname), $sample_file);
				}


				// Write the modified content to the new model file
				file_put_contents($filename, $sample_file);
				if (file_put_contents($filename, $sample_file)) {
					echo "\n\rGood News!! Model Created Successfully :)\n\r";
				} else {
					echo "\n\rAn error occured! Failed to create Model\n\r";
				}
				break;
			case 'make:migration':
				$folder = 'app' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;

				if (!file_exists($folder)) {
					mkdir($folder, 0777, true);
				}

				$filename = $folder . "Ntoshi_" . date("jS_M_Y_H_i_s_") . ucfirst($classname) . ".php";
				if (file_exists($filename))
					die("\n\rThat migration file already exists\n\r");

				$sample_file = file_get_contents('app' . DIRECTORY_SEPARATOR . 'jongi' . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'migration-sample.php');
				$sample_file = preg_replace("/CLASSNAME/", ucfirst($classname), $sample_file);
				$sample_file = preg_replace("/\{classname\}/", strtolower($classname), $sample_file);

				if (file_put_contents($filename, $sample_file)) {
					die("\n\r Good News!! Migration file created: " . basename($filename) . " \n\r");
				} else {
					die("\n\rFailed to create Migration file due to an error\n\r");
				}
				break;
			case 'make:seeder':
				echo 'Make Seeder';
				break;

			default:
				die("\n\rUnknown command '$argv[1]'!");
				break;
		}
	}
	public function migrate($argv)
	{
		$mode = $argv[1] ?? null;

		// Check if mode is provided
		if ($mode === null) {
			die("\n\rPlease provide a migration mode (migrate, migrate:rollback, migrate:refresh, or migrate:all)\n\r");
		}

		// Migrate all migrations
		if ($mode === 'migrate:all') {
			$migrationFiles = glob("app/migrations/*.php");

			if (empty($migrationFiles)) {
				die("\n\rNo migration files found\n\r");
			}

			foreach ($migrationFiles as $filename) {
				require_once $filename;

				preg_match("/[a-zA-Z]+\.php$/", $filename, $match);
				$classname = str_replace(".php", "", $match[0]);

				$myclass = new $classname();

				$myclass->alpha();

				echo "\n\rMigration file ran successfully: " . basename($filename) . " \n\rOne table affected";
			}
		} else {
			// Migrate a single migration
			$filename = $argv[2] ?? null;

			if ($filename === null) {
				die("\n\rPlease provide a migration file name\n\r");
			}

			$filename = "app/migrations/" . $filename;

			if (!file_exists($filename)) {
				die("\n\rMigration file not found\n\r");
			}

			require_once $filename;

			preg_match("/[a-zA-Z]+\.php$/", $filename, $match);
			$classname = str_replace(".php", "", $match[0]);

			$myclass = new $classname();

			switch ($mode) {
				case 'migrate':
					$myclass->alpha();
					break;
				case 'migrate:rollback':
					$myclass->omega();
					break;
				case 'migrate:refresh':
					$myclass->omega();
					$myclass->alpha();
					echo ("\n\rTables(s) refreshed successfully\n\r");
					break;
				default:
					die("\n\rUnknown command '$mode'!");
					break;
			}

			echo "\n\rMigration file ran successfully: " . basename($filename) . " \n\rOne table affected";
		}
	}

	// Spin the PHP Server
	public function serve($argv)
	{
		$port = $argv[2] ?? '5001'; // Default port 5001 if not specified

		// Validate port number
		if (!is_numeric($port)) {
			die("\n\rPort must be a number!\n\r");
		}
		if ($port < 1024 || $port > 65535) {
			die("\n\rPort must be between 1024 and 65535\n\r");
		}

		$publicDir = 'public';
		if (!is_dir($publicDir)) {
			die("\n\rPublic directory not found!\n\r");
		}

		$url = "http://localhost:$port";

		echo "\n\rStarting PHP development server on port $port...\n\r";
		echo "Document root is $publicDir\n\r";
		echo "Server URL: $url\n\r";
		echo "Press Ctrl-C to quit\n\r\n\r";

		// Open browser automatically
		$this->openBrowser($url);

		// Start the server with router for URL rewriting
		$routerFile = "$publicDir/router.php";
		if (file_exists($routerFile)) {
			passthru("php -S localhost:$port -t $publicDir $routerFile");
		} else {
			passthru("php -S localhost:$port -t $publicDir");
		}
	}

	private function openBrowser($url)
	{
		// Determine the operating system
		$os = strtoupper(substr(PHP_OS, 0, 3));

		try {
			switch ($os) {
				case 'WIN': // Windows
					exec("start $url");
					break;
				case 'DAR': // MacOS
					exec("open $url");
					break;
				case 'LIN': // Linux
					exec("xdg-open $url");
					break;
				default:
					echo "\n\rCould not automatically open browser on this OS. Please open manually: $url\n\r";
					break;
			}
		} catch (Exception $e) {
			echo "\n\rFailed to open browser automatically. Please open manually: $url\n\r";
		}
	}


	// List 'Help' Menu in the Command line 
	public function help()
	{
		echo "

    Jongi v$this->version Command Line Tool.

    **SERVER COMMANDS**
        serve [port]      - Starts PHP development server (default port: 5001)

    **DB-RELATED FUNCTIONS**
        db:create        - Creates a new Database Schema.
        db:seed         - Runs the specified seeder to populate known data into the Database.
        db:table        - Retrieves information contained within the selected table.
        db:drop         - Drops/Deletes a Database.
        migrate         - Locates and runs a migration from the specified folder.
        migrate:all         - Locates and runs migrations from the specified folder.
        migrate:rollback    - Runs the 'omega' method for a migration file(ie deletes the table).
        migrate:refresh     - Rolls Back and subsequently refreshes to the current state of the Database.

    **GENERATOR FUNCTIONS**
        *DB related*

        make:migration      - Generates a new migration file.
        make:seeder     - Generates a new seeder file.
        list:migrations     - Makes a list of all available migration files.

        *MVC related*

        make:model      - Generates a new Model file.
        make:controller     - Generates a new Controller file.
    ";
	}
}
