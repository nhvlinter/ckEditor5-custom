<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 09-12-19
 * Time: 18:55
 */

namespace salesteck\admin;



use Helper\Asset;

class PortalAsset extends Asset
{


    /**
     * General private constant part
     */
    private const
        SrcDirectory = PortalPage_C::Src."/src/element";

    /**
     * File constant
     */
    public const
        HEADER = self::SrcDirectory."/header.php",
        FOOTER = self::SrcDirectory."/footer.php",
        HEAD = self::SrcDirectory."/head.php",
        SCRIPT = self::SrcDirectory."/script.php"
    ;
}