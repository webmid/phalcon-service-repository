<?php
namespace MyApp\Models;

class User extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $lastname;

    /**
     *
     * @var string
     */
    public $mobile;

    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getLastname()
    {
        return $this->lastname;
    }
    public function getMobile()
    {
        return $this->mobile;
    }
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }


    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("users");
        //$this->skipAttributesOnUpdate(['name','lastname','mobile']);
        //$this->skipAttributesOnCreate(array('name', 'lastname', 'mobile'));

    }


    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return User[]|User|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return User|\Phalcon\Mvc\Model\ResultInterface|\Phalcon\Mvc\ModelInterface|null
     */
    public static function findFirst($parameters = null): ?\Phalcon\Mvc\ModelInterface
    {
        return parent::findFirst($parameters);
    }

}
