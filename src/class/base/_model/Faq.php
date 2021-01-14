<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 17-05-20
 * Time: 13:35
 */

namespace salesteck\_base;


class Faq implements \JsonSerializable
{
    private $question, $answer;

    /**
     * Faq constructor.
     * @param string $question
     * @param string $answer
     */
    public function __construct(string $question, string $answer)
    {
        $this->question = $question;
        $this->answer = $answer;
    }

    /**
     * @return mixed
     */
    public function getQuestion() : string
    {
        return $this->question;
    }

    public function printQuestion(){
        echo htmlspecialchars($this->getQuestion());
    }

    /**
     * @return mixed
     */
    public function getAnswer() : string
    {
        return $this->answer;
    }

    public function printAnswer(){
        echo htmlspecialchars($this->getAnswer());
    }


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}