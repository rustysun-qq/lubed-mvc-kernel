<?php
namespace Lubed\MVCKernel;
/**
 *
 */
abstract class AbstractModel implements Model {
    protected $conn;

    /**
     * @param $conn
     */
    public function __construct($conn) {
        $this->conn=$conn;
    }
}