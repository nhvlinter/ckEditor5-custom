<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 27-10-19
 * Time: 21:01
 */

namespace salesteck\_base;



class FormResponse extends Form_C
{
    private $send, $text, $insertToDb;

    /**
     * FormResponse constructor.
     */
    public function __construct()
    {
        $this->send = false;
        $this->insertToDb = false;
        $this->text = "";
    }

    /**
     * @param bool $send
     */
    public function setSend(bool $send)
    {
        $this->send = $send;
    }

    /**
     * @param string $text
     */
    public function setText(string $text)
    {
        $this->text = $text;
    }

    /**
     * @param bool $insertToDb
     */
    public function setInsertToDb(bool $insertToDb)
    {
        $this->insertToDb = $insertToDb;
    }






}