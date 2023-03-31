<?php
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
    $cyborgQuantity = '';

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
                $quantityErr = 'Please enter a number from 1 to 1 000 000!';
            }
        }

        if (empty($quantityErr)) {
            create_csv_file($NAMES, $SURNAMES, $cyborgQuantity); //250 000
            echo 'Done';
        }
    }

    function create_csv_file($namesArray, $surnamesArray, $cyborgQuantity) {
        $cyborgs = [];
        $id = 1;

        try {
            while (count($cyborgs) < $cyborgQuantity) {
                $id = $id;
                $givenName = get_name($namesArray);
                $initials = get_initials($givenName);
                $surname = get_surname($surnamesArray);
                $dob = get_dob();
                $age = get_age($dob);

                $cyborg = [
                    'id' => $id,
                    'firstname' => $givenName,
                    'surname' => $surname,
                    'initials' => $initials,
                    'age' => $age,
                    'dob' => $dob
                ];

                $cyborgKey = $cyborg['firstname'].'_'.$cyborg['surname'].'_'.$cyborg['dob'];

                if (!isset($cyborgs[$cyborgKey])) {
                    $cyborgs[$cyborgKey] = $cyborg;
                    $id++;
                }
                
            }
   
            $folder = 'output';
            if (!is_dir($folder)) {
                mkdir($folder);
            }
        
            $fp = fopen('output/output.csv', 'w'); 
            $header = array('Id', 'Name', 'Surname', 'Initials', 'Age', 'DateOfBirth');
            fputcsv($fp, $header);

            foreach ($cyborgs as $cyborg) {
                $row = $cyborg;
                fputcsv($fp, $row);
            }
            fclose($fp);
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getMessage();
        }

    }

    function get_name($namesArray) {
        $firstnameKeys = array_rand($namesArray, rand(1, 3));
        if (is_array($firstnameKeys)) {
            $firstname_s = array();
            foreach ($firstnameKeys as $firstnameKey) {
                $firstname_s[] = $namesArray[$firstnameKey];
                return implode(' ', $firstname_s).'';
            }
        } else {
            return $namesArray[$firstnameKeys].'';
        }
    }

    function get_initials($givenName) {
        $names = explode(" ", $givenName);
        $firstLetters = array();
        foreach ($names as $name) {
            $firstLetters[] = ucfirst((substr($name, 0, 1)));
        }
        return implode("", $firstLetters).'';
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
        $years = $age->y;
        $months = $age->m;
        if ($years < 1) {
            return $months.' months';
        } else {
            return $years.' years';
        }
    }

?>

<body>
    <h2>Colony Database</h2>

    <form action="" method="post">
        <fieldset>
            <label for="quantity">Number of Cyborgs:</label>
            <input type="number" id="quantity" name="quantity" min="1" max="1000000">
            <div>
                <input type="submit" name="create_button" value="Post">
            </div>
        </fieldset>
    </form>

</body>
