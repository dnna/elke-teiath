<?php

namespace DoctrineProxies;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Erga_Model_SubProjectProxy extends \Erga_Model_SubProject implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    
    public function get_parentproject()
    {
        $this->__load();
        return parent::get_parentproject();
    }

    public function set_parentproject($_parentproject)
    {
        $this->__load();
        return parent::set_parentproject($_parentproject);
    }

    public function get_isvirtual()
    {
        $this->__load();
        return parent::get_isvirtual();
    }

    public function set_isvirtual($_isvirtual)
    {
        $this->__load();
        return parent::set_isvirtual($_isvirtual);
    }

    public function get_subprojectid()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["_subprojectid"];
        }
        $this->__load();
        return parent::get_subprojectid();
    }

    public function set_subprojectid($_subprojectid)
    {
        $this->__load();
        return parent::set_subprojectid($_subprojectid);
    }

    public function get_subprojectnumber()
    {
        $this->__load();
        return parent::get_subprojectnumber();
    }

    public function set_subprojectnumber($_subprojectnumber)
    {
        $this->__load();
        return parent::set_subprojectnumber($_subprojectnumber);
    }

    public function get_subprojecttitle()
    {
        $this->__load();
        return parent::get_subprojecttitle();
    }

    public function set_subprojecttitle($_subprojecttitle)
    {
        $this->__load();
        return parent::set_subprojecttitle($_subprojecttitle);
    }

    public function get_subprojecttitleen()
    {
        $this->__load();
        return parent::get_subprojecttitleen();
    }

    public function set_subprojecttitleen($_subprojecttitleen)
    {
        $this->__load();
        return parent::set_subprojecttitleen($_subprojecttitleen);
    }

    public function get_subprojectsupervisor()
    {
        $this->__load();
        return parent::get_subprojectsupervisor();
    }

    public function set_subprojectsupervisor($_subprojectsupervisor)
    {
        $this->__load();
        return parent::set_subprojectsupervisor($_subprojectsupervisor);
    }

    public function get_subprojectdescription()
    {
        $this->__load();
        return parent::get_subprojectdescription();
    }

    public function set_subprojectdescription($_subprojectdescription)
    {
        $this->__load();
        return parent::set_subprojectdescription($_subprojectdescription);
    }

    public function get_subprojectbudget()
    {
        $this->__load();
        return parent::get_subprojectbudget();
    }

    public function set_subprojectbudget($_subprojectbudget)
    {
        $this->__load();
        return parent::set_subprojectbudget($_subprojectbudget);
    }

    public function get_subprojectbudgetfpa()
    {
        $this->__load();
        return parent::get_subprojectbudgetfpa();
    }

    public function set_subprojectbudgetfpa($_subprojectbudgetfpa)
    {
        $this->__load();
        return parent::set_subprojectbudgetfpa($_subprojectbudgetfpa);
    }

    public function get_subprojectbudgetwithfpa()
    {
        $this->__load();
        return parent::get_subprojectbudgetwithfpa();
    }

    public function get_subprojectstartdate()
    {
        $this->__load();
        return parent::get_subprojectstartdate();
    }

    public function set_subprojectstartdate($_startdate)
    {
        $this->__load();
        return parent::set_subprojectstartdate($_startdate);
    }

    public function get_subprojectenddate()
    {
        $this->__load();
        return parent::get_subprojectenddate();
    }

    public function set_subprojectenddate($_enddate)
    {
        $this->__load();
        return parent::set_subprojectenddate($_enddate);
    }

    public function get_subprojectdirectlabor()
    {
        $this->__load();
        return parent::get_subprojectdirectlabor();
    }

    public function set_subprojectdirectlabor($_subprojectdirectlabor)
    {
        $this->__load();
        return parent::set_subprojectdirectlabor($_subprojectdirectlabor);
    }

    public function get_competition()
    {
        $this->__load();
        return parent::get_competition();
    }

    public function set_competition($_competition)
    {
        $this->__load();
        return parent::set_competition($_competition);
    }

    public function get_employees()
    {
        $this->__load();
        return parent::get_employees();
    }

    public function set_employees($_employees)
    {
        $this->__load();
        return parent::set_employees($_employees);
    }

    public function get_contractors()
    {
        $this->__load();
        return parent::get_contractors();
    }

    public function set_contractors($_contractors)
    {
        $this->__load();
        return parent::set_contractors($_contractors);
    }

    public function get_workpackages()
    {
        $this->__load();
        return parent::get_workpackages();
    }

    public function get_workpackagesNatsort()
    {
        $this->__load();
        return parent::get_workpackagesNatsort();
    }

    public function set_workpackages($_workpackages)
    {
        $this->__load();
        return parent::set_workpackages($_workpackages);
    }

    public function get_comments()
    {
        $this->__load();
        return parent::get_comments();
    }

    public function set_comments($_comments)
    {
        $this->__load();
        return parent::set_comments($_comments);
    }

    public function createVirtualWorkPackage()
    {
        $this->__load();
        return parent::createVirtualWorkPackage();
    }

    public function getVirtualWorkPackage()
    {
        $this->__load();
        return parent::getVirtualWorkPackage();
    }

    public function isComplete()
    {
        $this->__load();
        return parent::isComplete();
    }

    public function getCompletionDate()
    {
        $this->__load();
        return parent::getCompletionDate();
    }

    public function hasOverdueDeliverables()
    {
        $this->__load();
        return parent::hasOverdueDeliverables();
    }

    public function getWorkpackagesSumAmount()
    {
        $this->__load();
        return parent::getWorkpackagesSumAmount();
    }

    public function getWorkpackagesSumAmountGreekFloat()
    {
        $this->__load();
        return parent::getWorkpackagesSumAmountGreekFloat();
    }

    public function save()
    {
        $this->__load();
        return parent::save();
    }

    public function __toString()
    {
        $this->__load();
        return parent::__toString();
    }

    public function get_employeesAs2dArray()
    {
        $this->__load();
        return parent::get_employeesAs2dArray();
    }

    public function findEmployeeByEmployee(\Application_Model_Employee $employee)
    {
        $this->__load();
        return parent::findEmployeeByEmployee($employee);
    }

    public function get_employeeSurnamesAs2dArray()
    {
        $this->__load();
        return parent::get_employeeSurnamesAs2dArray();
    }

    public function get_contractorsAs2dArray()
    {
        $this->__load();
        return parent::get_contractorsAs2dArray();
    }

    public function findContractorByAgency(\Application_Model_Contractor $agency)
    {
        $this->__load();
        return parent::findContractorByAgency($agency);
    }

    public function __set($name, $value)
    {
        $this->__load();
        return parent::__set($name, $value);
    }

    public function __get($name)
    {
        $this->__load();
        return parent::__get($name);
    }

    public function get__classname()
    {
        $this->__load();
        return parent::get__classname();
    }

    public function set__classname($__classname)
    {
        $this->__load();
        return parent::set__classname($__classname);
    }

    public function setOptions(array $options, $ignoreisvisible = false)
    {
        $this->__load();
        return parent::setOptions($options, $ignoreisvisible);
    }

    public function getOptions($onlyDbFields = true, $poptions = array (
))
    {
        $this->__load();
        return parent::getOptions($onlyDbFields, $poptions);
    }

    public function toArray($onlyDbFields = true, $recursive = false, $options = array (
))
    {
        $this->__load();
        return parent::toArray($onlyDbFields, $recursive, $options);
    }

    public function remove()
    {
        $this->__load();
        return parent::remove();
    }

    public function getMetaOptions()
    {
        $this->__load();
        return parent::getMetaOptions();
    }

    public function getOptionsAsStrings($where = 'object', $dbfieldsonly = false, $curObject = NULL, &$variables = array (
), $visited = array (
))
    {
        $this->__load();
        return parent::getOptionsAsStrings($where, $dbfieldsonly, $curObject, $variables, $visited);
    }

    public function modifySubCollection($newcollection, &$oldcollection)
    {
        $this->__load();
        return parent::modifySubCollection($newcollection, $oldcollection);
    }


    public function __sleep()
    {
        return array('__isInitialized__', '_subprojectid', '_parentproject', '_isvirtual', '_subprojectnumber', '_subprojecttitle', '_subprojecttitleen', '_subprojectsupervisor', '_subprojectdescription', '_subprojectbudget', '_subprojectbudgetfpa', '_subprojectstartdate', '_subprojectenddate', '_subprojectdirectlabor', '_competition', '_employees', '_contractors', '_workpackages', '_comments');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}