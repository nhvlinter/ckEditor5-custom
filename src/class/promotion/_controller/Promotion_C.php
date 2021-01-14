<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 30-04-20
 * Time: 16:24
 */
namespace salesteck\promotion;



use salesteck\_interface\DbCleaner;
use salesteck\_interface\DbJoinController;
use salesteck\admin\AdminI18;
use salesteck\admin\AdminI18_C;
use salesteck\Db\Db;
use salesteck\Db\Sql;

class Promotion_C extends Db implements DbJoinController, DbCleaner
{

    public const MIN_CODE_LENGTH = 6;
    public const
        TABLE = "_promotion",
        TABLE_TRANSLATION = self::TABLE.self::_TRANSLATION
    ;

    public const
        _col_sales_type = self::_col."_sales_type"
    ;


    public const
        sales_type_take_away = 0,
        sales_type_delivery = 1
    ;

    public const arraySalesType = [
        self::sales_type_take_away,
        self::sales_type_delivery
    ];


    public const
        type_percent = 0,
        type_money = 1
    ;

    static function _getSql(): Sql
    {
        $sql = Sql::_inst(self::TABLE);
        return $sql->idColumn(self::_col_id_code);
    }

    static function _getSqlTranslation(): Sql
    {
        $sql = Sql::_inst(self::TABLE_TRANSLATION);
        return $sql->idColumn(self::_col_id);
    }

    static function _getJoinSql(): Sql
    {
        $sql = self::_getSqlTranslation();
        return $sql->innerJoin(self::TABLE_TRANSLATION, self::_col_id_code, self::TABLE, self::_col_id_code);
    }

    public static function _getUniqueId(): ? string
    {
        return parent::_createUniqueId(self::TABLE, self::_col_id_code);
    }

    public static function _getEditorOptionSalesType(string $language)
    {
        $i18n = AdminI18_C::_getInstance($language);
//        $all = AdminI18_Controller::_getValueFromKey(AdminI18::TYPE_ALL, $i18n);
        $takeaway = AdminI18_C::_getValueFromKey(AdminI18::TYPE_TAKE_AWAY, $i18n);
        $delivery = AdminI18_C::_getValueFromKey(AdminI18::TYPE_DELIVERY, $i18n);

        return [
            $takeaway => strval(self::sales_type_take_away),
            $delivery => strval(self::sales_type_delivery)
        ];
    }

    public static function _getEditorOptionType()
    {
        return [
            "%" => strval(self::type_percent),
            "€" => strval(self::type_money)
        ];
    }

    public static function _promoCodeExist(string $promoCode, string $idCode = ""){
        $sql = self::_getSql();
        $sql
            ->equal(self::TABLE, self::_col_code, $promoCode)
        ;
        if($idCode !== ""){
            $sql
                ->different(self::TABLE, self::_col_id_code, $idCode);
        }
        if($sql->select()){
            return $sql->count() > 0 ;
        }
        return true;
    }

    static function _clean(bool $debug = false)
    {
        // TODO: Implement _clean() method.
    }
}