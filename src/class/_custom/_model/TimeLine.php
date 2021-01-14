<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 02-10-19
 * Time: 16:44
 */

namespace Content;


class TimeLine
{

    private $name, $title, $desc, $icon;

    /**
     * WorkFlow constructor.
     * @param string $name
     * @param string $title
     * @param string $desc
     * @param string $icon
     */
    public function __construct(string $name, string $title, string $desc, string $icon)
    {
        $this->name = $name;
        $this->title = $title;
        $this->desc = $desc;
        $this->icon = $icon;
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
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
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
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDesc(): string
    {
        return $this->desc;
    }

    /**
     * @param string $desc
     * @return $this
     */
    public function setDesc(string $desc)
    {
        $this->desc = $desc;
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon(string $icon)
    {
        $this->icon = $icon;
        return $this;
    }




}