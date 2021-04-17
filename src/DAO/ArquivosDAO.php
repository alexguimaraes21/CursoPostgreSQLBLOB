<?php


namespace App\DAO;


class ArquivosDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Conexao::get()->connect();
    }

    /**
     * @param $nomeArquivo
     * @param $mimeType
     * @param $caminhoArquivo
     * @return int
     * @throws \Exception
     */
    public function insert($nomeArquivo, $mimeType, $caminhoArquivo) : int
    {
        if (!file_exists($caminhoArquivo)) {
            throw new \Exception("File %s not found.");
        }

        $sql = "INSERT INTO tb_arquivos(mimetype,nome,arquivo) "
            . "VALUES(:mime_type,:file_name,:file_data)";

        try {
            $this->pdo->beginTransaction();

            // criando o BLOB
            $fileData = $this->pdo->pgsqlLOBCreate();
            $stream = $this->pdo->pgsqlLOBOpen($fileData, 'w');

            // lendo o conteúdo binário do arquivo e escrevendo no stream
            $fh = fopen($caminhoArquivo, 'rb');
            stream_copy_to_stream($fh, $stream);
            //
            $fh = null;
            $stream = null;

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':mime_type' => $mimeType,
                ':file_name' => $nomeArquivo,
                ':file_data' => $fileData,
            ]);

            // realizando COMMIT da TRANSACTION
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return $this->pdo->lastInsertId('tb_arquivos_id_seq');
    }

    /**
     * @param $id
     */
    public function read($id) : void
    {

        $this->pdo->beginTransaction();

        $stmt = $this->pdo->prepare("SELECT id, arquivo, mimetype "
            . "FROM tb_arquivos "
            . "WHERE id= :id");

        // buscando o BLOB no banco de dados
        $stmt->execute([$id]);

        $stmt->bindColumn('arquivo', $fileData, \PDO::PARAM_STR);
        $stmt->bindColumn('mimetype', $mimeType, \PDO::PARAM_STR);
        $stmt->fetch(\PDO::FETCH_BOUND);
        $stream = $this->pdo->pgsqlLOBOpen($fileData, 'r');

        // exibindo na tela
        header("Content-type: " . $mimeType);
        fpassthru($stream);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function delete($id) : void
    {
        try {
            $this->pdo->beginTransaction();
            // select the file data from the database
            $stmt = $this->pdo->prepare('SELECT arquivo '
                . 'FROM tb_arquivos '
                . 'WHERE id=:id');
            $stmt->execute([$id]);
            $stmt->bindColumn('file_data', $fileData, \PDO::PARAM_STR);
            $stmt->closeCursor();

            // removendo o BLOB
            $this->pdo->pgsqlLOBUnlink($fileData);
            $stmt = $this->pdo->prepare("DELETE FROM tb_arquivos WHERE id = :id");
            $stmt->execute([$id]);

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}