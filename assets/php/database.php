<?php

function getDatabaseConnection(){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "mpahdb";

    // Connect to MySQL server without specifying a database
    $connection = new mysqli($servername, $username, $password);

    if ($connection->connect_error) {
        die("Failed to connect to MySQL: " . $connection->connect_error);
    }

    // Check if the database exists, and if not, create it
    $db_check_query = "SHOW DATABASES LIKE '$database'";
    $result = $connection->query($db_check_query);

    if ($result->num_rows == 0) {
        // Database does not exist, create it
        $create_db_query = "CREATE DATABASE $database";
        if ($connection->query($create_db_query) === TRUE) {
            echo "Database created successfully";
        } else {
            die("Error creating database: " . $connection->error);
        }
    }

    // Connect to the database
    $connection->select_db($database);

    // Ensure the dbusers table exists
    createDbUsersTableIfNotExists($connection);

    return $connection;
}

function createDbUsersTableIfNotExists($connection) {
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS dbusers (
            userID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            userName VARCHAR(100) NOT NULL,
            userEmail VARCHAR(100) NOT NULL,
            userPhone VARCHAR(20) NOT NULL,
            password VARCHAR(255) NOT NULL
        )
    ";

    if ($connection->query($createTableQuery) !== TRUE) {
        die("Error creating dbusers table: " . $connection->error);
    }
}

function checkUserEmail($email){
    $connection = getDatabaseConnection();
    if (!$connection) {
        return false;
    }
    $statement = $connection->prepare("SELECT userID FROM dbusers WHERE userEmail = ?");
    if (!$statement) {
        mysqli_close($connection);
        return false;
    }
    $statement->bind_param("s", $email);
    $statement->execute();
    $statement->store_result();
    $result = $statement->num_rows === 0;
    $statement->close();
    mysqli_close($connection);
    return $result;
}

function addDatabaseUser($name, $email, $phone, $password) {
    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    // Establish database connection
    $connection = getDatabaseConnection();
    if (!$connection) {
        return false;
    }
    // Insert user into dbusers table
    $insertUserQuery = "INSERT INTO dbusers (userName, userEmail, userPhone, password) VALUES (?, ?, ?, ?)";
    $insertUserStatement = $connection->prepare($insertUserQuery);
    if (!$insertUserStatement) {
        mysqli_close($connection);
        return false;
    }
    $insertUserStatement->bind_param('ssss', $name, $email, $phone, $passwordHash);
    $insertUserStatement->execute();
    // Check if user was inserted successfully
    if ($insertUserStatement->affected_rows <= 0) {
        $insertUserStatement->close();
        mysqli_close($connection);
        return false;
    }
    // Get the inserted user's ID
    $userID = $insertUserStatement->insert_id;
    $insertUserStatement->close();
    // Create a new table for the user
    $createUserTableQuery = "CREATE TABLE IF NOT EXISTS dbuser$userID (
        plantID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        commonName VARCHAR(100) NOT NULL,
        scientificName VARCHAR(100) NOT NULL,
        stock INT NOT NULL,
        price INT NOT NULL
    )";
    $createUserTableStatement = $connection->prepare($createUserTableQuery);
    if (!$createUserTableStatement) {
        mysqli_close($connection);
        return false;
    }
    $createUserTableStatement->execute();
    $createUserTableStatement->close();
    mysqli_close($connection);
    return true;
}

function setDatabaseUser($name, $phone) {
    // Ensure userID is set
    if (!isset($_SESSION['userID'])) {
        return false;
    }

    $userID = intval($_SESSION['userID']); // Sanitize the userID

    // Establish database connection
    $connection = getDatabaseConnection();
    if (!$connection) {
        return false;
    }

    // Update user in the dbusers table
    $updateUserQuery = "UPDATE dbusers SET userName = ?, userPhone = ? WHERE userID = ?";
    $updateUserStatement = $connection->prepare($updateUserQuery);
    if (!$updateUserStatement) {
        $connection->close();
        return false;
    }

    $updateUserStatement->bind_param('ssi', $name, $phone, $userID);
    $updateUserStatement->execute();

    // Check if the query executed successfully
    if ($updateUserStatement->errno) {
        $updateUserStatement->close();
        $connection->close();
        return false;
    }

    // Close statement and connection
    $updateUserStatement->close();
    $connection->close();

    return true;
}

