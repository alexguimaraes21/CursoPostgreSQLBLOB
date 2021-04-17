<?php


namespace App\DAO;


class Conexao
{
    /**
     * Connection
     * @var type
     */
    private static $conn;

    /**
     * Conecta no database e retorna a instancia do objeto \PDO
     */
    public function connect()
    {
        $params = parse_ini_file(APPLICATION_PATH . '/config/database.ini');
        if ($params === false) {
            throw new \Exception("Erro ao tentar abrir o arquivo de configuracao!");
        }
        // Conecta no Banco de Dados PostgreSQL
        $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $params['host'],
            $params['port'],
            $params['database'],
            $params['user'],
            $params['password']);

        $pdo = new \PDO($conStr);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    public static function get()
    {
        if(null === static::$conn) {
            static::$conn = new static();
        }

        return static::$conn;
    }
    protected function __construct() {}

    private function __clone() {}

    private function __wakeup() {}
}