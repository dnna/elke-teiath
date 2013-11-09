<?php

namespace DoctrineProxies\__CG__;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Praktika_Model_Competition extends \Praktika_Model_Competition implements \Doctrine\ORM\Proxy\Proxy
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

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function get_subproject()
    {
        $this->__load();
        return parent::get_subproject();
    }

    public function set_subproject($_subproject)
    {
        $this->__load();
        return parent::set_subproject($_subproject);
    }

    public function get_project()
    {
        $this->__load();
        return parent::get_project();
    }

    public function get_aitisi()
    {
        $this->__load();
        return parent::get_aitisi();
    }

    public function set_aitisi($_aitisi)
    {
        $this->__load();
        return parent::set_aitisi($_aitisi);
    }

    public function get_committees()
    {
        $this->__load();
        return parent::get_committees();
    }

    public function set_committees($_committees)
    {
        $this->__load();
        return parent::set_committees($_committees);
    }

    public function get_competitiontype()
    {
        $this->__load();
        return parent::get_competitiontype();
    }

    public function set_competitiontype($_competitiontype)
    {
        $this->__load();
        return parent::set_competitiontype($_competitiontype);
    }

    public function get_procurementtype()
    {
        $this->__load();
        return parent::get_procurementtype();
    }

    public function set_procurementtype($_procurementtype)
    {
        $this->__load();
        return parent::set_procurementtype($_procurementtype);
    }

    public function get_technicalconsultant()
    {
        $this->__load();
        return parent::get_technicalconsultant();
    }

    public function set_technicalconsultant($_technicalconsultant)
    {
        $this->__load();
        return parent::set_technicalconsultant($_technicalconsultant);
    }

    public function get_offerslanguage()
    {
        $this->__load();
        return parent::get_offerslanguage();
    }

    public function set_offerslanguage($_offerslanguage)
    {
        $this->__load();
        return parent::set_offerslanguage($_offerslanguage);
    }

    public function get_offerssubmissionlocation()
    {
        $this->__load();
        return parent::get_offerssubmissionlocation();
    }

    public function set_offerssubmissionlocation($_offerssubmissionlocation)
    {
        $this->__load();
        return parent::set_offerssubmissionlocation($_offerssubmissionlocation);
    }

    public function get_offersopeningdate()
    {
        $this->__load();
        return parent::get_offersopeningdate();
    }

    public function set_offersopeningdate($_offersopeningdate)
    {
        $this->__load();
        return parent::set_offersopeningdate($_offersopeningdate);
    }

    public function get_execlocation()
    {
        $this->__load();
        return parent::get_execlocation();
    }

    public function set_execlocation($_execlocation)
    {
        $this->__load();
        return parent::set_execlocation($_execlocation);
    }

    public function get_execduration()
    {
        $this->__load();
        return parent::get_execduration();
    }

    public function set_execduration($_execduration)
    {
        $this->__load();
        return parent::set_execduration($_execduration);
    }

    public function get_paymentmethod()
    {
        $this->__load();
        return parent::get_paymentmethod();
    }

    public function set_paymentmethod($_paymentmethod)
    {
        $this->__load();
        return parent::set_paymentmethod($_paymentmethod);
    }

    public function get_responsibleperson()
    {
        $this->__load();
        return parent::get_responsibleperson();
    }

    public function set_responsibleperson($_responsibleperson)
    {
        $this->__load();
        return parent::set_responsibleperson($_responsibleperson);
    }

    public function get_refnumassignment()
    {
        $this->__load();
        return parent::get_refnumassignment();
    }

    public function set_refnumassignment($_refnumassignment)
    {
        $this->__load();
        return parent::set_refnumassignment($_refnumassignment);
    }

    public function get_assignmentdate()
    {
        $this->__load();
        return parent::get_assignmentdate();
    }

    public function set_assignmentdate($_assignmentdate)
    {
        $this->__load();
        return parent::set_assignmentdate($_assignmentdate);
    }

    public function get_refnumnotice()
    {
        $this->__load();
        return parent::get_refnumnotice();
    }

    public function set_refnumnotice($_refnumnotice)
    {
        $this->__load();
        return parent::set_refnumnotice($_refnumnotice);
    }

    public function get_noticedate()
    {
        $this->__load();
        return parent::get_noticedate();
    }

    public function set_noticedate($_noticedate)
    {
        $this->__load();
        return parent::set_noticedate($_noticedate);
    }

    public function get_execdate()
    {
        $this->__load();
        return parent::get_execdate();
    }

    public function set_execdate($_execdate)
    {
        $this->__load();
        return parent::set_execdate($_execdate);
    }

    public function get_result()
    {
        $this->__load();
        return parent::get_result();
    }

    public function set_result($_result)
    {
        $this->__load();
        return parent::set_result($_result);
    }

    public function get_refnumresultapproved()
    {
        $this->__load();
        return parent::get_refnumresultapproved();
    }

    public function set_refnumresultapproved($_refnumresultapproved)
    {
        $this->__load();
        return parent::set_refnumresultapproved($_refnumresultapproved);
    }

    public function get_resultapproveddate()
    {
        $this->__load();
        return parent::get_resultapproveddate();
    }

    public function set_resultapproveddate($_resultapproveddate)
    {
        $this->__load();
        return parent::set_resultapproveddate($_resultapproveddate);
    }

    public function get_refnumaward()
    {
        $this->__load();
        return parent::get_refnumaward();
    }

    public function set_refnumaward($_refnumaward)
    {
        $this->__load();
        return parent::set_refnumaward($_refnumaward);
    }

    public function get_awarddate()
    {
        $this->__load();
        return parent::get_awarddate();
    }

    public function set_awarddate($_awarddate)
    {
        $this->__load();
        return parent::set_awarddate($_awarddate);
    }

    public function get_competitionstage()
    {
        $this->__load();
        return parent::get_competitionstage();
    }

    public function hasDates()
    {
        $this->__load();
        return parent::hasDates();
    }

    public function setOwner($owner)
    {
        $this->__load();
        return parent::setOwner($owner);
    }

    public function __toString()
    {
        $this->__load();
        return parent::__toString();
    }

    public function get_recordid()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["_recordid"];
        }
        $this->__load();
        return parent::get_recordid();
    }

    public function set_recordid($_recordid)
    {
        $this->__load();
        return parent::set_recordid($_recordid);
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

    public function save()
    {
        $this->__load();
        return parent::save();
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
        return array('__isInitialized__', '_competitiontype', '_procurementtype', '_offerslanguage', '_offerssubmissionlocation', '_offersopeningdate', '_execlocation', '_execduration', '_paymentmethod', '_refnumassignment', '_assignmentdate', '_refnumnotice', '_noticedate', '_execdate', '_result', '_refnumresultapproved', '_resultapproveddate', '_refnumaward', '_awarddate', '_recordid', '_subproject', '_aitisi', '_committees', '_technicalconsultant', '_responsibleperson');
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
            foreach ($class->reflFields as $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}