function getLoginSession($email, $password) {
    $connection = getDatabaseConnection();
    if (!$connection) {
        return null; // Handle database connection error
    }
    $statement = $connection->prepare(
        "SELECT userID, userName, userEmail, userPhone, password FROM dbusers WHERE userEmail = ?"
    );
    if (!$statement) {
        mysqli_close($connection);
        return null; // Handle prepare statement error
    }
    $statement->bind_param('s', $email);
    $statement->execute();
    $statement->store_result();
    if ($statement->num_rows > 0) {
        $statement->bind_result($userID, $userName, $userEmail, $userPhone, $storedPassword);
        $statement->fetch();
        if (password_verify($password, $storedPassword)) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['userID'] = $userID;
            $_SESSION['userName'] = $userName;
            $_SESSION['userEmail'] = $userEmail;
            $_SESSION['userPhone'] = $userPhone;
            $statement->close();
            mysqli_close($connection);
            return $_SESSION; // Return session data
        } else {
            $statement->close();
            mysqli_close($connection);
            return null; // Incorrect password
        }
    } else {
        $statement->close();
        mysqli_close($connection);
        return null; // User not found
    }
}

function getAllPlant() {
    if (!isset($_SESSION['userID'])) {
        return null; // Ensure userID is set
    }

    $userID = intval($_SESSION['userID']); // Sanitize the userID

    $connection = getDatabaseConnection();
    if ($connection->connect_error) {
        $connection->close();
        return null;
    }

    $query = "SELECT * FROM dbuser$userID"; // Construct query
    $result = $connection->query($query);

    if (!$result) {
        $connection->close();
        return null;
    } else {
        // Fetch all data
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $connection->close();
        return $data;
    }
}

function addPlant($cName, $sName, $stock, $price) {
    // Ensure userID is set
    if (!isset($_SESSION['userID'])) {
        return false;
    }

    $userID = intval($_SESSION['userID']); // Sanitize the userID

    // Establish database connection
    $connection = getDatabaseConnection();
    if (!$connection) {
        return false;
    }

    // Insert plant into the user's table
    $insertPlantQuery = "INSERT INTO dbuser$userID (commonName, scientificName, stock, price) VALUES (?, ?, ?, ?)";
    $insertPlantStatement = $connection->prepare($insertPlantQuery);
    if (!$insertPlantStatement) {
        $connection->close();
        return false;
    }

    $insertPlantStatement->bind_param('ssii', $cName, $sName, $stock, $price);
    $insertPlantStatement->execute();

    // Check if plant was inserted successfully
    if ($insertPlantStatement->affected_rows <= 0) {
        $insertPlantStatement->close();
        $connection->close();
        return false;
    }

    // Close statement and connection
    $insertPlantStatement->close();
    $connection->close();

    return true;
}

function getPlant($plantID) {
    if (!isset($_SESSION['userID'])) {
        return null; // Ensure userID is set
    }

    $userID = intval($_SESSION['userID']); // Sanitize the userID

    $connection = getDatabaseConnection();
    if ($connection->connect_error) {
        $connection->close();
        return null;
    }

    // Use prepared statement to prevent SQL injection
    $query = "SELECT * FROM dbuser$userID WHERE plantID = ?";
    $statement = $connection->prepare($query);
    if (!$statement) {
        $connection->close();
        return null;
    }

    $statement->bind_param('i', $plantID); // Bind the plantID parameter
    $statement->execute();
    $result = $statement->get_result();

    if (!$result) {
        $statement->close();
        $connection->close();
        return null;
    }

    $data = $result->fetch_assoc(); // Fetch single row
    $statement->close();
    $connection->close();

    return $data;
}

