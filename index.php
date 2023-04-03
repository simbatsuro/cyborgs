<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ini_set('max_execution_time', 300); // Set maximum execution time to 60 seconds


    session_start();
       
    $NAMES = array(
        "Johannes",
        "Maria",
        "Elizabeth",
        "David",
        "Anna",
        "John",
        "Michael",
        "Sibongile",
        "Johanna",
        "Sipho",
        "Joseph",
        "Bongani",
        "Lindiwe",
        "Jan",
        "Daniel",
        "Martha",
        "Busisiwe",
        "Sibusiso",
        "Thabo",
        "Mpho"
    );
    $SURNAMES = array(
        "Dlamini",
        "Nkosi",
        "Ndlovu",
        "Khumalo",
        "Sithole",
        "Botha",
        "Mahlangu",
        "Mokoena",
        "Smith",
        "Naidoo",
        "Mkhize",
        "Mthembu",
        "Ngcobo",
        "Gumede",
        "Jacobs",
        "Buthelezi",
        "Zulu",
        "Nel",
        "Pretorius",
        "Venter"
    );
    $cyborgQuantity =  $fileDownloaded = '';
   
    if (isset($_POST['create_button'])) {

        // Validate user input
        if (empty($_POST['quantity'])) {
            $quantityErr = 'Please specify the number of cyborgs you want!';
        } else {
            $cyborgQuantity = $_POST['quantity'];
            // Check for digits only
            if (!preg_match('/^[0-9]*$/', $cyborgQuantity)) {
                $quantityErr = 'Please specify the number of cyborgs in digits!';
            }
            //Check for digits length
            if ($cyborgQuantity < 0 || $cyborgQuantity > 1000000) {
                $quantityErr = 'Please enter a number between 1 to 1 000 000!';
            }
        }

        if (empty($quantityErr)) {
            create_csv_file($NAMES, $SURNAMES, $cyborgQuantity);

            // prompt user to download the file
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="output.csv"');
            
            // output the file contents for download
            readfile('output/output.csv');
        
            // register a shutdown function to set the session variable
            register_shutdown_function(function() {
                $_SESSION['success'] = true;
            });
             
            // call exit to prevent extra content from being sent to the file.
            exit;
        }
    }

    // check if the download was successful and display the success message
    if (isset($_SESSION['success']) && $_SESSION['success']) {
        echo "File download completed successfully.";
    }

    function create_csv_file($namesArray, $surnamesArray, $cyborgQuantity) {
        try {
            $folder = 'output';
            if (!is_dir($folder)) {
                mkdir($folder);
            }
            $fp = fopen('output/output.csv', 'w'); 
            $header = array('Id', 'Name', 'Surname', 'Initials', 'Age', 'DateOfBirth');
            fputcsv($fp, $header);
        
            $batchSize = 100000;
            $numBatches = ceil($cyborgQuantity / $batchSize);
            $cyborgCounter = 0;
            $batchCounter = 0;
        
            while ($batchCounter < $numBatches) {
                $cyborgsInBatchCounter = 0;
                while ($cyborgsInBatchCounter < $batchSize && $cyborgCounter < $cyborgQuantity) {
                    $givenName = get_name($namesArray);
                    $initials = get_initials($givenName);
                    $surname = get_surname($surnamesArray);
                    $dob = get_dob();
                    $age = get_age($dob);
                    $cyborg = array(
                        'id' => $cyborgCounter + 1,
                        'firstname' => $givenName,
                        'surname' => $surname,
                        'initials' => $initials,
                        'age' => $age,
                        'dob' => $dob
                    );
                    $row = array_values($cyborg);
                    fputcsv($fp, $row);
        
                    $cyborgCounter++;
                    $cyborgsInBatchCounter++;
                }
                $batchCounter++;
            }
            fclose($fp);
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getMessage();
        }
    }

    function get_name($namesArray) {
        $firstnameKeys = array_rand($namesArray, mt_rand(1, 3));
        if (is_array($firstnameKeys)) {         
            $firstname_s = '';
            foreach ($firstnameKeys as $firstnameKey) {
                $firstnameValue = $namesArray[$firstnameKey];
                $firstname_s .= $firstnameValue . ' ';
                unset($firstnameKey, $firstnameValue);
            }
            unset($firstnameKeys, $namesArray);
            return trim($firstname_s);
        } else {
            return $namesArray[$firstnameKeys];
        }
    }

    function get_initials($givenName) {
        $names = explode(" ", $givenName);
        $firstLetters = '';
        foreach ($names as $name) {
            $firstLetter = ucfirst((substr($name, 0, 1)));
            $firstLetters .= $firstLetter;
            unset($name);
        }
        unset($names, $givenName);
        return $firstLetters;
    }

    function get_surname($surnamesArray) {
        return $surnamesArray[array_rand($surnamesArray)];
    }

    function get_dob() {
        // Set the earliest date of birth to 01 January 1900
        $dobEarliest = strtotime('01/01/1900');
        // Set the latest date of birth to yesterday
        $dobLatest = time() - 86400;
        $dobRandom = mt_rand($dobEarliest, $dobLatest);
        return date('d/m/Y', $dobRandom);
    }

    function get_age($dob) {
        $now = new DateTime();
        $birthdate = DateTime::createFromFormat('d/m/Y', $dob);
        $age = $birthdate->diff($now);
        if ($age->y < 1) {
            unset($birthdate, $now, $dob);
            return $age->m.' months';
        } else {
            unset($birthdate, $now, $dob);
            return $age->y.' years';
        }
    }
