<?php

namespace Model;

class SanitizeInput
{
    public static function sanitize(string $string)
    {
        return strip_tags($string);
    }
}
