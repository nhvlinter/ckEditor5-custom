<?php
namespace salesteck\_base;

use salesteck\utils\Debug;

/**
 * Created by PhpStorm.
 * User: Son
 * Date: 12-11-19
 * Time: 02:24
 */

class SocialMedia_View
{

    public static function _displaySocialMedia(array $socialMedias = [], bool $showColor = true){


        if($socialMedias === []){
            $sql = SocialMedia_C::_getSql();
            $sql->equal(SocialMedia_C::TABLE, SocialMedia_C::_col_is_enable, intval(true));

            if($sql->select()){
                $socialMedias = $sql->result();
            }
        }
        foreach ($socialMedias as $socialMedia){
            if(
                    array_key_exists(SocialMedia_C::_col_icon, $socialMedia) &&
                    array_key_exists(SocialMedia_C::_col_link, $socialMedia) &&
                    array_key_exists(SocialMedia_C::_col_color, $socialMedia)
            ){
                $icon = $socialMedia[SocialMedia_C::_col_icon];
                $link = $socialMedia[SocialMedia_C::_col_link];
                $color = "";
                if($showColor){
                    $color = $socialMedia[SocialMedia_C::_col_color];
                }
                if($icon !== "" && $link !== "") {
                    ?>
                    <li>
                        <a href="<?php echo($link) ?>" class="" target="_blank"
                           style="background-color: <?php echo $color ?>">
                            <i class="<?php echo $icon ?>"></i>
                        </a>
                    </li>
                    <?php
                }
            }
        }

        Debug::_exposeVariable($socialMedias, false);
    }

}