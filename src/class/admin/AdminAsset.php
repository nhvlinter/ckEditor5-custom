<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 09-12-19
 * Time: 18:55
 */

namespace salesteck\admin;



use Helper\Asset;

class AdminAsset extends Asset
{


    /**
     * General private constant part
     */
    private const
        SrcDirectory = "/admin/src/element";

    /**
     * File constant
     */
    public const
        HEADER = self::SrcDirectory."/header.php",
        FOOTER = self::SrcDirectory."/footer.php",
        HEAD = self::SrcDirectory."/head.php",
        SCRIPT = self::SrcDirectory."/script.php"
    ;

    public const
        SECTION_MERCHANT_GRID = "/admin/src/section/merchant-grid.php",
        SECTION_MERCHANT_GRID_SCRIPT = "/admin/src/section/merchant-grid-script.php"
    ;
}