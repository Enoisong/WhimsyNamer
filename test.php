
<?php
$cn = pg_connect("host=localhost port=5432 dbname=WhimsyNamer user=postgres password=admins");
 
if (!$cn) {
    die("Connection failed: " . pg_last_error());
}

// Fetch names from the 'users' table
$query = "SELECT id, sname FROM public.users LIMIT 10";
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

// Display the sorted names in a table
echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Sname</th>
        </tr>";

foreach ($data as $row) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['sname']}</td>
          </tr>";
}

echo "</table>";

// Close the database connection
pg_close($cn);
?>
