<?php
/**
 * Created by PhpStorm.
 * User: lirongyi@51ucar.cn
 * Date: 2019/2/28
 * Annotation:
 */

namespace CalJect\Productivity\Component\GD;


class WriteColor
{
    /**
     * 三原色-红
     * @var int
     */
    protected $red = 0;
    
    /**
     * 三原色绿
     * @var int
     */
    protected $green = 0;
    
    /**
     * 三原色-蓝
     * @var int
     */
    protected $blue = 0;
    
    /**
     * 十六进制颜色
     * @var string
     */
    protected $color = '#000000';
    
    
    /*---------------------------------------------- function ----------------------------------------------*/
    
    /**
     * make by color
     * @param string $color
     * @return static
     */
    public static function makeByColor(string $color)
    {
        $instance = new static();
        list($instance->red, $instance->green, $instance->blue) = $instance->colorToRgb(substr($color, 1));
        $instance->setColor($color);
        return $instance;
    }
    
    /**
     * make by rgb color
     * @param int $red
     * @param int $green
     * @param int $blue
     * @return static
     */
    public static function makeByRgb(int $red, int $green, int $blue)
    {
        $instance = new static();
        $instance->setRed($red);
        $instance->setGreen($green);
        $instance->setBlue($blue);
        $instance->setColor($instance->rgbToColor($red, $green, $blue));
        return $instance;
    }
    
    
    /**
     * 十六进制颜色转为rgb
     * @param string $color
     * @return array
     */
    public function colorToRgb(string $color): array
    {
        $colors = str_split($color, 2);
        foreach ($colors as $color) {
            $arr[] = hexdec($color);
        }
        return $arr ?? [];
    }
    
    /**
     * rgb转16进制颜色
     * @param int $red
     * @param int $green
     * @param int $blue
     * @return string
     */
    public function rgbToColor(int $red, int $green, int $blue)
    {
        $to_hex = function ($color) {
            return sprintf("%02s", dechex($color));
        };
        return '#' . $to_hex($red) . $to_hex($green) . $to_hex($blue);
    }
    
    
    /*---------------------------------------------- set ----------------------------------------------*/
    
    /**
     * @param int $red
     * @return $this
     */
    public function setRed(int $red)
    {
        $this->red = $red;
        return $this;
    }
    
    /**
     * @param int $blue
     * @return $this
     */
    public function setBlue(int $blue)
    {
        $this->blue = $blue;
        return $this;
    }
    
    /**
     * @param int $green
     * @return $this
     */
    public function setGreen(int $green)
    {
        $this->green = $green;
        return $this;
    }
    
    /**
     * @param string $color
     * @return $this
     */
    public function setColor(string $color)
    {
        $this->color = $color;
        return $this;
    }
    
    /*---------------------------------------------- get ----------------------------------------------*/
    
    /**
     * @return int
     */
    public function getRed(): int
    {
        return $this->red;
    }
    
    /**
     * @return int
     */
    public function getBlue(): int
    {
        return $this->blue;
    }
    
    /**
     * @return int
     */
    public function getGreen(): int
    {
        return $this->green;
    }
    
    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }
    
}