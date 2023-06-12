<?php
namespace Lubed\MVCKernel;

interface Model {
    public function deleteBy(array $where);
    public function findAll();
    public function findBy(array $where,string $order_by='',int $page=1, int $page_size=20);

    public function findOne();
    //保存数据
    public function save(array $data);
}

