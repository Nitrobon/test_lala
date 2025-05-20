<?php

namespace App\Enum;

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';

    public function label(): string
    {
        return match ($this) {
            self::TODO => 'К выполнению',
            self::IN_PROGRESS => 'В процессе',
            self::DONE => 'Выполнено',
        };
    }
}