?>

<h2>Cyborgs Registry</h2>
<div class="container">
	<div class="form-container container-generate">
		<form class="form-generate" action="" method="post">
			<h1>Step 1</h1>
			<p>Enter the number of desired cyborgs and generate.</p>
            <input type="number" id="quantity" name="quantity" placeholder="No of cyborgs" min="1" max="1000000">
            <input class="button-generate" type="submit" name="create_button" value="Generate">
		</form>
    </div>

	<div class="overlay-container">
		<div class="overlay">
			<div class="overlay-panel overlay-right">
				<h1>Step 2</h1>
				<p>Upload the file you generated in <i>Step 1</i></p>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="file" name="file">
                    <input class="button-upload" type="submit" name="upload_button" value="Upload">
                </form>
			</div>
		</div>
	</div>
</div>

<?php
    require 'vendor/autoload.php';

    use League\Csv\Reader;

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
   

    if (isset($_POST['upload_button'])) {
        if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {       
            try {
                $file_ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($file_ext == 'csv') {
                    $folder = 'uploads';
                    if (!is_dir($folder)) {
                        mkdir($folder);
                    }
            
                    $temp_file = $_FILES["file"]["tmp_name"];
                    $target_file = "uploads/" . $_FILES["file"]["name"];
                    move_uploaded_file($temp_file, $target_file);
            
                    // Connect to SQLite database using PDO
                    $pdo = new PDO('sqlite:./cyborgs_registry.sqlite');
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
                    // Create the table if it doesn't exist
                    $pdo->exec("CREATE TABLE IF NOT EXISTS cyborgs (
                        id INTEGER PRIMARY KEY,
                        name TEXT NOT NULL,
                        initials TEXT NOT NULL,
                        surname TEXT NOT NULL,
                        age TEXT NOT NULL,
                        date_of_birth DATE NOT NULL
                    )");
            
                    // Delete existing records
                    $pdo->beginTransaction();
                    try {
                        $pdo->exec("DELETE FROM cyborgs");

                        // Commit the transaction
                        $pdo->commit();
                    } catch (Exception $e) {
                        // Rollback the transaction
                        $pdo->rollback();
                        echo "An error occurred: " . $e->getMessage();
                        exit();
                    }

            
                    // Open the CSV file for reading
                    $file = fopen($target_file, 'r');
                    $firstLine = true;

                    // Open the CSV file for reading using the league/csv library
                    $csv = Reader::createFromPath($target_file, 'r');
                    $csv->setHeaderOffset(0); // Assumes the first row is the header
                    $rowCount = 0;

                    // Prepare the SQL statement to insert data
                    $pdo->beginTransaction();
                    try {
                        $stmt = $pdo->prepare("INSERT INTO cyborgs (id, name, initials, surname, age, date_of_birth) VALUES (?, ?, ?, ?, ?, ?)");

                        // Bind parameters
                        $stmt->bindParam(1, $id);
                        $stmt->bindParam(2, $name);
                        $stmt->bindParam(3, $initials);
                        $stmt->bindParam(4, $surname);
                        $stmt->bindParam(5, $age);
                        $stmt->bindParam(6, $date_of_birth);
                   
                        // Read the CSV file row by row and insert data into the database in batches
                        $batchSize = 1000; // Number of rows to insert in each batch
                        $batch = array(); // Array to accumulate rows
                        foreach ($csv as $row) {
                            $id = $row['Id'];
                            $name = $row['Name'];
                            $initials = $row['Initials'];
                            $surname = $row['Surname'];
                            $age = $row['Age'];
                            $date_of_birth = $row['DateOfBirth'];

                            $batch[] = array($id, $name, $initials, $surname, $age, $date_of_birth);
                            if (count($batch) >= $batchSize) {
                                // Execute the prepared statement with the batch
                                foreach ($batch as $row) {
                                    $stmt->execute($row);
                                }
                                $rowCount += count($batch);
                                $batch = array();
                            }
                            $rowCount++;
                        }

                        // Commit the transaction
                        $pdo->commit();


                    } catch (Exception $e) {
                        // Rollback the transaction
                        $pdo->rollback();
                        echo "An error occurred: " . $e->getMessage();
                        exit();
                    }
                    
                    // Close the CSV file
                    fclose($file);
            
                    // Close the database connection
                    $pdo = null;
            
                    echo $rowCount . ' records uploaded to database.', '<br>';
                } else {
                    echo "Incorrect file type!";
                }
                echo "200 OK";
            } catch (Exception $e) {
                echo "An error occurred: " . $e->getMessage();
            }
        } else {
            echo "There was an error uploading the file!";
        }       
    }
