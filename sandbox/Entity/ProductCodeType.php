<?php


namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Class representing the ProductCodeType model.
 *
 * @ApiResource(
 *     collectionOperations={
 *          "get":      { "path": "/product-code-type" },
 *     },
 *     itemOperations={},
 * )
 */
class ProductCodeType
{
    /**
     * Unique identifier representing a ProductCodeType.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     */
    protected $label;

    /**
     * Human-readable name of ProductCodeType.
     *
     * @var string
     * @Assert\NotNull()
     * @Assert\Type("string")
     */
    protected $name;

    /**
     * Whether ProductCode attached to this ProductCodeType require a serial number while processing
     *
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type("bool")
     */
    protected $expectingSerial;

    /**
     * Constructor
     * @param mixed[] $data Associated array of property values initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->label = isset($data['label']) ? $data['label'] : null;
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->expectingSerial = isset($data['expectingSerial']) ? $data['expectingSerial'] : null;
    }

    /**
     * Gets label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets label.
     *
     * @param string $label  Unique identifier representing a ProductCodeType.
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Gets name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets name.
     *
     * @param string $name  Human-readable name of ProductCodeType.
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets expectingSerial.
     *
     * @return bool
     */
    public function isExpectingSerial()
    {
        return $this->expectingSerial;
    }

    /**
     * Sets expectingSerial.
     *
     * @param bool $expectingSerial  Whether ProductCode attached to this ProductCodeType require a serial number while processing
     *
     * @return $this
     */
    public function setExpectingSerial($expectingSerial)
    {
        $this->expectingSerial = $expectingSerial;

        return $this;
    }
}


