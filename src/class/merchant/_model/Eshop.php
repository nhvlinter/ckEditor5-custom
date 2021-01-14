<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 25-10-20
 * Time: 01:50
 */

namespace salesteck\merchant;


use JsonSerializable;
use salesteck\_interface\ArrayUnique;
use salesteck\_base\Language_C;
use salesteck\_base\Page;
use salesteck\_base\Page_C;
use salesteck\_base\Page_View;
use salesteck\utils\File;
use salesteck\utils\String_Helper;

/**
 * Class Eshop
 * @package salesteck\merchant
 */
class Eshop implements JsonSerializable, ArrayUnique
{


    /**
     * This is used to store Eshop page in a static variable for faster processing
     * @var null|Page $_eshopPage
     * @default null
     *
     * @var null|string
     */
    private static
        $_eshopPage = null,
        $_languagePage = null
    ;

    private static function _getEshopPage($language){
        if(String_Helper::_isStringNotEmpty($language)){
            $languagePage = Language_C::_getValidLanguage($language);
            if($languagePage !== self::$_languagePage || !(self::$_eshopPage instanceof Page)){
                self::$_languagePage = $languagePage;
                self::$_eshopPage = Page_C::_getPageByPath(Page_C::pageEshop, $language);

            }
        }
        return self::$_eshopPage;
    }

    public const ARRAY_ROW_KEY = [
        Merchant_C::_col_merchant_id_code, Merchant_C::_col_commercial_name,
        Merchant_C::_col_post_code, Merchant_C::_col_address,
        Merchant_C::_col_phone, Merchant_C::_col_commercial_url,
        Merchant_C::_col_category, Merchant_C::_col_category_tag
    ];

    public static function _inst($sqlRow, string $language) : ? self
    {

        if(
            self::_allKeyExist($sqlRow) && array_key_exists(Merchant_C::_col_web_path, $sqlRow)
        ){
            $category = $sqlRow[Merchant_C::_col_category];
//            $category = "";
            $commercialName = $sqlRow[Merchant_C::_col_commercial_name];
            $postCode = $sqlRow[Merchant_C::_col_post_code];
            $postCode = $postCode !== "" ?  "($postCode)" : $postCode;
            $displayText = "$commercialName";
            $eshopPage = self::_getEshopPage($language);
            if($eshopPage instanceof Page){
                $shopPage = Page_View::_getRoute($eshopPage, [$sqlRow[Merchant_C::_col_commercial_url], ""]);
                if(String_Helper::_isStringNotEmpty($shopPage)){

                    return new self(
                        $sqlRow[Merchant_C::_col_merchant_id_code],
                        $commercialName,
                        $displayText,
                        $postCode,
                        $sqlRow[Merchant_C::_col_address],
                        $sqlRow[Merchant_C::_col_phone],
                        $sqlRow[Merchant_C::_col_commercial_url],
                        $shopPage,
                        $sqlRow[Merchant_C::_col_web_path], $category
                    );
                }
            }
        }
        return null;
    }

    private static function _allKeyExist($sqlRow) : bool
    {

        if(is_array($sqlRow)){
            foreach (self::ARRAY_ROW_KEY as $key){
                if (array_key_exists($key, $sqlRow) === false){
                    return false;
                }
            }
            return true;
        }
        return false;
    }


    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    /**
     * @var string
     */
    private $merchantId, $merchantName, $displayText, $postCode, $address, $phone, $shopUrl, $shopPage, $logoSrc, $category;


    /**
     * Eshop constructor.
     *
     * @param string $merchantId
     * @param string $merchantName
     * @param string $displayText
     * @param string $postCode
     * @param string $address
     * @param string $phone
     * @param string $shopUrl
     * @param string $shopPage
     * @param        $logoSrc
     * @param        $category
     */
    private function __construct(
            string $merchantId, string $merchantName, string $displayText, string $postCode,
            string $address, string $phone, string $shopUrl, string $shopPage, $logoSrc, string $category
    )
    {
        $this->merchantId = $merchantId;
        $this->merchantName = $merchantName;
        $this->displayText = $displayText;
        $this->postCode = $postCode;
        $this->address = $address;
        $this->phone = $phone;
        $this->shopUrl = $shopUrl;
        $this->shopPage = $shopPage;
        $this->logoSrc = String_Helper::_isStringNotEmpty($logoSrc) ? $logoSrc : "";
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @param string $merchantId
     *
     * @return Eshop
     */
    public function setMerchantId(string $merchantId): Eshop
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantName(): string
    {
        return $this->merchantName;
    }

    /**
     * @param string $merchantName
     *
     * @return Eshop
     */
    public function setMerchantName(string $merchantName): Eshop
    {
        $this->merchantName = $merchantName;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayText(): string
    {
        return $this->displayText;
    }

    /**
     * @param string $displayText
     *
     * @return Eshop
     */
    public function setDisplayText(string $displayText): Eshop
    {
        $this->displayText = $displayText;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostCode(): string
    {
        return $this->postCode;
    }

    /**
     * @param string $postCode
     *
     * @return Eshop
     */
    public function setPostCode(string $postCode): Eshop
    {
        $this->postCode = $postCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return Eshop
     */
    public function setAddress(string $address): Eshop
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return Eshop
     */
    public function setPhone(string $phone): Eshop
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getShopUrl(): string
    {
        return $this->shopUrl;
    }

    /**
     * @param string $shopUrl
     *
     * @return Eshop
     */
    public function setShopUrl(string $shopUrl): Eshop
    {
        $this->shopUrl = $shopUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getShopPage(): string
    {
        return $this->shopPage;
    }

    /**
     * @param string $shopPage
     *
     * @return Eshop
     */
    public function setShopPage(string $shopPage): Eshop
    {
        $this->shopPage = $shopPage;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogoSrc(): ?string
    {
        return $this->logoSrc;
    }

    /**
     * @param string $logoSrc
     *
     * @return Eshop
     */
    public function setLogoSrc(string $logoSrc): Eshop
    {
        $this->logoSrc = $logoSrc;
        return $this;
    }

    public function isLogoSrcValid() : bool
    {
        return is_string($this->getLogoSrc()) && File::_fileExist($this->getLogoSrc());
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

    /**
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }


    /**
     * @method void _displayItem
     * @param $eshop mixed|Eshop
     * @return void
     */
    public static function _displaySearchItem($eshop){
        if($eshop instanceof self){
            $image = $eshop->isLogoSrcValid() ? $eshop->getLogoSrc() : "";


        ?>  <div class="col-12 col-sm-6 col-md-4 col-lg-3 m-b-10">
                <a href="<?php echo $eshop->getShopPage() ?>" class="">
                    <div class="portfolio-item img-zoom ct-photography ct-media ct-branding ct-Media elevation" style="height: 100%">
                        <div class="portfolio-item-wrap">
                            <div class="portfolio-image">
                                <img src="<?php echo $image ?>" alt="" class="">
<!--                                <div class="portfolio-banner portfolio-promo">-->
<!--                                    promo-->
<!--                                </div>-->
<!--                                <div class="portfolio-label portfolio-promo">-10% (code : PROMO)</div>-->
                            </div>
                            <div class="portfolio-description">
                                commander
                            </div>
                        </div>
                        <div class=" p-h-10">
                            <h5 class="text-center title text-xs p-v-10"><?php echo $eshop->getMerchantName() ?></h5>
                            <div class="d-none d-md-block p-v-10">
                                <p class="description text-xxs"><?php echo $eshop->getDisplayText() ?></p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php
        }
    }
}