?>


<style>
    * {
        box-sizing: border-box;
    }

    body {
        background: #f6f5f7;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        height: 100vh;
        margin: -20px 0 50px;
    }

    h1 {
        font-weight: bold;
        margin: 0;
    }

    h2 {
        text-align: center;
    }

    p {
        font-size: 14px;
        font-weight: 100;
        line-height: 20px;
        letter-spacing: 0.5px;
        margin: 20px 0 30px;
    }

    .button-generate {
        border-radius: 20px;
        border: 1px solid #FF4B2B;
        background-color: #FF4B2B;
        color: #FFFFFF;
        font-size: 12px;
        font-weight: bold;
        padding: 12px 45px;
        letter-spacing: 1px;
        text-transform: uppercase;
        transition: transform 80ms ease-in;
    }

    .button-upload {
        border-radius: 20px;
        border: 1px solid #FFFFFF;
        color: #FFFFFF;
        font-size: 12px;
        font-weight: bold;
        padding: 12px 45px;
        letter-spacing: 1px;
        text-transform: uppercase;
        transition: transform 80ms ease-in;
        background-color: transparent;
    }

    .form-generate {
        background-color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 0 50px;
        height: 100%;
        text-align: center;
    }

    input {
        background-color: #eee;
        border: none;
        padding: 12px 15px;
        margin: 8px 0;
        width: 100%;
        color: black;
    }

    .container {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 14px 28px rgba(0,0,0,0.25), 
                    0 10px 10px rgba(0,0,0,0.22);
        position: relative;
        overflow: hidden;
        width: 768px;
        max-width: 100%;
        min-height: 480px;
    }

    .form-container {
        position: absolute;
        top: 0;
        height: 100%;
        transition: all 0.6s ease-in-out;
    }

    .container-generate {
        left: 0;
        width: 50%;
        z-index: 2;
    }

    .container.right-panel-active .container-generate {
        transform: translateX(100%);
    }

    .overlay-container {
        position: absolute;
        top: 0;
        left: 50%;
        width: 50%;
        height: 100%;
        overflow: hidden;
        transition: transform 0.6s ease-in-out;
        z-index: 100;
    }

    .container.right-panel-active .overlay-container{
        transform: translateX(-100%);
    }

    .overlay {
        background: #FF416C;
        background: -webkit-linear-gradient(to right, #FF4B2B, #FF416C);
        background: linear-gradient(to right, #FF4B2B, #FF416C);
        background-repeat: no-repeat;
        background-size: cover;
        background-position: 0 0;
        color: #FFFFFF;
        position: relative;
        left: -100%;
        height: 100%;
        width: 200%;
        transform: translateX(0);
        transition: transform 0.6s ease-in-out;
    }

    .container.right-panel-active .overlay {
        transform: translateX(50%);
    }

    .overlay-panel {
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 0 40px;
        text-align: center;
        top: 0;
        height: 100%;
        width: 50%;
        transform: translateX(0);
        transition: transform 0.6s ease-in-out;
    }

    .overlay-left {
        transform: translateX(-20%);
    }

    .container.right-panel-active .overlay-left {
        transform: translateX(0);
    }

    .overlay-right {
        right: 0;
        transform: translateX(0);
    }

    .container.right-panel-active .overlay-right {
        transform: translateX(20%);
    }
</style>