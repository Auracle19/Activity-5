<?php
session_start();

if (isset($_POST['delete_name'])) {
    $deleteName = trim(filter_input(INPUT_POST, 'delete_name'));

    if (isset($_SESSION['characters'])) {
        foreach ($_SESSION['characters'] as $key => $character) {
            if (strcasecmp($character['name'], $deleteName) === 0) {
                unset($_SESSION['characters'][$key]);

                $_SESSION['characters'] = array_values($_SESSION['characters']);
                echo "Character '$deleteName' deleted successfully.";
                break;
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete_name'])) {

    $name = trim(filter_input(INPUT_POST, 'name'));
    $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
    $birthdate = filter_input(INPUT_POST, 'birthdate');

    if (empty($name) || !preg_match("/^[A-Za-z\s]+$/", $name)) {
        die("Invalid name. Only letters and spaces are allowed.");
    }
    
    if ($age < 1 || $age > 120) {
        die("Invalid age. Please enter an age between 1 and 120.");
    }

    $_SESSION['characters'][] = [
        'name' => $name,
        'age' => $age,
    ];
}

if (isset($_GET['query'])) {
    $suggestions = [];
    $query = filter_input(INPUT_GET, 'query');

    if (isset($_SESSION['characters'])) {
        foreach ($_SESSION['characters'] as $character) {
            if (stripos($character['name'], $query) === 0) {
                $suggestions[] = $character['name'];
            }
        }
    }

    echo json_encode($suggestions);
    exit;
}

if (!empty($_SESSION['characters'])) {
    echo "<h1>Character Information</h1>";
    foreach ($_SESSION['characters'] as $character) {
        echo "<p>Name: " . htmlspecialchars($character['name']) . "</p>";
        echo "<p>Age: " . htmlspecialchars($character['age']) . "</p>";
        echo '<form method="POST" action="">
                <input type="hidden" name="delete_name" value="' . htmlspecialchars($character['name']) . '">
                <input type="submit" value="Delete Character">
              </form>';
        echo "<hr>";
    }
} else {
    echo "No character information available.";
}
?>
