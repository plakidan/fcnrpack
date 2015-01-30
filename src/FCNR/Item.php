<?php
/**
 * Project: FCNR
 * Ivan Koretskiy <gillbeits@gmail.com>
 * Date: 16/01/15
 * Time: 17:35
 */

namespace FCNR;


/**
 * Class Item
 * @author Ivan Koretskiy <gillbeits@gmail.com>
 * @package FCNR
 */
class Item {
    protected $_id;
    /**
     * @var null|array
     */
    protected $pos = null;
    /**
     * @var float
     */
    protected $width;
    /**
     * @var float
     */
    protected $length;
    /**
     * @var float
     */
    protected $height;
    /**
     * @var bool
     */
    protected $rotated;

    function __construct($width, $length, $height, $rotated, $id = null, $maxWidth = null)
    {
        $this->width = $width;
        $this->length = $length;
        $this->height = $height;
        $this->rotated = $rotated;
        $this->_id = $id;

        if (null === $id) {
            $this->_id = substr(sha1(md5(serialize([$width, $length, $height, $rotated]))), 0, 8);
        }

        if ($rotated) {

            $__width = array_diff([$width, $length, $height], [max($width, $length, $height), min($width, $length, $height)]);
            if (!empty($__width)) {
                $__width = ceil(reset($__width));
            } elseif(array_sum([$width, $length, $height]) > 0) {
                $__width = (int)ceil(2 / 3 * array_sum([$width, $length, $height]) / (max($width, $length, $height) + min($width, $length, $height)) > 1 ? max($width, $length, $height) : min($width, $length, $height));
            }

            $this->width = $__width;
            $this->length = max($width, $length, $height);
            if ($maxWidth && $this->length <= $maxWidth) {
                $this->width = $this->length;
                $this->length = $__width;
            }
            $this->height = min($width, $length, $height);
            $this->rotated = $rotated;
        }
    }

    /**
     * @return array|null
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * @param array|null $pos
     */
    public function setPos($pos)
    {
        $this->pos = $pos;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param float $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return float
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param float $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param float $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return boolean
     */
    public function isRotated()
    {
        return $this->rotated;
    }

    /**
     * @param boolean $rotated
     */
    public function setRotated($rotated)
    {
        $this->rotated = $rotated;
    }


}