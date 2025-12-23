<?php
namespace App\Constants;

class Invite
{

    const INVITE_PENDING = 'P';
    const INVITE_COMPLETE = 'C';

    const INVITE_DECLINE = 'D';
    const INVITE_EXPIRED = 'E';


    public const STATUS_LABELS = [
        self::INVITE_PENDING => 'Pending',
        self::INVITE_COMPLETE => 'Completed',
        self::INVITE_DECLINE => 'Declined',
        self::INVITE_EXPIRED => 'Expired'
    ];



    public static function getRoleLabel($status)
    {
        return self::STATUS_LABELS[$status] ?? "Unknow";
    }
}