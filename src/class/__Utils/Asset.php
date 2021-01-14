<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 30-07-19
 * Time: 09:21
 */
namespace Helper;

use salesteck\_base\Page;
use salesteck\utils\File;

class Asset
{

    /**
     * General private constant part
     */
    private const SrcDirectory = "/src/element/";



    /**
     * File constant
     */
    public const

        HEADER = self::SrcDirectory."header/index.php",
        HEADER_TRANSPARENT = self::SrcDirectory."header/header-transparent.php",
        HEADER_INNER = self::SrcDirectory."header/header-inner.php",
        HEADER_MODERN = self::SrcDirectory."header/header-modern.php",
        HEADER_DEFAULT = self::SrcDirectory."header/header.php",
        HEADER_MINI = self::SrcDirectory."header/header-mini.php",

        TOPBAR = self::SrcDirectory."topbar.php",

        FOOTER = self::SrcDirectory."footer/index.php",
        FOOTER_DEFAULT = self::SrcDirectory."footer/footer.php",
        SETTING_APP = self::SrcDirectory."setting_app.php",


        HEAD = self::SrcDirectory."head.php",
        SCRIPT = self::SrcDirectory."script.php"
    ;


    private const
        SECTION_SEARCH_BAR = self::SrcDirectory."section/search-bar/",
        SECTION_CONDITION = self::SrcDirectory."section/condition/"
    ;

    /**
     * Head path constant part
     */



    /**
     * @param $string
     */
    public static function _printAsset($string){
        print($string);
    }

    /**
     * @param string $link
     */
    public static function _printCdnCssAsset(string $link){
        $cssLink = "<link rel='stylesheet' href='$link'>";
        self::_printAsset($cssLink);
    }

    /**
     * print CDN asset
     * @param string $link
     */
    public static function _printCdnJsAsset(string $link){
        $jsSrc = "<script src='$link'></script>";
        self::_printAsset($jsSrc);
    }

    /**
     * print image tag with arguments if image exists
     * @param string $imagePath
     * @param array (string) $class
     * @param string $title
     * @param string $desc
     */
    public static function _printImgAsset(string $imagePath, array $class = [], string $title = "",  string $desc = ""){
        if(File::_fileExist($imagePath)) {
            $title = $title !== "" ? "title='$title'" : $title;
            $desc = $desc !== "" ? "alt='$desc'" : $desc;
            $classes = implode($class, " ");
            $classes = $classes !== "" ? "class='$classes'" : "";

            $imgLink = "<img src='$imagePath' $classes $title $desc/>";
            self::_printAsset($imgLink);
        }
    }


    /*** start include file ***/


    /**
     * @param $page
     */

    public static function _includeSearchBar ($page){
        if($page instanceof Page){
            $language = $page->getLanguage();
            $searchBarDir = self::SECTION_SEARCH_BAR;
            File::_includeFile("$searchBarDir/$language.php", ["page" => $page]);
        }
    }


    /*** start include file ***/


}