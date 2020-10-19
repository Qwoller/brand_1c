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
?>
