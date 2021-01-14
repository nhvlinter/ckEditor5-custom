<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 07-10-19
 * Time: 19:44
 */

namespace salesteck\_base;




class Page implements \JsonSerializable
{

    public static function _inst($row) : ? self
    {
        if(
            is_array($row) &&
            array_key_exists(Page_C::_col_id, $row) &&
            array_key_exists(Page_C::_col_id_code, $row) &&
            array_key_exists(Page_C::_col_link_text, $row) &&
            array_key_exists(Page_C::_col_file_absolute_path, $row) &&
            array_key_exists(Page_C::_col_create_date, $row) &&
            array_key_exists(Page_C::_col_last_modified, $row) &&
            array_key_exists(Page_C::_col_is_enable, $row) &&
            array_key_exists(Page_C::_col_label, $row) &&
            array_key_exists(Page_C::_col_title, $row) &&
            array_key_exists(Page_C::_col_description, $row) &&
            array_key_exists(Page_C::_col_language, $row) &&
            array_key_exists(Page_C::_col_route, $row)
        ){
            return new Page(
                $row[Page_C::_col_id],
                $row[Page_C::_col_id_code],
                $row[Page_C::_col_link_text],
                $row[Page_C::_col_file_absolute_path],
                $row[Page_C::_col_create_date],
                $row[Page_C::_col_last_modified],
                $row[Page_C::_col_is_enable],
                $row[Page_C::_col_label],
                $row[Page_C::_col_title],
                $row[Page_C::_col_description],
                $row[Page_C::_col_keywords],
                $row[Page_C::_col_language],
                $row[Page_C::_col_route]

            );
        }
        return null;
    }

    private
        $id, $idCode, $text, $absolutePath, $createDate, $lastModified, $enable,
        $label, $title, $description, $keyword, $language, $route
    ;

    /**
     * Page constructor.
     * @param int $id
     * @param string $idCode
     * @param string $text
     * @param string $absolutePath
     * @param string $createDate
     * @param string $lastModified
     * @param string $enable
     * @param string $label
     * @param string $title
     * @param string $description
     * @param string $keyword
     * @param string $language
     * @param string $route
     */
    public function __construct(
        int $id, string $idCode, string $text, string $absolutePath, string $createDate, string $lastModified, string $enable,
        string $label, string $title, string $description, string $keyword, string $language, string $route
    ){
        $this->id = $id;
        $this->idCode = $idCode;
        $this->text = $text;
        $this->absolutePath = $absolutePath;
        $this->createDate = $createDate;
        $this->lastModified = $lastModified;
        $this->enable = boolval($enable);
        $this->label = $label;
        $this->title = $title;
        $this->description = $description;
        $this->keyword = $keyword;
        $this->language = $language;
        $this->route = $route;
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIdCode() : string
    {
        return $this->idCode;
    }

    /**
     * @return string
     */
    public function getText() : string
    {
        return $this->text;
    }



    /**
     * @return string
     */
    public function getAbsolutePath() : string
    {
        return $this->absolutePath;
    }

    /**
     * @return string
     */
    public function getCreateDate() : string
    {
        return $this->createDate;
    }

    /**
     * @return string
     */
    public function getLastModified() : string
    {
        return $this->lastModified;
    }

    /**
     * @return bool
     */
    public function isEnable() : bool
    {
        return $this->enable;
    }

    /**
     * @param bool $enable
     * @return $this
     */
    public function setEnable(bool $enable) : self
    {
        $this->enable = $enable;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel() : string
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     * @return $this
     */
    public function setLabel(string$label) : self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title) : self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() :string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getKeyword(): string
    {
        return $this->keyword;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }






    public function _printListHeader(){
        ?>
        <li>
            <a href="<?php echo $this->getRoute() ?>">
                <?php echo $this->getText() ?>
            </a>
        </li>
        <?php
    }

    /**
     * Specify data which should be serialized to JSON
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

}