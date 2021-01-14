<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 05-05-20
 * Time: 15:05
 */

namespace salesteck\product;

use salesteck\_base\Language_C;
use salesteck\utils\String_Helper;

class Product_view
{

    public static function _displayProduct($product, $language){
        if($product instanceof Product){
            $language = Language_C::_getValidLanguage($language);
            $name = $product->getName();
            $description = $product->getDescription();
            $price = $product->getPriceDec();
            $categoryName = $product->getCategoryName($language);
            $imageSrc = $product->getImageWebPath();
            $arrayAllergen = $product->getAllergen();
//            $imageSrc = '/assets/images/300.png';
//            if(is_string($imagePath) && $imagePath !== "" && File::_fileExist($imagePath)){
//                $imageSrc = $imagePath;
//            }
            ?>


            <div class="product-item elevation dp-product">
                <?php
                if(String_Helper::_isStringNotEmpty($imageSrc)){
                    ?>

                    <div class="product-image">
                        <img src="<?php echo $imageSrc?>" class="img-fluid">
                    </div>
                    <?php
                }
                ?>
                <div class="product-all-details">
                    <div class="visible-details">
                        <div class="product-details">
                            <p class="product-name primary text-xs">
                                <?php echo $name; ?>
                            </p>
                            <p class="product-desc text-xxs"><?php echo $categoryName; ?></p>
                            <p class="product-desc text-xxs"><?php echo $description; ?></p>
                            <div class="product-allergen">
                                <p class="text-accent text-xxs m-b-5">allergènes</p>
                                <div>

                                    <?php
                                    foreach ($arrayAllergen as $allergenIdCode){
                                        $allergen = Allergen_C::_getAllergenByIdCode($allergenIdCode, $language);
                                        if($allergen instanceof Allergen){
                                            ?>
                                            <span class="badge badge-pill badge-secondary allergen"><?php echo $allergen->getName()?></span>
                                            <?php
                                        }
                                    }?>
                                </div>
                            </div>
                        </div>
                        <div class="product-extra">
                            <div class="price">
                                <div class="cart">
                                    <div class="product-price text-accent"><?php echo $price; ?> €</div>
                                    <input type="number" placeholder="1" value="1" min="1" disabled="disabled"
                                           class="qty-input form-control">
                                    <button class="product-add btn dp-add-btn" disabled="disabled">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
        }
    }

    public static function _displayAllProduct(string $language)
    {
        $arrayAllAllergen = Allergen_C::_getAllAllergen(
            [Product_C::_col_is_enable => intval(true)],
            [Product_C::_col_language => $language]);
        $arrayAllProduct = Product_C::_getAllProduct(
                [Product_C::_col_is_enable => intval(true)],
                [Product_C::_col_language => $language]
        );
        foreach ($arrayAllProduct as $product) {
            self::_displayProduct($product, $language);
        }
    }

}