<?php

namespace App\Traits;

trait DtoToArray
{


    public function toArray(): array
    {
        $data = [];

        foreach (get_object_vars($this) as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
