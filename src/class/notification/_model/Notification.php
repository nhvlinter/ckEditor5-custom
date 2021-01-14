<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 22-11-20
 * Time: 13:07
 */

namespace salesteck\notification;

/**
 * Class Notification
 * @package salesteck\notification
 */
class Notification implements \JsonSerializable
{


    /**
     * @param string $title
     * @param string $message
     * @param string $link
     * @param string $notificationId
     *
     * @return \salesteck\notification\Notification
     */
    public static function _inst(string $title, string $message, string $link = "", string $notificationId = "") : self
    {
        return new self($title, $message, $link, $notificationId);
    }

    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $message;
    /**
     * @var string
     */
    private $href;
    /**
     * @var string
     */
    private $notificationId;

    private $qty;

    /**
     * Notification constructor.
     *
     * @param string $title
     * @param string $message
     * @param string $link
     * @param string $notificationId
     */
    public function __construct(string $title, string $message, string $link = "", string $notificationId = "")
    {
        $this->title = $title;
        $this->message = $message;
        $this->href = $link;
        $this->notificationId = $notificationId;
        $this->qty = 0;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Notification
     */
    public function setTitle(string $title): Notification
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return Notification
     */
    public function setMessage(string $message): Notification
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->href;
    }

    /**
     * @param string $link
     *
     * @return Notification
     */
    public function setLink(string $link): Notification
    {
        $this->href = $link;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotificationId(): string
    {
        return $this->notificationId;
    }

    /**
     * @param string $notificationId
     *
     * @return Notification
     */
    public function setNotificationId(string $notificationId): Notification
    {
        $this->notificationId = $notificationId;
        return $this;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @param string $href
     *
     * @return Notification
     */
    public function setHref(string $href): Notification
    {
        $this->href = $href;
        return $this;
    }

    /**
     * @return int
     */
    public function getQty(): int
    {
        return $this->qty;
    }

    /**
     * @param int $qty
     *
     * @return Notification
     */
    public function setQty(int $qty): Notification
    {
        $this->qty = abs($qty);
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