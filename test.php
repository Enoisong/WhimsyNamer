<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhimsyNamer</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php
$cn = pg_connect("host=localhost port=5432 dbname=WhimsyNamer user=postgres password=admins");

if (!$cn) {
    die("Connection failed: " . pg_last_error());
}

// Fetch names, registration_no, and year from the 'users' table
$query = "SELECT id, sname, registration_no, year FROM public.users LIMIT 10";
$result = pg_query($cn, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . pg_last_error($cn));
}

// Fetch the data into an associative array
$data = pg_fetch_all($result);

// Check if fetching was successful
if ($data === false) {
    die("Fetching data failed: " . pg_last_error($cn));
}

// Sort the data based on 'sname' column alphabetically
usort($data, function($a, $b) {
    return strcmp($a['sname'], $b['sname']);
});

// Assign new numerical order to 'id' column
foreach ($data as $key => $row) {
    $data[$key]['id'] = $key + 1;
}

// Display the table name
echo "<h1 style='text-align: center;'>WhimsyNamer</h1>";

// Display the sorted names, registration_no, and year in a table with form inputs
echo "<form method='post' action='{$_SERVER['PHP_SELF']}'>
<table class='center-table' border='1'>
    <tr>
        <th style='text-align: center;'>ID</th>
        <th style='text-align: center;'>Sname</th>
        <th class='Registration_No' style='text-align: center;'>Registration No.</th>
        <th class='Year' style='text-align: center;'>Year</th>
    </tr>";

foreach ($data as $row) {
    echo "<tr>
            <td style='text-align: center;'>{$row['id']}</td>
            <td style='text-align: left;'>{$row['sname']}</td>
            <td style='text-align: center;'><input type='text' name='registration_no[{$row['id']}]' required></td>
            <td style='text-align: center;'><input type='text' name='year[{$row['id']}]' required></td>
          </tr>";
}

// Add a new table row for the submit button
echo "<tr>
        <td colspan='4' style='text-align:center;'>
            <input type='submit' value='Submit'>
        </td>
      </tr>";

echo "</table>
      </form>";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['registration_no'] as $id => $registrationNo) {
        $year = $_POST['year'][$id];

        // Update the 'users' table with the provided registration_no and year
        $updateQuery = "UPDATE public.users SET registration_no='$registrationNo', year='$year' WHERE id=$id";
        $updateResult = pg_query($cn, $updateQuery);

        if (!$updateResult) {
            die("Update failed: " . pg_last_error($cn));
        }
    }

    // Redirect to prevent form resubmission on refresh
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Close the database connection
pg_close($cn);
?>
</body>
</html>
