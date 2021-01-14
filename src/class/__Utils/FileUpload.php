<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 03-07-20
 * Time: 13:46
 */

namespace salesteck\utils;



class FileUpload implements \JsonSerializable
{
    private const
        TMP_NAME = "tmp_name",
        NAME = "name",
        ERROR = "error",
        SIZE = "size",
        TYPE = "type",
        FILE_PROP = array(self::TMP_NAME, self::NAME, self::ERROR, self::SIZE, self::TYPE)
    ;




    public static function _inst(string $fileUploadIndex) : ? self
    {
//        echo json_encode($_FILES);
        $file = null;
        if($fileUploadIndex !== "" && array_key_exists($fileUploadIndex, $_FILES)){
            $fileUpload = $_FILES[$fileUploadIndex];
            if(count(array_intersect_key(array_flip(self::FILE_PROP), $fileUpload)) === count(self::FILE_PROP)){
                return new self(
                    $fileUpload[self::TMP_NAME], $fileUpload[self::NAME],
                    $fileUpload[self::ERROR], $fileUpload[self::SIZE], $fileUpload[self::TYPE]
                );
            }
        }
        return $file;
    }

    private $tmp_name;
    private $name;
    private $error;
    private $size;
    private $type;
    private $extension;

    /**
     * FileUpload constructor.
     * @param string $tmp_name
     * @param string $name
     * @param int $error
     * @param int $size
     * @param string $type
     */
    public function __construct(string $tmp_name, string $name, int $error, int $size, string $type)
    {
        $this->tmp_name = $tmp_name;
        $this->name = $name;
        $this->error = $error;
        $this->size = $size;
        $this->type = $type;
        $this->extension = pathinfo($name, PATHINFO_EXTENSION);
    }

    /**
     * @return string
     */
    public function getTmpName(): string
    {
        return $this->tmp_name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getExtension() : string
    {
        return $this->extension;
    }



    public function moveTo(string $folderPath) : bool
    {
        if($folderPath !== "" && File::_folderExist($folderPath)){

            $target_file = $folderPath.$this->getName();
            $target_file = File::_getFileFullPath($target_file);

            if( !File::_fileExist($target_file) ){
                return move_uploaded_file($this->getTmpName(), $target_file);
            }
        }else{
            $this->error = "Can't find folder : $folderPath";
        }
        return false;
    }







    public static function _upload(string $fileName = ""){
        if( $fileName !== ""){
            if(array_key_exists($fileName, $_FILES)){
                $files = $_FILES[$fileName];
            }else{
                throw new \Exception("file : $fileName doesn't exist");
            }
        }else{
            throw new \Exception("file name is empty");
        }
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
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