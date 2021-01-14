<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 12-11-19
 * Time: 14:39
 */

namespace salesteck\custom;



use salesteck\_interface\DbControllerObject;
use salesteck\_interface\DbIdCode;
use salesteck\Db\Db;
use salesteck\Db\Sql;
use salesteck\utils\CustomDateTime;

class Skill_C extends Db implements DbControllerObject, DbIdCode
{
    public const TABLE = "_skills";
    public const TABLE_TRANSLATION = "_skills".self::_TRANSLATION;

    public const
        dotNet = "dot net",
        visualBasic = "visual basic",
        php = "php",
        symfony = "symfony",
        laravel = "laravel",
        drupal = "drupal",
        wordPress = "wordPress",
        java = "java",
        python = "python",
        bootstrap = "bootstrap",
        jquery = "jquery",
        nodeJs = "node Js",
        wooCommerce = "woo Commerce",
        shopify = "shopify",
        xamarin = "xamarin",
        ios = "ios",
        android = "android",
        swift = "swift",
        kotlin = "kotlin"
    ;


    static function _getSql(): Sql
    {
        return Sql::_inst(self::TABLE);
    }

    static function _getObjectClassFromResultRow($row) : ? Skills
    {
        if(
            $row !== null && gettype($row) === gettype([]) &&
            array_key_exists(self::_col_name, $row) &&
            array_key_exists(self::_col_image, $row) &&
            array_key_exists(self::_col_is_enable, $row)
        ){
            return new Skills(
                $row[self::_col_name],
                $row[self::_col_image],
                $row[self::_col_is_enable]
            );
        }else{
            return null;
        }
    }

    public static function _getAllSkill(bool $enable = false){
        $sql = self::_getSql();
        $skills = [];
        if($enable){
            $sql->equal(self::TABLE, self::_col_is_enable, strval($enable));
        }
        if($sql->select()){
            $result = $sql->result();
            foreach ($result as $row){

                $skill = self::_getObjectClassFromResultRow($row);
                if($skill !== null && $skill instanceof Skills){
                    array_push($skills, $skill);
                }
            }
        }
        $sql = null;
        return $skills;
    }


    public static function _getSkillFromLabel($arrayLabel, bool $enable = false){
        $sql = self::_getSql();
        $skills = [];
        foreach ($arrayLabel as $label){
            $sql->equal(self::TABLE, self::_col_label, $label);
            if($enable){
                $sql->equal(self::TABLE, self::_col_is_enable, strval($enable));
            }
            if($sql->select()){
                $skill = self::_getObjectClassFromResultRow($sql->first());
                if($skill !== null && $skill instanceof Skills){
                    array_push($skills, $skill);
                }
            }
        }
        $sql = null;
        return $skills;
    }



    public static function _indexSkills(){


        $dotNet = new Skills(self::dotNet, "ms-dot-net.png");
        $visualBasic = new Skills(self::visualBasic, "visual-basic.png");
        $php = new Skills(self::php, "php.png");
        $symfony = new Skills(self::symfony, "symfony.png");
        $laravel = new Skills(self::laravel, "laravel.png");
        $drupal = new Skills(self::drupal, "drupal.png");
        $wordPress = new Skills(self::wordPress, "wordpress.png");
        $java = new Skills(self::java, "java.png");
        $python = new Skills(self::python, "python.png");
        $bootstrap = new Skills(self::bootstrap, "bootstrap.png");
        $jquery = new Skills(self::jquery, "jquery.png");
        $nodeJs = new Skills(self::nodeJs, "nodejs.png");
        $wooCommerce = new Skills(self::wooCommerce, "woocommerce.png");
        $shopify = new Skills(self::shopify, "shopify.png");
        $xamarin = new Skills(self::xamarin, "xamarin.png");



        $ios = new Skills(self::ios, "ios.png");
        $android = new Skills(self::android, "android.png");
        $swift = new Skills(self::swift, "swift.png");
        $kotlin = new Skills(self::kotlin, "kotlin.png");

        $allSkills = [
            $dotNet, $visualBasic, $php, $symfony, $laravel, $drupal, $wordPress, $java, $python, $ios, $android, $swift, $kotlin, $bootstrap, $jquery, $nodeJs, $wooCommerce, $shopify, $xamarin
        ];

        $sql = self::_getSql();
        $dataToInsert = [];
        foreach ($allSkills as $skill){
            if($skill !== null && $skill instanceof Skills){
                $createDate = CustomDateTime::_getTimeStamp();
                $row = [
                    self::_col_label => $skill->getName(),
                    self::_col_name => $skill->getName(),
                    self::_col_image => $skill->getAbsoluteImagePath(),
                    self::_col_create_date => $createDate,
                    self::_col_last_modified => $createDate,
                    self::_col_is_enable => strval($skill->isEnable()),
                ];
                array_push($dataToInsert, $row);
            }
        }
        $sql->bulkInsert($dataToInsert);
    }
    public static function _getUniqueId(): string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }
}