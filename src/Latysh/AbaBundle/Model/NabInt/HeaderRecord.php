<?php
/**
 * Author: Latysh (Altynbek Usenov)
 */
namespace Latysh\AbaBundle\Model\NabInt;

use Symfony\Component\Validator\Constraints as Assert;

class HeaderRecord {

    /**
     * @var string $indicator
     *
     * @Assert\NotBlank()
     * @Assert\EqualTo(value = 01)
     */
    private $indicator;

    /**
     * @var string $fileName
     *
     * @Assert\Blank()
     */
    private $fileName;

    /**
     * @var string $numberMessages
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[\d]{0,3}$/",
     *     message = "Header record number of messages is invalid: {{ value }}. Must be up to 3 digits only."
     * )
     */
    private $numberMessages;

    public function __construct() {
        $this->setIndicator('01');
        $this->setNumberMessages('0');
    }

    /**
     * @return string
     */
    public function getIndicator() {
        return $this->indicator;
    }

    /**
     * @param string $indicator
     */
    public function setIndicator($indicator) {
        $this->indicator = $indicator;
    }

    /**
     * @return string
     */
    public function getFileName() {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getNumberMessages() {
        return $this->numberMessages;
    }

    /**
     * @param string $numberMessages
     */
    public function setNumberMessages($numberMessages) {
        $this->numberMessages = $numberMessages;
    }
}
