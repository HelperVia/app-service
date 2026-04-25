<?php
namespace App\Domain\Invite\Constants;

class InviteStatus
{

    const INVITE_PENDING = 'P';
    const INVITE_COMPLETE = 'C';

    const INVITE_DECLINE = 'D';
    const INVITE_EXPIRED = 'E';
    const INVITE_CANCELED = 'X';

    public const STATUS_LABELS = [
        self::INVITE_PENDING => 'Pending',
        self::INVITE_COMPLETE => 'Completed',
        self::INVITE_DECLINE => 'Declined',
        self::INVITE_EXPIRED => 'Expired',
        self::INVITE_CANCELED => 'Canceled'
    ];



    public static function getRoleLabel($status)
    {
        return self::STATUS_LABELS[$status] ?? "Unknow";
    }
}