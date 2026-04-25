<?php

namespace App\Domain\Settings\DTO;
use App\Domain\Agent\DTO\Validatable\Validate;
use App\Contracts\DTO\DtoInterface;
use App\Traits\DtoToArray;
class UpdateSettingsData extends Validate implements DtoInterface
{

    use DtoToArray;
    public function __construct(
        public readonly ?array $prechatform = null,
        public readonly ?array $postchatform = null,
        public readonly ?array $widget_customization = null,
        public readonly ?array $widget_language = null,

    ) {
        $this->validate();
    }



    public function validate(): void
    {




    }


}