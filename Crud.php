<?php

class Crud
{
  /**
   * @var \PDO
   */
  public static $connection;

  /**
   * @param \PDO $connection
   * @return void
   */
  public function __construct(\PDO $connection)
  {
    static::$connection = $connection;
  }

  /**
   * @param string $sql
   * @param array $parameters
   * @return \PDOStatement
   */
  public static function query($sql, array $parameters = [])
  {
    $sth = static::$connection->prepare($sql);
    $sth->execute($parameters);
    return $sth;
  }

  /**
   * @return \PDO
   */
  public static function pdo()
  {
    return static::$connection;
  }

  /**
   * @return int
   */
  public static function lastInsertId()
  {
    return (int) static::pdo()->lastInsertId();
  }

  /**
   * @param string $tableName
   * @param array $columnsAndValues
   * @param array $onDuplicateKeyUpdate
   * @return \PDOStatement
   */
  public static function insert(
    $tableName,
    array $columnsAndValues,
    array $onDuplicateKeyUpdate = []
  )
  {
    $parameters = [];
    $insertString = "INSERT INTO {$tableName} ";
    $insertString .= static::arrayToString($columnsAndValues, 'insert');
    $parameters = array_merge($parameters, array_values($columnsAndValues));

    if (!empty($onDuplicateKeyUpdate)) {
      $insertString .= " ON DUPLICATE KEY UPDATE ";
      $insertString .= static::arrayToString($onDuplicateKeyUpdate, 'update');
      $parameters = array_merge($parameters, array_values($onDuplicateKeyUpdate));
    }

    return static::query($insertString, $parameters);
  }

  /**
   * @param string $tableName
   * @param array $columnsAndValues
   * @param string|array $statements
   * @param array $parameters
   * @return \PDOStatement
   */
  public static function update(
    $tableName,
    array $columnsAndValues,
    $statements = '',
    array $parameters = []
  )
  {
    $updateString = "UPDATE {$tableName} SET ";
    $updateString .= static::arrayToString($columnsAndValues, 'update');
    $params = array_values($columnsAndValues);

    if (is_array($statements)) {
      $updateString .= ' ' . static::buildWhere($statements);
      $params = array_merge($params, array_values($statements));
    } else {
      $updateString .= ' ' . $statements;
      $params = array_merge($params, $parameters);
    }
    
    return static::query($updateString, $params);
  }

  /**
   * @param string $tableName
   * @param string|array $statements
   * @param array $parameters
   * @return \PDOStatement
   */
  public static function delete(
    $tableName,
    $statements = '',
    array $parameters = []
  )
  {
    if (is_array($statements)) {
      $where = static::buildWhere($statements);
      $deleteString = "DELETE FROM {$tableName} {$where}";
      $params = array_values($statements);
    } else {
      $deleteString = "DELETE FROM {$tableName} {$statements}";
      $params = $parameters;
    }
    
    return static::query($deleteString, $params);
  }

  /**
   * @param array $columnsAndValues
   * @param string $type
   * @return string
   */
  private static function arrayToString(array $columnsAndValues, $type)
  {
    $string = '';

    switch ($type) {
      case 'insert':
        $arrayParameters = array_values($columnsAndValues);
        $columnsString = implode(',', array_keys($columnsAndValues));
        $valuesString = implode(',', array_fill(0, count($arrayParameters), '?'));
        $string = "({$columnsString}) VALUES ({$valuesString})";
        break;

      case 'update':
        foreach ($columnsAndValues as $key => $value) {
          $string .= "{$key}=?,";
        }
        $string = rtrim($string, ',');
        break;
    }

    return $string;
  }

  /**
   * @param array $array
   * @return string
   */
  private static function buildWhere(array $array)
  {
    $whereString = 'WHERE ';

    foreach ($array as $key => $value) {
      $whereString .= "{$key}=? AND ";
    }

    return rtrim($whereString, ' AND ');
  }
}
