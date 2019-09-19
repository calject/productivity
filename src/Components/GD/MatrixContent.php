<?php
/**
 * Created by PhpStorm.
 * User: lirongyi@51ucar.cn
 * Date: 2019/2/28
 * Annotation:
 */

namespace CalJect\Productivity\Components\GD;


class MatrixContent
{
    
    /**
     * @var int
     */
    protected $x;
    
    /**
     * @var int
     */
    protected $y;
    
    /**
     * @var string
     */
    protected $content;
    
    /**
     * MatrixContent constructor.
     * @param int $x
     * @param int $y
     * @param string $content
     */
    public function __construct(int $x, int $y, string $content)
    {
        $this->x = $x;
        $this->y = $y;
        $this->content = $content;
    }
    
    /**
     * @param int $x
     * @param int $y
     * @param string $content
     * @return static
     */
    public static function make(int $x, int $y, string $content)
    {
        return new static($x, $y, $content);
    }
    
    /*---------------------------------------------- set ----------------------------------------------*/
    
    /**
     * @param int $x
     * @return $this
     */
    public function setX(int $x)
    {
        $this->x = $x;
        return $this;
    }
    
    /**
     * @param int $y
     * @return $this
     */
    public function setY(int $y)
    {
        $this->y = $y;
        return $this;
    }
    
    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }
    
    /*---------------------------------------------- get ----------------------------------------------*/
    
    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }
    
    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }
    
    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
    
}