function setPlant($plantID, $cName, $sName, $stock, $price) {
    // Ensure userID is set
    if (!isset($_SESSION['userID'])) {
        return false;
    }

    $userID = intval($_SESSION['userID']); // Sanitize the userID

    // Establish database connection
    $connection = getDatabaseConnection();
    if (!$connection) {
        return false;
    }

    // Update plant in the user's table
    $updatePlantQuery = "UPDATE dbuser$userID SET commonName = ?, scientificName = ?, stock = ?, price = ? WHERE plantID = ?";
    $updatePlantStatement = $connection->prepare($updatePlantQuery);
    if (!$updatePlantStatement) {
        $connection->close();
        return false;
    }

    $updatePlantStatement->bind_param('ssiii', $cName, $sName, $stock, $price, $plantID);
    $updatePlantStatement->execute();

    // Check if plant was updated successfully
    if ($updatePlantStatement->affected_rows <= 0) {
        $updatePlantStatement->close();
        $connection->close();
        return false;
    }

    // Close statement and connection
    $updatePlantStatement->close();
    $connection->close();

    return true;
}

function searchPlant($searchString) {
    if (!isset($_SESSION['userID'])) {
        return null; // Ensure userID is set
    }

    $userID = intval($_SESSION['userID']); // Sanitize the userID

    $connection = getDatabaseConnection();
    if ($connection->connect_error) {
        $connection->close();
        return null;
    }

    // Prepare the query
    $query = "SELECT * FROM dbuser$userID WHERE commonName LIKE ? OR scientificName LIKE ?";
    $stmt = $connection->prepare($query);
    if (!$stmt) {
        $connection->close();
        return null;
    }

    $searchTerm = '%' . $searchString . '%';
    $stmt->bind_param('ss', $searchTerm, $searchTerm); // Bind the search string

    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        $stmt->close();
        $connection->close();
        return null;
    }

    // Fetch all data
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $connection->close();

    return $data;
}

function removePlant($plantID){
    // Ensure userID is set
    if (!isset($_SESSION['userID'])) {
        return false;
    }

    $userID = intval($_SESSION['userID']); // Sanitize the userID

    // Establish database connection
    $connection = getDatabaseConnection();
    if ($connection->connect_error) {
        return false;
    }

    // Prepare the delete query
    $deletePlantQuery = "DELETE FROM dbuser$userID WHERE plantID = ?";
    $deletePlantStatement = $connection->prepare($deletePlantQuery);
    if (!$deletePlantStatement) {
        $connection->close();
        return false;
    }

    // Bind parameters and execute the statement
    $deletePlantStatement->bind_param('i', $plantID);
    $deletePlantStatement->execute();

    // Check if the plant was deleted successfully
    if ($deletePlantStatement->affected_rows <= 0) {
        $deletePlantStatement->close();
        $connection->close();
        return false;
    }

    // Close statement and connection
    $deletePlantStatement->close();
    $connection->close();

    return true;
}

function removeAll() {
    // Ensure userID is set
    if (!isset($_SESSION['userID'])) {
        return false;
    }

    $userID = intval($_SESSION['userID']); // Sanitize the userID

    // Establish database connection
    $connection = getDatabaseConnection();
    if (!$connection) {
        return false;
    }

    // Prepare the delete query
    $deleteAllQuery = "DELETE FROM dbuser$userID";
    $deleteAllStatement = $connection->prepare($deleteAllQuery);
    if (!$deleteAllStatement) {
        $connection->close();
        return false;
    }

    // Execute the statement
    $deleteAllStatement->execute();

    // Check if the plants were deleted successfully
    if ($deleteAllStatement->affected_rows <= 0) {
        $deleteAllStatement->close();
        $connection->close();
        return false;
    }

    // Close statement and connection
    $deleteAllStatement->close();
    $connection->close();

    return true;
}

