<?php

namespace Kobens\Core\Helper\Console\Command\StdOut;

class Format
{
    /**
     * @var array
     */
    protected static $fgColors = [
        'black'        => '0;30',
        'blue'         => '0;34',
        'brown'        => '0;33',
        'cyan'         => '0;36',
        'dark_gray'    => '1;30',
        'green'        => '0;32',
        'light_blue'   => '1;34',
        'light_cyan'   => '1;36',
        'light_gray'   => '0;37',
        'light_green'  => '1;32',
        'light_red'    => '1;31',
        'light_purple' => '1;35',
        'red'          => '0;31',
        'purple'       => '0;35',
        'yellow'       => '1;33',
        'white'        => '1;37',

        'normal'       => '0;39',
    ];

    /**
     * @var array
     */
    protected static $bgColors = [
        'black'      => '40',
        'blue'       => '44',
        'cyan'       => '46',
        'green'      => '42',
        'light_gray' => '47',
        'magenta'    => '45',
        'red'        => '41',
        'yellow'     => '43',
    ];

    protected static $options = [
        'underline' => '4',
        'blink'     => '5',
        'reverse'   => '7',
        'hidden'    => '8',
    ];

    /**
     * Returns colored string
     *
     * @param string $string
     * @param string $fgColor
     * @param string $bgColor
     * @return string
     */
    public static function formatString($string, array $args)
    {
        $format = "";
        if (isset($args['foreground']) && isset(self::$fgColors[$args['foreground']])) {
            $format.= "\033[" . self::$fgColors[$args['foreground']] . 'm';
        }
        if (isset($args['background']) && isset(self::$bgColors[$args['background']])) {
            $format .= "\033[" . self::$bgColors[$args['background']] . 'm';
        }
        foreach (array_keys(self::$options) as $option) {
            if (isset($args[$option]) && $args[$option]) {
                $format .= "\033[" . self::$options[$option] . 'm';
            }
        }
        return $format . $string . "\033[0m";
    }

    public static function red($string)
    {
        return self::formatString($string, ['foreground' => 'light_red']);
    }

    public static function green($string)
    {
        return self::formatString($string, ['foreground' => 'light_green']);
    }

    public static function underline($string)
    {
        return self::formatString($string, ['underline' => true]);
    }

    /**
     * Returns all foreground color names
     *
     * @return array
     */
    public static function getForegroundColors()
    {
        return array_keys(self::$fgColors);
    }

    /**
     * Returns all background color names
     *
     * @return array
     */
    public static function getBackgroundColors()
    {
        return array_keys(self::$bgColors);
    }
}
