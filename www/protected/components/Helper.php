<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 15-05-2015
 * Time: 00:27
 */

class Helper {

    /**
     * Gets a shade of an Hexadecimal base color based on a given spread
     * @param $hexBaseColor
     * @param int $spread
     * @return string
     */
    static public function generateRandomShade($hexBaseColor, $spread = 100) {
        $baseColor = str_replace('#', '', $hexBaseColor);
        if (strlen($baseColor) == 3) {
            $color = str_split($baseColor, 1);
        } elseif (strlen($baseColor) == 6) {
            $color = str_split($baseColor, 2);
        } else {
            return '#000000';
        }
        $r = rand(hexdec($color[0])-$spread, hexdec($color[0])+$spread);
        $g = rand(hexdec($color[1])-$spread, hexdec($color[1])+$spread);
        $b = rand(hexdec($color[2])-$spread, hexdec($color[2])+$spread);

        $r = ($r < 0 ? 0: $r);
        $r = ($r > 255 ? 255 : $r);
        $g = ($g < 0 ? 0: $g);
        $g = ($g > 255 ? 255 : $g);
        $b = ($b < 0 ? 0: $b);
        $b = ($b > 255 ? 255 : $b);

        return '#' . sprintf('%02x', $r) . sprintf('%02x', $g) . sprintf('%02x', $b);
    }
} 