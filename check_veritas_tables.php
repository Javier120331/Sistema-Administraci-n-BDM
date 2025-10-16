<?php
echo "Buscando tablas con nombres similares a 'trabajador' o 'empleado'...\n\n";

try {
    $pdo = new PDO(
        'mysql:host=host.docker.internal;port=3306',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    $stmt = $pdo->query("
        SELECT TABLE_SCHEMA, TABLE_NAME 
        FROM information_schema.TABLES 
        WHERE TABLE_SCHEMA NOT IN ('information_schema', 'mysql', 'performance_schema', 'phpmyadmin', 'sys')
        AND (
            TABLE_NAME LIKE '%trabajad%' 
            OR TABLE_NAME LIKE '%emplead%'
            OR TABLE_NAME LIKE '%worker%'
            OR TABLE_NAME LIKE '%employee%'
            OR TABLE_NAME LIKE '%cosechad%'
            OR TABLE_NAME LIKE '%operador%'
        )
        ORDER BY TABLE_SCHEMA, TABLE_NAME
    ");
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($results) > 0) {
        echo "Tablas encontradas:\n";
        foreach ($results as $row) {
            echo "  - " . $row['TABLE_SCHEMA'] . "." . $row['TABLE_NAME'] . "\n";
        }
    } else {
        echo "✗ No se encontraron tablas relacionadas.\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}


