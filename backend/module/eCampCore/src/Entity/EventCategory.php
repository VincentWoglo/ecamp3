<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * EventCategory.
 *
 * @ORM\Entity
 * @ORM\Table(name="event_categories")
 */
class EventCategory extends BaseEntity {
    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $camp;

    /**
     * @var EventType
     * @ORM\ManyToOne(targetEntity="EventType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eventType;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $short;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    private $color;

    /**
     * @var string
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $numberingStyle;

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return Camp
     */
    public function getCamp() {
        return $this->camp;
    }

    public function setCamp($camp) {
        $this->camp = $camp;
    }

    /**
     * @return EventType
     */
    public function getEventType() {
        return $this->eventType;
    }

    public function setEventType(EventType $eventType) {
        $this->eventType = $eventType;

        if (null == $this->getColor()) {
            $this->setColor($eventType->getDefaultColor());
        }
        if (null == $this->getNumberingStyle()) {
            $this->setNumberingStyle($eventType->getDefaultNumberingStyle());
        }
    }

    /**
     * @return string
     */
    public function getShort() {
        return $this->short;
    }

    public function setShort($short) {
        $this->short = $short;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getColor() {
        if (null !== $this->color) {
            return $this->color;
        }
        $eventType = $this->getEventType();

        return null !== $eventType ? $eventType->getDefaultColor() : null;
    }

    public function setColor($color) {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getNumberingStyle() {
        return $this->numberingStyle;
    }

    public function setNumberingStyle($numberingStyle) {
        $this->numberingStyle = $numberingStyle;
    }

    /**
     * @return string
     */
    public function getStyledNumber(int $num): string {
        switch ($this->numberingStyle) {
            case 'a':
                return strtolower($this->getAlphaNum($num));
            case 'A':
                return strtoupper($this->getAlphaNum($num));
            case 'i':
                return strtolower($this->getRomanNum($num));
            case 'I':
                return strtoupper($this->getRomanNum($num));
            default:
                return $num;
        }
    }

    private function getAlphaNum($num) {
        --$num;
        $alphaNum = '';
        if ($num >= 26) {
            $alphaNum .= $this->getAlphaNum(floor($num / 26));
        }
        $alphaNum .= chr(97 + ($num % 26));

        return $alphaNum;
    }

    private function getRomanNum($num) {
        $table = [
            'M' => 1000,  'CM' => 900,  'D' => 500,   'CD' => 400,
            'C' => 100,   'XC' => 90,   'L' => 50,    'XL' => 40,
            'X' => 10,    'IX' => 9,    'V' => 5,     'IV' => 4,
            'I' => 1,
        ];
        $romanNum = '';
        while ($num > 0) {
            foreach ($table as $rom => $arb) {
                if ($num >= $arb) {
                    $num -= $arb;
                    $romanNum .= $rom;

                    break;
                }
            }
        }

        return $romanNum;
    }
}
