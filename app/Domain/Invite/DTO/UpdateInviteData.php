<?php

namespace App\Domain\Invite\DTO;

use App\Traits\DtoToArray;

class UpdateInviteData
{

    use DtoToArray;
    public function __construct(

        public ?string $inviting_company_id = null,
        public ?string $invited_email = null,
        public ?string $inviting_user = null,
        public ?string $invited_role = null,
        public ?string $temporary_name = null,
        public ?string $status = null,
        public ?string $invited_id = null,
        public ?string $invite_expire = null,
        public ?string $invite_code = null
    ) {

    }

}