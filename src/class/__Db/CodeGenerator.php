<?php
namespace salesteck\Db;
/**
 * Created by PhpStorm.
 * User: SON
 * Date: 18/02/2018
 * Time: 21:16
 */
class CodeGenerator
{


    const
        LETTER = "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
        LETTER_LOWER = "abcdefghijklmnopqrstuvwxyz",
        NUMBER = "0123456789",
        CHARACTER = self::NUMBER.self::LETTER,
        CHARACTER_FULL = self::CHARACTER.self::LETTER_LOWER,
        CODE_LENGTH = 6
    ;

    public static function generateCode(int $codeLength = self::CODE_LENGTH, string $codeCharacter = self::CHARACTER)
    {
        $charactersLength = strlen($codeCharacter);
        $randomString = '';
        for ($i = 0; $i < $codeLength; $i++) {
            $randomString .= $codeCharacter[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}