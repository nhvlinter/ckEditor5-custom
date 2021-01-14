<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 22-11-20
 * Time: 14:39
 */

namespace salesteck\notification;
use salesteck\utils\String_Helper;


/**
 * Class GroupNotification
 * @package salesteck\notification
 */
class GroupNotification implements \JsonSerializable
{

    public static function _inst(string $name, array $_notifications) : ? self
    {
        if(String_Helper::_isStringNotEmpty($name) && sizeof($_notifications) > 0){
            return new self($name, $_notifications);
        }

        return null;
    }

    /**
     * @var string $name
     */
    private $name;
    /**
     * @var array $_notifications
     */
    private $_notifications;

    /**
     * GroupNotification constructor.
     *
     * @param string $name
     * @param array  $_notifications
     */
    private function __construct(string $name, array $_notifications)
    {
        $this->name = $name;
        $this->_notifications = $_notifications;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return GroupNotification
     */
    public function setName(string $name): GroupNotification
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getNotifications(): array
    {
        return $this->_notifications;
    }

    /**
     * @param array $notifications
     *
     * @return GroupNotification
     */
    public function setNotifications(array $notifications): GroupNotification
    {
        $this->_notifications = $notifications;
        return $this;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}