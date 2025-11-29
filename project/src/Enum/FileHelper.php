<?php

namespace App\Enum;

enum FileHelper: string
{
    case PROMPT = 'prompt.json';
    case MANUAL = 'manual.md';
    case FILE_DIR = '/project/storage/telegram/';
    case PROMPT_FILE_PATH = self::FILE_DIR->value . self::PROMPT->value;
    case MANUAL_FILE_PATH = self::FILE_DIR->value . self::MANUAL->value;

}
