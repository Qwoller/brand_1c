<?
AddEventHandler( "iblock", "OnAfterIBlockElementAdd", array("aspro_import", "FillTheBrands"));
AddEventHandler( "iblock", "OnAfterIBlockElementUpdate", array("aspro_import", "FillTheBrands"));
class aspro_import {
	function FillTheBrands($arFields) {
		$dbItems = \Bitrix\Iblock\ElementTable::getList(array(
			'select' => array('ID', 'NAME', 'IBLOCK_ID'),
			'filter' => array('IBLOCK_ID' => 24, 'ID' => $arFields['ID'])
		));
		while ($arItem = $dbItems->fetch()){  
			$dbProperty = \CIBlockElement::getProperty(
				$arItem['IBLOCK_ID'],
				$arItem['ID'],
				array(),
				array('CODE' => 'CML2_MANUFACTURER')
			);
			while($arProperty = $dbProperty->Fetch()){  
				$arItem['PROPERTIES'][] = $arProperty;
			}
			if($arItem['PROPERTIES'][0]['VALUE_ENUM'] != ''){
			$dbItems2 = \Bitrix\Iblock\ElementTable::getList(array(
				'select' => array('ID', 'NAME', 'IBLOCK_ID'),
				'filter' => array('IBLOCK_ID' => 28, 'NAME' => $arItem['PROPERTIES'][0]['VALUE_ENUM'])
			));
				if ($arBrand = $dbItems2->fetch()){  
					CIBlockElement::SetPropertyValues($arItem['ID'], 24, $arBrand['ID'], 'BRAND');
				}
			}
		}
	}
}
/*Другой проект, клиент выгружал все свойства из 1С просто строкой. Необходим был обработчик котоырй объединяет 
Несколько видов трубок в один множественный список
Несколько видов резьбы F в один множественный список
Несколько видов резьб M в один множественный список
Использовалось для фильтра
http://prorotor.site-x10.ru/catalog/fitingi_universalnye/
*/
AddEventHandler( "iblock", "OnAfterIBlockElementAdd", array("aspro_import", "FillTheBrands"));
AddEventHandler( "iblock", "OnAfterIBlockElementUpdate", array("aspro_import", "FillTheBrands"));
class aspro_import {
	function FillTheBrands($arFields) {
	$dbItems = \Bitrix\Iblock\ElementTable::getList(array(
		'select' => array('ID', 'NAME', 'IBLOCK_ID'),
		'filter' => array('IBLOCK_ID' => 17, 'ID' => $arFields['ID'])
	));
	while($arItem = $dbItems->Fetch()){
		$dbProperty = \Bitrix\Iblock\PropertyTable::getList(array(
			'select' => array('ID', 'CODE', 'NAME'),
			'filter' => array(
				'LOGIC' => 'OR',
				array(
					'=CODE' => 'TRUBKA_C_MM'
				),
				array(
					'=CODE' => 'TRUBKA_C1_MM'
				),
				array(
					'=CODE' => 'TRUBKA_C2_MM'
				),
				array(
					'=CODE' => 'REZBA_F'
				),
				array(
					'=CODE' => 'REZBA_F1'
				),
				array(
					'=CODE' => 'REZBA_F2'
				),
				array(
					'=CODE' => 'REZBA_M'
				),
				array(
					'=CODE' => 'REZBA_M1'
				),
			),
		));
		while($arProperty = $dbProperty->Fetch()){  
			$dbProperty_val = \Bitrix\Iblock\ElementPropertyTable::getList(array(
				'select' => array('ID', 'VALUE'),
				'filter' => array('IBLOCK_ELEMENT_ID' => $arItem['ID'], 'IBLOCK_PROPERTY_ID' => $arProperty['ID'])
			));
			while($value = $dbProperty_val->Fetch()){
				$arProperty['VALUE'] = $value;
				$arItem['PROPS'][] = $arProperty;
			}
		}
		foreach($arItem['PROPS'] as $key => $prop){
			if($prop['CODE'] == 'TRUBKA_C_MM' || $prop['CODE'] == 'TRUBKA_C1_MM' || $prop['CODE'] == 'TRUBKA_C2_MM') {
				$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$arItem['IBLOCK_ID'], "CODE"=>"DIAM_TRUB"));
				while($enum_fields = $property_enums->GetNext())
				{
					if($enum_fields["VALUE"] == $prop['VALUE']['VALUE'])
					{
						$ar_val_diam[] = $enum_fields["ID"];
					}
				}
				if(!empty($ar_val_diam)){
					CIblockElement::SetPropertyValuesEx($arItem["ID"], $arItem["IBLOCK_ID"], ["DIAM_TRUB" => $ar_val_diam]);
					unset($ar_val_diam);
				}
			}
			if($prop['CODE'] == 'REZBA_F' || $prop['CODE'] == 'REZBA_F1' || $prop['CODE'] == 'REZBA_F2') {
				$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$arItem['IBLOCK_ID'], "CODE"=>"VNUT_REZ"));
				while($enum_fields = $property_enums->GetNext())
				{
					if($enum_fields["VALUE"] == $prop['VALUE']['VALUE'])
					{
						$ar_val_vnut[] = $enum_fields["ID"];
					}
				}
				if(!empty($ar_val_vnut)){
					CIblockElement::SetPropertyValuesEx($arItem["ID"], $arItem["IBLOCK_ID"], ["VNUT_REZ" => $ar_val_vnut]);
					unset($ar_val_vnut);
				}
			}
			if($prop['CODE'] == 'REZBA_M' || $prop['CODE'] == 'REZBA_M1') {
				$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$arItem['IBLOCK_ID'], "CODE"=>"NARUZ_REZ"));
				while($enum_fields = $property_enums->GetNext())
				{
					if($enum_fields["VALUE"] == $prop['VALUE']['VALUE'])
					{
						$ar_val_naruz[] = $enum_fields["ID"];
					}
				}
				if(!empty($ar_val_naruz)){
					CIblockElement::SetPropertyValuesEx($arItem["ID"], $arItem["IBLOCK_ID"], ["NARUZ_REZ" => $ar_val_naruz]);
					unset($ar_val_naruz);
				}
			}
		}
	}
}
?>
