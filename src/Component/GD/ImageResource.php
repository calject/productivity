<?php
/**
 * Created by PhpStorm.
 * User: lirongyi@51ucar.cn
 * Date: 2019/2/28
 * Annotation:
 */

namespace CalJect\Productivity\Component\GD;


class ImageResource
{
    
    /**
     * 图片路径
     * @var string
     */
    protected $image;
    
    /**
     * 图片宽度
     * @var int
     */
    protected $imageWidth;
    
    /**
     * 图片高度
     * @var int
     */
    protected $imageHigh;
    
    /**
     * 图片类型(图片扩展名)
     * @var string
     */
    protected $type;
    
    /**
     * 图片信息 [0]:width [1]:high [2]: type
     * @var array
     */
    protected $info;
    
    /**
     * 图片资源操作符
     * @var resource
     */
    protected $resource;
    
    /**
     * 输入
     * @var array
     */
    protected $inputList = [];
    
    /**
     * 图片内容
     * @var string
     */
    protected $imageContent = '';
    
    /**
     * ImageResource constructor.
     * @param string $image
     */
    public function __construct($image = null)
    {
        $this->image = $image;
        if (isset($image)) {
            $this->init();
        }
    }
    
    /**
     * 初始化参数
     */
    public function init()
    {
        $this->info = getimagesize($this->image);
        $this->type = image_type_to_extension($this->info[2], false);
        $this->imageWidth = $this->info[0];
        $this->imageHigh = $this->info[1];
        $func = "imagecreatefrom{$this->type}";
        $this->resource = function_exists($func) ? $func($this->image) : imagecreatefromstring($this->image);
    }
    
    /**
     * @param string $content
     * @param int $with
     * @param int $high
     * @param string $type
     * @return static
     */
    public static function makeByContent(string $content, int $with, int $high, string $type)
    {
        $instance = new static();
        $instance->resource = imagecreatefromstring($content);
        $instance->imageWidth = $with;
        $instance->imageHigh = $high;
        $instance->type = $type;
        return $instance;
    }
    
    /**
     * @return resource
     */
    public function resource()
    {
        return $this->resource;
    }
    /*---------------------------------------------- functionality ----------------------------------------------*/
    
    /**
     * @param int $width
     * @param int $high
     * @param null $filename 保存的图片路径,为空则返回新图片content
     * @return string
     */
    public function changeImageSize(int $width, int $high, $filename = null)
    {
        $image = imagecreatetruecolor($width, $high); //创建一个彩色的底图
        imagecopyresampled($image, $this->resource(), 0, 0, 0, 0, $width, $high, $this->imageWidth, $this->imageHigh);
        ob_start();
        imagepng($image, $filename);
        $content = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
        return $filename ? null : $content;
    }
    
    /**
     * 创建新的图片尺寸实例
     * @param int $width
     * @param int $high
     * @param null $filename
     * @return ImageResource|static
     */
    public function createNewImageSize(int $width, int $high, $filename = null)
    {
        $content = $this->changeImageSize($width, $high, $filename);
        if (isset($filename)) {
            return new static($filename);
        } else {
            return static::makeByContent($content, $width, $high, $this->getType());
        }
    }
    
    /**
     * 获取图片文本数据
     * @return string
     */
    public function get()
    {
        ob_start();
        ("image{$this->type}")($this->resource());
        $this->imageContent = ob_get_contents();
        ob_end_clean();
        return $this->imageContent;
    }
    
    /**
     * 保存输出到文件
     * @param string $file_path
     * @param int $quality
     * @param int $filters
     * @return bool
     */
    public function save(string $file_path, $quality = -1, $filters = -1)
    {
        return ("image{$this->type}")($this->resource(), $file_path, $quality, $filters);
    }
    
    /**
     * 直接输出显示图片
     */
    public function show()
    {
        header('Content-type: image/jpg');
        ("image{$this->type}")($this->resource());
    }
    
    
    /*---------------------------------------------- append ----------------------------------------------*/
    
    /**
     * 写入指定字体文本
     * @param WriteText $text
     * @return $this
     */
    public function appendTextFont(WriteText $text)
    {
        $color = $this->getColorAllocate($text->getColor());
        imagettftext($this->resource(), $text->getSize(), $text->getCircle(), $text->getX(), $text->getY(), $color, $text->getFont(), $text->getContent());
        return $this;
    }
    
    /**
     * 写入普通文本
     * @param WriteText $text
     * @return $this
     */
    public function appendText(WriteText $text)
    {
        $color = $this->getColorAllocate($text->getColor());
        imagestring($this->resource(), $text->getSize(), $text->getX(), $text->getY(), $text->getContent(), $color);
        return $this;
    }
    
    /**
     * 图片合并(合并的图片在上层)
     * @param ImageResource $image
     * @param int $x
     * @param int $y
     * @param int $high
     * @param $width
     * @return $this
     */
    public function appendImage(ImageResource $image, int $x, int $y, $high = 0, $width = 0)
    {
        imagecopymerge($this->resource(), $image->resource(), $x, $y, 0, 0, $width ?: $image->getImageWidth(), $high ?: $image->getImageHigh(), 100);
        return $this;
    }
    
    /**
     * 图片合成, 重新设置宽高
     * @param ImageResource $image
     * @param $x
     * @param $y
     * @param $high
     * @param $width
     * @return $this
     */
    public function appendImageResource(ImageResource $image, $x, $y, $high, $width)
    {
        $_image = imagecreatetruecolor($width, $high); //创建一个彩色的底图
        imagecopyresampled($_image, $image->resource(), 0, 0, 0, 0, $width, $high, $image->getImageWidth(), $image->getImageHigh());
        imagedestroy($_image);
        imagecopymerge($this->resource(), $_image, $x, $y, 0, 0, $width, $high, 100);
        return $this;
    }
    
    /**
     * 获取颜色参数
     * @param WriteColor $color
     * @return int
     */
    protected function getColorAllocate(WriteColor $color)
    {
        return imagecolorallocate($this->resource(), $color->getRed(), $color->getGreen(), $color->getBlue());
    }
    
    /*---------------------------------------------- get ----------------------------------------------*/
    
    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }
    
    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
    
    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info;
    }
    
    /**
     * @return resource
     */
    public function getResource(): resource
    {
        return $this->resource;
    }
    
    /**
     * @return array
     */
    public function getInputList(): array
    {
        return $this->inputList;
    }
    
    /**
     * @return int
     */
    public function getImageHigh(): int
    {
        return $this->imageHigh;
    }
    
    /**
     * @return int
     */
    public function getImageWidth(): int
    {
        return $this->imageWidth;
    }
    
    /**
     * @return string
     */
    public function getImageContent(): string
    {
        return $this->imageContent;
    }
    
    /*----------------------------------------------  ----------------------------------------------*/
    
    /**
     * 析构
     */
    public function __destruct()
    {
        imagedestroy($this->resource);
    }
    
}