<?php

require_once('../conf/ompinfo.php');
require_once('../../vendor/autoload.php');
require_once('../Util/BcryptHasher.php');


if(defined('HASHED_PASSWORDS') && HASHED_PASSWORDS == 1) {
    $conn->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    $sm = $conn->getSchemaManager();
    $columns = $sm->listTableColumns('omp_users');
    $passwordColumn = $columns['password'];
    $hashedPasswordColumn = $columns['hashed_password'];

    $sqlAlterTable = '';

    if($passwordColumn->getLength() != 100) {
        $sqlAlterTable .= 'ALTER TABLE omp_users MODIFY password VARCHAR (100) NOT NULL;';
        echo "\npassword column has been set to 100 characters\n";
    }

    if(empty($hashedPasswordColumn)) {
        $sqlAlterTable .= 'ALTER TABLE omp_users ADD hashed_password TINYINT (1) DEFAULT 0;';
        echo "\nhashed_password column added\n";
    }

    if($sqlAlterTable != '') {
        if (method_exists($conn, 'exec')) {
            $conn->exec($sqlAlterTable);
        } else {
            $conn->executeQuery($sqlAlterTable);
        }
    }

    $sql = 'SELECT * FROM omp_users';
    $prepare = $conn->prepare($sql);
    $prepare->execute();
    $users=$prepare->fetchAll();

    $hasher = new BcryptHasher();

    foreach($users as $user) {
        if($user['hashed_password'] == 0) {
            $hashed_password = $hasher->make($user['password']);
            $conn->update('omp_users', ['password' => $hashed_password, 'hashed_password' => 1], ['id' => $user['id']]);
        }
    }

    echo "\nDONE!\n";
} else {
    echo "HASHED_PASSWORDS FLAG IS NOT SET\n";
}

echo '-- Ending ' . __FILE__ . ' ' . date('d/m/Y H:i:s') . SEP . "\n\n";
