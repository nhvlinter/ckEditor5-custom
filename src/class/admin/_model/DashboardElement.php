<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 30-06-20
 * Time: 00:20
 */

namespace salesteck\_base;


class DashboardElement implements \JsonSerializable
{
    private $title, $urlLink, $items, $activeItems;

    public static function _inst(Page $page, int $items, int $activeItems = -1) : ? self
    {

        if($page instanceof Page && $page->isEnable()){
            return new self($page, $items, $activeItems);
        }
        return null;
    }


    /**
     * DashboardElement constructor.
     * @param Page $page
     * @param int $items
     * @param int $activeItems
     * @internal param string $urlLink
     */
    private function __construct(Page $page, int $items, int $activeItems = -1)
    {
        $this->title = ucfirst($page->getText());
        $this->items = $items >= 0 ? $items : 0;
        $this->activeItems = $activeItems ;
        $this->urlLink = $page->getRoute();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getUrlLink(): string
    {
        return $this->urlLink;
    }

    /**
     * @return int
     */
    public function getItems(): int
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getActiveItems(): int
    {
        return $this->activeItems;
    }





    /**
     * print dashboard element
     */
    public function _print(){
        ?>
        <div class="col-sm-6 col-md-3 m-t-20">
            <a href="<?php echo $this->getUrlLink()?>" target="_blank" class="">
                <div class="dashboard-element p-25 elevation text-uppercase">


                    <h5 >
                        <?php echo $this->getTitle()?>
                    </h5>
                    <div>
                        <?php echo $this->getItems()?> élément(s)
                    </div>
                    <?php
                    if($this->getActiveItems() > -1){
                        ?>
                        <div>
                            actifs : <?php echo $this->getActiveItems()?>/<?php echo $this->getItems()?>
                        </div>
                        <?php
                    }
                    ?>

                </div>
            </a>
        </div>

        <?php
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