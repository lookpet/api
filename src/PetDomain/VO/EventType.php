<?php

namespace App\PetDomain\VO;

final class EventType
{
    public const REGISTRATION = 'REGISTRATION';
    public const LOGIN = 'LOGIN';
    public const FACEBOOK_REGISTRATION = 'FACEBOOK_REGISTRATION';
    public const FACEBOOK_LOGIN = 'FACEBOOK_LOGIN';
    public const RESET_PASSWORD = 'RESET_PASSWORD';
    public const UPLOAD_PHOTO = 'UPLOAD_PHOTO';
    public const CROP_PHOTO = 'CROP_PHOTO';
    public const DELETE_PHOTO = 'DELETE_PHOTO';
    public const PET_CREATE = 'PET_CREATE';
    public const PET_LIKE = 'PET_LIKE';
    public const PET_UNLIKE = 'PET_UNLIKE';
    public const PET_COMMENT = 'PET_COMMENT';

    private const EVENT_LIST = [
        self::REGISTRATION,
        self::LOGIN,
        self::FACEBOOK_REGISTRATION,
        self::FACEBOOK_LOGIN,
        self::RESET_PASSWORD,
        self::UPLOAD_PHOTO,
        self::CROP_PHOTO,
        self::DELETE_PHOTO,
        self::PET_LIKE,
        self::PET_UNLIKE,
        self::PET_CREATE,
        self::PET_COMMENT,
    ];

    private string $eventType;

    public function __construct(string $eventType)
    {
        $this->validate($eventType);
        $this->eventType = $eventType;
    }

    public function __toString(): string
    {
        return $this->eventType;
    }

    private function validate(string $eventType): void
    {
        if (!in_array($eventType, self::EVENT_LIST)) {
            throw new \LogicException('Event type is not define');
        }
    }
}
