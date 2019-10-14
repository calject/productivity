<?php
/**
 * Author: 沧澜
 * Date: 2019/2/28
 */

namespace CalJect\Productivity\Components\GD;


class WriteText
{
    /**
     * 字体
     * @var string
     */
    protected $font;
    
    /**
     * 文字内容
     * @var string
     */
    protected $content;
    
    /**
     * 字体大小
     * @var int
     */
    protected $size = 20;
    
    /**
     * 文字颜色
     * @var WriteColor
     */
    protected $color;
    
    /**
     * x坐标偏移量
     * @var int
     */
    protected $x;
    
    /**
     * y坐标偏移量
     * @var int
     */
    protected $y;
    
    /**
     * 文字旋转角度(默认0)
     * @var int
     */
    protected $circle = 0;
    
    /**
     * @param MatrixContent $matrixContent
     * @return $this
     */
    public function setByMatrixContent(MatrixContent $matrixContent)
    {
        $this->setX($matrixContent->getX());
        $this->setY($matrixContent->getY());
        $this->setContent($matrixContent->getContent());
        return $this;
    }
    
    /*---------------------------------------------- set ----------------------------------------------*/
    
    /**
     * @param string $font
     * @return $this
     */
    public function setFont(string $font)
    {
        $this->font = $font;
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
    
    /**
     * @param int $size
     * @return $this
     */
    public function setSize(int $size)
    {
        $this->size = $size;
        return $this;
    }
    
    /**
     * @param WriteColor $color
     * @return $this
     */
    public function setColor(WriteColor $color)
    {
        $this->color = $color;
        return $this;
    }
    
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
     * @param int $circle
     * @return $this
     */
    public function setCircle(int $circle)
    {
        $this->circle = $circle;
        return $this;
    }
    
    /*---------------------------------------------- get ----------------------------------------------*/
    
    /**
     * @return WriteColor
     */
    public function getColor(): WriteColor
    {
        if (!$this->color) {
            $this->color = new WriteColor();
        }
        return $this->color;
    }
    
    /**
     * @return string
     */
    public function getFont(): string
    {
        return $this->font;
    }
    
    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
    
    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }
    
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
     * @return int
     */
    public function getCircle(): int
    {
        return $this->circle;
    }
    
}