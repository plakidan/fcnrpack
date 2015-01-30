<?php
/**
 * Project: FCNR
 * Ivan Koretskiy <gillbeits@gmail.com>
 * Date: 21/01/15
 * Time: 18:46
 */

namespace FCNR;


/**
 * Class Level
 * @author Ivan Koretskiy <gillbeits@gmail.com>
 * @package FCNR
 */
class Level {
    /**
     * @var float
     */
    protected $maxLength = 0.0;
    /**
     * @var float
     */
    protected $maxHeight = 0.0;
    /**
     * @var LevelItem[]
     */
    protected $floor = [];
    /**
     * @var LevelItem[]
     */
    protected $ceil = [];
    /**
     * @var int
     */
    protected $level;

    public function __construct($level, $maxLength)
    {
        $this->level = $level;
        $this->maxLength = $maxLength ? : $this->maxLength;
    }

    public function getRemainFloorLength()
    {
        $maxLength = $this->maxLength;
        foreach ($this->floor as $item) {
            $maxLength -= $item->getItem()->getLength();
        }
        return $maxLength;
    }

    /**
     * @return float
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @return float
     */
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    public function getRemainCeilLength()
    {
        $maxLength = $this->maxLength;
        foreach ($this->ceil as $item) {
            $maxLength -= $item->getItem()->getLength();
        }
        return $maxLength;
    }

    public function addFloor(LevelItem $item)
    {
        $item->setRemainHeight(!empty($this->floor) ? $this->floor[0]->getItem()->getHeight() - $item->getItem()->getHeight() : 0);
        $this->maxHeight = max($this->maxHeight, $item->getItem()->getHeight());
        $item->setPosition($this->level, count($this->floor));
        $this->floor[] = $item;
        return $this;
    }

    public function addCeil(LevelItem $item)
    {
        $item->setPosition($this->level, count($this->ceil));
        $this->ceil[] = $item;
        return $this;
    }

    public function isItemCeilPlaced(Item $item)
    {
        $reverseFloor = [];
        $ceilLength = $this->getRemainCeilLength();
        foreach ($this->floor as $i) {
            if ($i->getItem()->getLength() <= $ceilLength) {
                $reverseFloor[] = $i;
                $ceilLength -= $i->getItem()->getLength();
                continue;
            }
            break;
        }

        if (empty($reverseFloor)) return false;

        $reverseFloor = array_reverse($reverseFloor);

        $length = $this->getRemainFloorLength();
        /** @var LevelItem $i */
        foreach ($reverseFloor as $i) {
            if ($item->getLength() <= ($length += $i->getItem()->getLength())) {
                if ($item->getHeight() <= $i->getRemainHeight()) {
                    return true;
                }
                break;
            }
        }
    }

    /**
     * @return LevelItem[]
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * @return LevelItem[]
     */
    public function getCeil()
    {
        return $this->ceil;
    }
}