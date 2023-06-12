<?php
namespace Lubed\MVCKernel;
use Lubed\Data\ResultSet;
use Lubed\Utils\Registry;
/**
 *
 */
abstract class AbstractModel implements Model {
    /**
     * @var \Lubed\Data\Connection $conn
     */
    private $conn;

    /**
     * @param $conn
     */
    public function __construct() {
        $registry = Registry::getInstance();
        $this->conn=$registry->get('conn');
    }

    protected function execute(string $sql,array $params=[]):ResultSet
    {
        return $this->conn->execute([$sql,$params]);
    }
}