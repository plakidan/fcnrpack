<?php
/**
 * Project: FCNR
 * Ivan Koretskiy <gillbeits@gmail.com>
 * Date: 16/01/15
 * Time: 17:41
 */

namespace FCNR;


/**
 * Class Packer
 * @author Ivan Koretskiy <gillbeits@gmail.com>
 * @package FCNR
 */
class Packer {
    /**
     * @var int
     */
    protected $scale        = 10;

    /**
     * @var Item[]
     */
    protected $items        = [];
    /**
     * @var Level[]
     */
    protected $levels       = [];

    protected $maxLength    = 0;
    protected $maxWidth     = 0;

    protected $colors = [
        'fillMain' => null,
        'borderMain' => null
    ];

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->setItems($items);
        $this->maxLength = $this->_getStripLength();
    }

    /**
     * @param float $maxWidth
     */
    public function setMaxWidth($maxWidth)
    {
        if ($maxWidth < $this->maxWidth) {
            throw new \Exception("Wrong strip width!");
        }
        $this->maxWidth = $maxWidth;
        return $this;
    }

    /**
     * @param float $maxLength
     */
    public function setMaxLength($maxLength)
    {
        if ($maxLength < $this->_getStripLength()) {
            throw new \Exception("Wrong strip length!");
        }
        $this->maxLength = $maxLength;
        return $this;
    }

    /**
     * @return int
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @param int $SCALE
     */
    public function setScale($scale)
    {
        $this->scale = $scale;
        return $this;
    }

    /**
     * @param Item $item
     */
    public function addItem(Item $item)
    {
        $this->items[] = $item;
        $this->maxWidth = max($item->getWidth(), $this->maxWidth);
        return $this;
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems(array $items)
    {
        foreach ($items as $item) {
            list($width, $length, $height, $rotated) = $item;
            $this->addItem(new Item($width, $length, $height, $rotated, null, $this->maxWidth));
        }
        return $this;
    }

    public function _getStripLength()
    {
        $arr = $this->items;
        usort($arr, function ($a, $b) {
            /**
             * @var Item $a
             * @var Item $b
             */
            return $a->getLength() > $b->getLength() ? -1 : 1;
        });
        return $arr[0]->getLength();
    }

    private function _sort()
    {
        usort($this->items, function ($a, $b) {
            /**
             * @var Item $a
             * @var Item $b
             */
            return $a->getHeight() > $b->getHeight() ? -1 : 1;
        });
        return $this;
    }

    public function pack()
    {
        $this->_sort();
        $items = $this->items;
        while(!empty($items)) {
            $level = new Level(count($this->levels), $this->maxLength);
            foreach ($items as $i => $item) {
                if ($level->getRemainFloorLength() >= $item->getLength()) {
                    $level->addFloor(new LevelItem($item));
                    unset($items[$i]);
                }
            }

            foreach ($items as $i => $item) {
                if ($level->isItemCeilPlaced($item)) {
                    $level->addCeil(new LevelItem($item));
                    unset($items[$i]);
                }
            }

            $this->levels[] = $level;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getDimensions()
    {
        return [
            $this->maxLength,
            $this->_getLevelsHeight(),
            $this->maxWidth
        ];
    }

    protected function _getLevelsHeight()
    {
        $height = 0;
        foreach ($this->levels as $level) {
            $height += $level->getFloor()[0]->getItem()->getHeight();
        }
        return $height;
    }

    private function getColors($canvas)
    {
        $this->colors = (object)[
            'fillMain'      => imagecolorallocate($canvas, 0, 0, 0),
            'borderMain'    => imagecolorallocate($canvas, 255, 255, 255)
        ];

        return $this->colors;
    }

    private function rectangle($canvas, $x1, $y1, $x2, $y2, LevelItem $item)
    {
        $colors = $this->getColors($canvas);
        imagefilledrectangle($canvas, $x1 * $this->scale, $y1 * $this->scale, $x2 * $this->scale, $y2 * $this->scale, imagecolorallocate($canvas, hexdec(substr($item->getItem()->getId(), 0, 2)), hexdec(substr($item->getItem()->getId(), 2, 2)), hexdec(substr($item->getItem()->getId(), 4, 2))));
        imagerectangle($canvas, $x1 * $this->scale, $y1 * $this->scale, $x2 * $this->scale, $y2 * $this->scale, $colors->borderMain);
        imagettftext($canvas, 7, 0, ($x1 + $item->getItem()->getLength() / 2 - 2) * $this->scale, ($y2 + $item->getItem()->getHeight() / 2) * $this->scale + 4, $colors->fillMain, __DIR__ . '/arial.ttf', $item->getItem()->getLength() . "x" . $item->getItem()->getWidth() . "x" . $item->getItem()->getHeight());
    }

    public function getImage()
    {
        $length = $this->maxLength;
        $height = $this->_getLevelsHeight();

        $canvas = imagecreatetruecolor($length * $this->scale, $height * $this->scale);
        imagerectangle($canvas, $length * $this->scale, 0, 0, $height * $this->scale, $this->getColors($canvas)->fillMain);

        $startHeight = $this->_getLevelsHeight();
        foreach($this->levels as $level)
        {
            $x1 = 0;
            foreach ($level->getFloor() as $item) {
                $x1 = $x1;
                $x2 = $x1 + $item->getItem()->getLength();
                $y1 = $startHeight;
                $y2 = $startHeight - $item->getItem()->getHeight();
                $this->rectangle($canvas, $x1, $y1, $x2, $y2, $item);
                $x1 += $item->getItem()->getLength();
            }

            $x2 = $length;
            foreach ($level->getCeil() as $item) {
                $x1 = $x2 - $item->getItem()->getLength();
                $x2 = $x2;
                $y1 = $startHeight - $level->getMaxHeight() + $item->getItem()->getHeight();
                $y2 = $startHeight - $level->getMaxHeight();
                $this->rectangle($canvas, $x1, $y1, $x2, $y2, $item);
                $x2 -= $item->getItem()->getLength();
            }

            $startHeight -= $level->getMaxHeight();
        }
        header('Content-Type: image/png');
        imagepng($canvas, null, 9);
        exit;
    }
}