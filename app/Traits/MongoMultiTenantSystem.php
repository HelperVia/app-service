<?php

namespace App\Traits;

trait MongoMultiTenantSystem
{


    public static function tenant($db = '')
    {
        $model = new static();

        if (!empty($db)) {
            $connection = $model->getConnection();
            $client = $connection->getMongoClient();
            $database = $client->selectDatabase($db);
            $connection->setDatabase($database);


        }




        return $model;

    }
}
