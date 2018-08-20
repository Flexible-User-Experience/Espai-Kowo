<?php

namespace AppBundle\Model;

/**
 * Class CategoryHistogramHelperModel
 */
class CategoryHistogramHelperModel
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $amount;

    /**
     * Methods
     */

    /**
     * CategoryHistogramHelperModel constructor.
     *
     * @param string $title
     * @param int $amount
     */
    public function __construct($title, $amount)
    {
        $this->title = $title;
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }
}
