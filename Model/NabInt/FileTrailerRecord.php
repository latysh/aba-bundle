<?php
/**
 * Author: Latysh (Altynbek Usenov)
 */
namespace Latysh\AbaBundle\Model\NabInt;

use Symfony\Component\Validator\Constraints as Assert;

class FileTrailerRecord
{
    /**
     * @var string $indicator
     *
     * @Assert\NotBlank()
     * @Assert\EqualTo(value = 99)
     */
    private $indicator;

    /**
     * @var string $filename
     *
     * @Assert\Regex(
     *     pattern = "/^[\w]{0,20}$/",
     *     message = "File trailer record file name is invalid: {{ value }}. Must be up to 20 characters, including special characters(+-/'?.,())."
     * )
     */
    private $filename;

    /**
     * @var \DateTime $createdDate
     *
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $createdDate;


    public function __construct()
    {
        $this->setIndicator('99');
        $this->setCreatedDate(new \DateTime());
    }

    /**
     * @return string
     */
    public function getIndicator()
    {
        return $this->indicator;
    }

    /**
     * @param string $indicator
     */
    public function setIndicator($indicator)
    {
        $this->indicator = $indicator;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTime $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }
}
