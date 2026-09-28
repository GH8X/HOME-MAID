<?php
/**
 * Maid4Condos — MySQL connection factory (PDO, prepared statements only).
 */

/**
 * Returns a shared PDO instance, or null when the database is not configured
 * or unreachable. The public site degrades gracefully to its file-based
 * content when null is returned.
 */
function db()
{
    static $pdo = null;
    static $attempted = false;

    if ($attempted) {
        return $pdo;
    }
    $attempted = true;

    $cfg = config('db');
    if (empty($cfg['enabled'])) {
        return null;
    }

    $dsn = sprintf(
        '%s:host=%s;port=%d;dbname=%s;charset=%s',
        $cfg['driver'],
        $cfg['host'],
        (int) $cfg['port'],
        $cfg['name'],
        $cfg['charset']
    );

    try {
        $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_STRINGIFY_FETCHES  => false,
        ]);
    } catch (PDOException $e) {
        $pdo = null;
        if (config('app.debug')) {
            error_log('[Maid4Condos] Database connection failed: ' . $e->getMessage());
        }
    }

    return $pdo;
}

/** True when a usable database connection exists. */
function db_available()
{
    return db() instanceof PDO;
}

/** Prefix a logical table name with the configured DB prefix. */
function table($name)
{
    return config('db.prefix', '') . $name;
}

/** Run a prepared statement and return the statement handle. */
function db_run($sql, array $params = [])
{
    $pdo = db();
    if (!$pdo) {
        return null;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt;
}

/** Fetch every row for a prepared query. */
function db_all($sql, array $params = [])
{
    $stmt = db_run($sql, $params);

    return $stmt ? $stmt->fetchAll() : [];
}

/** Fetch a single row (or null). */
function db_one($sql, array $params = [])
{
    $stmt = db_run($sql, $params);

    return $stmt ? ($stmt->fetch() ?: null) : null;
}

/** Fetch a single scalar value. */
function db_value($sql, array $params = [], $default = null)
{
    $stmt = db_run($sql, $params);
    if (!$stmt) {
        return $default;
    }
    $value = $stmt->fetchColumn();

    return $value === false ? $default : $value;
}

/** Insert a row from an associative array and return the new id. */
function db_insert($table, array $data)
{
    $pdo = db();
    if (!$pdo || empty($data)) {
        return 0;
    }
    $columns = array_keys($data);
    $quoted  = array_map(function ($column) {
        return '`' . str_replace('`', '', $column) . '`';
    }, $columns);

    $sql = sprintf(
        'INSERT INTO `%s` (%s) VALUES (%s)',
        str_replace('`', '', $table),
        implode(', ', $quoted),
        implode(', ', array_fill(0, count($columns), '?'))
    );

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($data));

    return (int) $pdo->lastInsertId();
}

/** Update rows matching $where. Returns affected row count. */
function db_update($table, array $data, $where, array $whereParams = [])
{
    $pdo = db();
    if (!$pdo || empty($data)) {
        return 0;
    }
    $sets = [];
    foreach (array_keys($data) as $column) {
        $sets[] = '`' . str_replace('`', '', $column) . '` = ?';
    }
    $sql = sprintf(
        'UPDATE `%s` SET %s WHERE %s',
        str_replace('`', '', $table),
        implode(', ', $sets),
        $where
    );

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge(array_values($data), $whereParams));

    return $stmt->rowCount();
}

/** Delete rows matching $where. Returns affected row count. */
function db_delete($table, $where, array $params = [])
{
    $pdo = db();
    if (!$pdo) {
        return 0;
    }
    $sql  = sprintf('DELETE FROM `%s` WHERE %s', str_replace('`', '', $table), $where);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->rowCount();
}
