<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 01-05-20
 * Time: 00:09
 */

namespace salesteck\_base;


use salesteck\_interface\DbControllerObject;
use salesteck\_interface\DbControllerTranslation;
use salesteck\Db\Sql;
use salesteck\utils\File;

class Gallery_C  extends Image_c implements DbControllerObject, DbControllerTranslation
{
    public const
        TABLE = "_gallery",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION,
        TABLE_IMAGES = self::TABLE."_images"
    ;
    public const FOLDER_SRC = self::SRC."gallery/";

    public static function _count(array $columnValue = []){
        $sql = self::_getSql();
        foreach ($columnValue as $columnName => $value){
            $sql->equal(self::TABLE, $columnName, $value);
        }
        return $sql->count();
    }

    static function _getSqlImages(): Sql
    {
        return Sql::_inst(self::TABLE_IMAGES);
    }

    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getSqlTranslation(): Sql
    {
        return Sql::_inst(self::TABLE_TRANSLATION);
    }

    public static function _getUniqueId(): ? string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }

    public static function _getElementById(string $id){
        return parent::_getTableElementBy(self::TABLE_TRANSLATION, self::_col_id, $id);
    }

    public static function _getElementByIdCode(string $idCode){
        return parent::_getTableElementBy(self::TABLE, self::_col_id_code, $idCode);
    }

    public static function _deleteElement(string $idCode){
        if($idCode !== ""){
            $elementRow = self::_getElementByIdCode($idCode);
            $imageId = array_key_exists(self::_col_image, $elementRow) ? $elementRow[self::_col_image] : null;
            self::_deleteElementImage($imageId);
            $sql = self::_getSql();
            $sql->equal(self::TABLE, self::_col_id_code, $idCode);
            $sql->delete();
        }
    }

    private static function _deleteElementImage($imageId){
        if(gettype($imageId) === gettype("")){
            $sqlImage = self::_getSqlImages();
            $sqlImage->equal(self::TABLE_IMAGES, self::_col_id, $imageId);
            if($sqlImage->select()){
                $imageRow = $sqlImage->first();
                if(gettype($imageRow) === gettype([])){
                    $imageAbsolutePath = array_key_exists(self::_col_file_absolute_path, $imageRow) ? $imageRow[self::_col_file_absolute_path] : null;
                    if($imageAbsolutePath !== "" && $imageAbsolutePath !== null){
                        File::_deleteFile($imageAbsolutePath);
                    }
                }

            }
            $sqlImage->delete();
        }
    }


    public static function _getGalleryImages(
        array $columnValue = [],
        array $columnValueTranslation = [],
        array $columnValueImage = [],
        int $limit = -1,
        int $offSet = -1
    ) : array
    {
        $arrayImages = [];
        $sql = self::_getSql();
        $sql->innerJoin(self::TABLE, self::_col_id_code, self::TABLE_TRANSLATION, self::_col_id_code);
        $sql->innerJoin(self::TABLE, self::_col_image, self::TABLE_IMAGES, self::_col_id);
        $sql->orderAsc(self::TABLE, self::_col_order);
        foreach ($columnValue as $column => $value){
            $sql->equal(self::TABLE, $column, $value);
        }
        foreach ($columnValueTranslation as $column => $value){
            $sql->equal(self::TABLE_TRANSLATION, $column, $value);
        }
        foreach ($columnValueImage as $column => $value){
            $sql->equal(self::TABLE_IMAGES, $column, $value);
        }
        $sql->limit($limit, $offSet);
        if($sql->select()){
            $arrayResult = $sql->result();
            foreach ($arrayResult as $row){
                $galleryImage = self::_getObjectClassFromResultRow($row);
                if($galleryImage !== null && $galleryImage instanceof Image){
                    array_push($arrayImages, $galleryImage);
                }
            }
        }

        return $arrayImages;
    }

    static function _getObjectClassFromResultRow($row) : ? Image
    {
        $var = null;

        if(
            array_key_exists(self::_col_id_code, $row) &&
            array_key_exists(self::_col_title, $row) &&
            array_key_exists(self::_col_description, $row) &&
            array_key_exists(self::_col_file_absolute_path, $row)
        ){
            $absolutePath = $row[self::_col_file_absolute_path];
            if($absolutePath !== ""  && File::_fileExist($absolutePath)){
                $var =  new Image(
                    $row[self::_col_id_code],
                    $row[self::_col_title],
                    $row[self::_col_description],
                    $absolutePath
                );
            }
        }

        return $var;
    }

    static function _clean()
    {
        // TODO: Implement _clean() method.
    }
}