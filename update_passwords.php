<?php
require_once 'db_config.php';

try {
    $res = load_config(dirname(__FILE__) . "/configuration.xml", dirname(__FILE__) . "/configuration.xsd");
    $db = new PDO($res[0], $res[1], $res[2]);

    $stmt = $db->prepare("SELECT user_id, user_password FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        $hashed_password = password_hash($user['user_password'], PASSWORD_DEFAULT);

        $updateStmt = $db->prepare("UPDATE users SET user_password = ? WHERE user_id = ?");
        $updateStmt->execute([$hashed_password, $user['user_id']]);

        echo "Password updated for user ID: " . $user['user_id'] . "<br>";
    }

    echo "All passwords have been successfully updated!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
