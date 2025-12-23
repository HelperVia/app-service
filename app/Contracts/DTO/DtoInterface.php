<?php


namespace App\Contracts\DTO;

interface DtoInterface
{
    /**
     * Validate the DTO data
     * 
     * @throws \InvalidArgumentException
     */
    public function validate(): void;
}