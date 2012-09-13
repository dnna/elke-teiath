<?php
namespace ELKE\Doctrine\Types {
    use Doctrine\DBAL\Types\Type;
    use Doctrine\DBAL\Types\DateTimeType;
    use Doctrine\DBAL\Types\DateTimeTzType;
    use Doctrine\DBAL\Types\DateType;
    use Doctrine\DBAL\Platforms\AbstractPlatform;
    use \Zend_Locale_Format;
    use \Zend_Registry;

    class EDateTimeType extends DateTimeType
    {
        public function convertToPHPValue($value, AbstractPlatform $platform)
        {
            if ($value === null) {
                return null;
            }

            $val = \EDateTime::createFromFormat($platform->getDateTimeFormatString(), $value);
            if (!$val) {
                throw ConversionException::conversionFailed($value, $this->getName());
            }
            return $val;
        }
    }

    class EDateTimeTzType extends DateTimeTzType
    {
        public function convertToPHPValue($value, AbstractPlatform $platform)
        {
            if ($value === null) {
                return null;
            }

            $val = \EDateTime::createFromFormat($platform->getDateTimeTzFormatString(), $value);
            if (!$val) {
                throw ConversionException::conversionFailed($value, $this->getName());
            }
            return $val;
        }
    }

    class EDateType extends DateType
    {
        public function convertToPHPValue($value, AbstractPlatform $platform)
        {
            if ($value === null) {
                return null;
            }

            $val = \EDateTime::createFromFormat('!'.$platform->getDateFormatString(), $value);
            if (!$val) {
                throw ConversionException::conversionFailed($value, $this->getName());
            }
            return $val;
        }
    }

    Type::overrideType('datetime', 'ELKE\Doctrine\Types\EDateTimeType');
    Type::overrideType('datetimetz', 'ELKE\Doctrine\Types\EDateTimeTzType');
    Type::overrideType('date', 'ELKE\Doctrine\Types\EDateType');
}

namespace {
    class EDateTime extends DateTime implements ArrayAccess {
        public static $format;

        public static $timeformat;

        public static function createFromFormat($format, $time, $timezone = null) {
            $edatetime = new static();
            if(isset($timezone)) {
                $datetime = parent::createFromFormat($format, $time, $timezone);
            } else {
                $datetime = parent::createFromFormat($format, $time);
            }
            if($datetime instanceof DateTime) {
                $edatetime->setTimestamp($datetime->getTimestamp());
                return $edatetime;
            } else {
                return $datetime;
            }
        }

        public static function create($time, $timezone = null) {
            if(EDateTime::$format == null) {
                EDateTime::$format = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
                EDateTime::$format = EDateTime::$format['date']['format'];
            }
            if(!is_object($time)) {
                $edatetime = static::createFromFormat(EDateTime::$format, $time, $timezone);
                if($time == "") {
                    return null;
                } else if(!$edatetime) {
                    // Κάνουμε έναν έλεγχο μήπως η ημερομηνία ήταν σε ISO8601
                    $edatetime = static::createFromFormat(DateTime::ISO8601, $time, $timezone);
                    if(!$edatetime) {
                        throw new Exception('Λάθος μορφή σε ημερομηνία');
                    }
                }
                return $edatetime;
            } else {
                return $time;
            }
        }

        public function __toString() {
            if(EDateTime::$format == null) {
                EDateTime::$format = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
                EDateTime::$format = EDateTime::$format['date']['format'];
            }
            return (string)parent::format(EDateTime::$format);
        }
        
        public function __toStringTime() {
            if(EDateTime::$timeformat == null) {
                EDateTime::$timeformat = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
                EDateTime::$timeformat = EDateTime::$timeformat['date']['timeformat'];
            }
            return (string)parent::format(EDateTime::$timeformat);
        }

        // Implemented για να παίζει το PHPExcel
        public function offsetSet($offset, $value) {}
        public function offsetExists($var) {
            return false;
        }
        public function offsetUnset($var) {}
        public function offsetGet($var) {
            return "";
        }
    }
}
?>