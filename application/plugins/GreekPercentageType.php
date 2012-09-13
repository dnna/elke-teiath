<?php
namespace ELKE\Doctrine\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use \Zend_Locale_Format;
use \Zend_Registry;

class GreekPercentageType extends Type
{
    public function getName()
    {
        return 'greekpercentage';
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getFloatDeclarationSQL($fieldDeclaration);
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if($value === null || $value === "") {
            return null;
        } else {
            return Zend_Locale_Format::getNumber($value,
                                        array('precision' => 2,
                                              'locale' => Zend_Registry::get('Zend_Locale'))
                                       )/100;
        }
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if($value === null || $value === "") {
            return null;
        } else {
            return Zend_Locale_Format::toNumber($value*100,
                                        array(
                                              'precision' => 2,
                                              'locale' => Zend_Registry::get('Zend_Locale')));
        }
    }
}

Type::addType('greekpercentage', 'ELKE\Doctrine\Types\GreekPercentageType');
?>