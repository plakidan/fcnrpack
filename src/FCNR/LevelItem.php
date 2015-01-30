<?php
/**
 * Project: FCNR
 * Ivan Koretskiy <gillbeits@gmail.com>
 * Date: 21/01/15
 * Time: 18:39
 */

namespace FCNR;


/**
 * Class Level
 * @author Ivan Koretskiy <gillbeits@gmail.com>
 * @package FCNR
 */
class LevelItem {
    protected $position;
    protected $remainHeight;
    /**
     * @var Item
     */
    protected $item;

    function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($level, $position)
    {
        $this->item->setPos([$level, $position]);
        $this->position = $position;
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return mixed
     */
    public function getRemainHeight()
    {
        return $this->remainHeight;
    }

    /**
     * @param mixed $remainHeight
     */
    public function setRemainHeight($remainHeight)
    {
        $this->remainHeight = $remainHeight;
    }
}