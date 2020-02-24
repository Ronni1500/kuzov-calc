<?
const SETTING_IB = 38;
const PRICES_IB = 37;

$rsWork = CIBlockElement::GetList(
    $arOrder  = array("SORT" => "ASC"),
    $arFilter = array(
        "ACTIVE"    => "Y",
        "IBLOCK_ID" => SETTING_IB,
        "SECTION_ID"=> false
    ),
    false,
    false,
    $arSelectFields = array("ID", "NAME", "IBLOCK_ID")
);
while($arWork = $rsWork->fetch()) {
  $works[$arWork['ID']] = [];  
}


$rsType = CIBlockSection::GetList(
    $arOrder  = array("SORT" => "ASC"),
    $arFilter = array(
        "ACTIVE"    => "Y",
        "IBLOCK_ID" => SETTING_IB,
        "DEPTH_LEVEL"=> '1'
    ),
    false,
    $arSelect = array("ID", "NAME", "IBLOCK_ID"),
    false
);
while($arType = $rsType->fetch()) {
    $type[$arType['ID']] = [];
}


$result = [];
foreach($type as $key => $item){
    $rsAuto = CIBlockSection::GetList(
        $arOrder  = array("SORT" => "ASC"),
        $arFilter = array(
            "ACTIVE"    => "Y",
            "IBLOCK_ID" => PRICES_IB,
            "UF_UF_TYPE_AUTO" => $key,
            "DEPTH_LEVEL" => '1'
        ),
        false,
        $arSelect = array("ID", "NAME", "IBLOCK_ID"),
        false
    );
    //Типы авто
    while($arAuto = $rsAuto->fetch()) {

        foreach($works as $key_w => $work){
            $rsDetail = CIBlockSection::GetList(
                $arOrder  = array("SORT" => "ASC"),
                $arFilter = array(
                    "ACTIVE"    => "Y",
                    "IBLOCK_ID" => PRICES_IB,
                    "UF_UF_TYPE" => $key_w,
                    "SECTION_ID"=> $arAuto['ID']
                ),
                false,
                $arSelect = array("ID", "NAME", "IBLOCK_ID"),
                false
            );
            //Типы деталей
            while($arDetail = $rsDetail->fetch()) {

                $rsElement = CIBlockElement::GetList(
                    $arOrder  = array("SORT" => "ASC"),
                    $arFilter = array(
                        "ACTIVE"    => "Y",
                        "IBLOCK_ID" => PRICES_IB,
                        "SECTION_ID"=> $arDetail['ID']
                    ),
                    false,
                    false,
                    $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_*")
                );
                $list_w = [];
                while($arElement = $rsElement->fetch()) {
                    $tmp = [];
                    $tmp[$arElement['ID']] = [
                        'price' => $arElement['PROPERTY']['START_PRICE']['VALUE'],
                        'plus' => $arElement['PROPERTY']['ADD_FIVE_PRICE']['VALUE'],
                        'name' => $arElement['PROPERTY']['CALC_NAME']['VALUE']
                    ];
                    $list_w[$arElement['PROPERTY']['LIST_WORKINK']['VALUE']] = $tmp;
                }
                //Типы авто
                $result[$arAuto['ID']][$arDetail['ID']] = $list_w;
            }
        }    
    }  
}
$result_json = json_encode($result);

