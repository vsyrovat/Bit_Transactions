<?php
namespace Framework\PDO;

class PDOStatement extends \PDOStatement
{
    private $paramTypes = [];

    protected function __construct()
    {
    }

    /**
     * Set types of parameters for following execute() method.
     * Argument $paramTypes should be hash array where keys are field names and values are corresponding bind types.
     * Supported types:
     *     PDO::PARAM_BOOL,
     *     PDO::PARAM_NULL,
     *     PDO::PARAM_INT,
     *     PDO::PARAM_STR,
     *     PDO::PARAM_LOB,
     *     PDO::PARAM_STMT,
     *     PDO::PARAM_INPUT_OUTPUT
     *
     * Usage example:
     * $statement = $pdo->prepare('SELECT * FROM `table` LIMIT :offset,:limit');
     * $statement->bindParamTypes([
     *     'offset' => \PDO::PARAM_INT,
     *     'limit' => \PDO::PARAM_INT,
     * ]);
     * $statement->execute([
     *     'offset' => 20,
     *     'limit' => 10,
     * ]);
     * $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
     *
     * @param array $paramTypes
     */
    public function bindParamTypes(array $paramTypes = [])
    {
        $this->paramTypes = $paramTypes;
    }

    public function execute($inputParameters = null)
    {
        if (is_array($inputParameters)) {
            foreach ($inputParameters as $paramName => $paramValue) {
                $type = isset($this->paramTypes[$paramName]) ? $this->paramTypes[$paramName] : \PDO::PARAM_STR;
                $this->bindValue(':' . $paramName, $paramValue, $type);
            }
        }

        return parent::execute();
    }
}
