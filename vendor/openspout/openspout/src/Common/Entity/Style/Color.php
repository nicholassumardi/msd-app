<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Style;

use OpenSpout\Common\Exception\InvalidColorException;

/**
 * This class provides constants and functions to work with colors.
 */
final class Color
{
    /**
     * Standard colors - based on Office Online.
     */
    public const BLACK = '000000';
    public const WHITE = 'FFFFFF';

    public const RED = 'FF0000';
    public const DARK_RED = '8B0000';
    public const LIGHT_RED = 'FFA07A';
    public const CRIMSON = 'DC143C';
    public const MAROON = '800000';

    public const ORANGE = 'FFA500';
    public const DARK_ORANGE = 'FF8C00';
    public const LIGHT_ORANGE = 'FFD580';

    public const YELLOW = 'FFFF00';
    public const GOLD = 'FFD700';
    public const LIGHT_YELLOW = 'FFFFE0';
    public const DARK_YELLOW = 'B59F00';

    public const LIGHT_GREEN = '90EE90';
    public const GREEN = '00B050';
    public const DARK_GREEN = '006400';
    public const LIME = '32CD32';
    public const OLIVE = '808000';
    public const TEAL = '008080';

    public const LIGHT_BLUE = 'ADD8E6';
    public const SKY_BLUE = '87CEEB';
    public const BLUE = '0070C0';
    public const DARK_BLUE = '002060';
    public const NAVY = '000080';
    public const CYAN = '00FFFF';
    public const TURQUOISE = '40E0D0';

    public const PURPLE = '7030A0';
    public const VIOLET = 'EE82EE';
    public const INDIGO = '4B0082';
    public const LAVENDER = 'E6E6FA';
    public const MAGENTA = 'FF00FF';

    public const PINK = 'FFC0CB';
    public const HOT_PINK = 'FF69B4';
    public const DEEP_PINK = 'FF1493';

    public const BROWN = 'A52A2A';
    public const SADDLE_BROWN = '8B4513';
    public const CHOCOLATE = 'D2691E';
    public const TAN = 'D2B48C';
    public const BEIGE = 'F5F5DC';

    public const GRAY = '808080';
    public const DARK_GRAY = 'A9A9A9';
    public const DIM_GRAY = '696969';
    public const LIGHT_GRAY = 'D3D3D3';
    public const SILVER = 'C0C0C0';
    public const GAINSBORO = 'DCDCDC';


    /**
     * Returns an RGB color from R, G and B values.
     *
     * @param int $red   Red component, 0 - 255
     * @param int $green Green component, 0 - 255
     * @param int $blue  Blue component, 0 - 255
     *
     * @return string RGB color
     */
    public static function rgb(int $red, int $green, int $blue): string
    {
        self::throwIfInvalidColorComponentValue($red);
        self::throwIfInvalidColorComponentValue($green);
        self::throwIfInvalidColorComponentValue($blue);

        return strtoupper(
            self::convertColorComponentToHex($red) .
                self::convertColorComponentToHex($green) .
                self::convertColorComponentToHex($blue)
        );
    }

    /**
     * Returns the ARGB color of the given RGB color,
     * assuming that alpha value is always 1.
     *
     * @param string $rgbColor RGB color like "FF08B2"
     *
     * @return string ARGB color
     */
    public static function toARGB(string $rgbColor): string
    {
        return 'FF' . $rgbColor;
    }

    /**
     * Throws an exception is the color component value is outside of bounds (0 - 255).
     *
     * @throws InvalidColorException
     */
    private static function throwIfInvalidColorComponentValue(int $colorComponent): void
    {
        if ($colorComponent < 0 || $colorComponent > 255) {
            throw new InvalidColorException("The RGB components must be between 0 and 255. Received: {$colorComponent}");
        }
    }

    /**
     * Converts the color component to its corresponding hexadecimal value.
     *
     * @param int $colorComponent Color component, 0 - 255
     *
     * @return string Corresponding hexadecimal value, with a leading 0 if needed. E.g "0f", "2d"
     */
    private static function convertColorComponentToHex(int $colorComponent): string
    {
        return str_pad(dechex($colorComponent), 2, '0', STR_PAD_LEFT);
    }
}
