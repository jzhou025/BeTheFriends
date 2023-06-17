<?php
$host = 'localhost';      // Change as necessary
$data = 'bethefriends';   // Change as necessary
$user = 'bethefriends';   // Change as necessary
$pass = 'password';       // Change as necessary
$chrs = 'utf8mb4';
$attr = "mysql:host=$host;dbname=$data;charset=$chrs";
$opts =
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

try
{
    $pdo = new PDO($attr, $user, $pass, $opts);
}
catch (PDOException $e)
{
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

//Checks whether a table already exists and, if not, creates it
function createTable($name, $query)
{
    queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
    echo "Table '$name' created or already exists.<br>";
}

//Issues a query to MySQL, outputting an error message if it fails
function queryMysql($query)
{
    global $pdo;
    return $pdo->query($query);
}

//Destroys a PHP session and clears its data to log users out
function destroySession()
{
    $_SESSION=array();

    if (session_id() != "" || isset($_COOKIE[session_name()]))
        setcookie(session_name(), '', time()-2592000, '/');

    session_destroy();
}

//Removes potentially malicious code or tags from user input
function sanitizeString($var)
{
    global $pdo;

    $var = strip_tags($var);
    $var = htmlentities($var);

    if (get_magic_quotes_gpc())
        $var = stripslashes($var);

    $result = $pdo->quote($var);          // This adds single quotes
    return str_replace("'", "", $result); // So now remove them
}

//Displays the user’s image and “about me” message if they have one
function showProfile($user)
{
    global $pdo;

    if (file_exists("$user.jpg"))
        echo "<img src='$user.jpg' style='float:left;'>";

    $result = $pdo->query("SELECT * FROM profiles WHERE user='$user'");

    while ($row = $result->fetch())
    {
        die(stripslashes($row['text']) . "<br style='clear:left;'><br>");
    }

    echo "<p>Nothing to see here, yet</p><br>";
}

