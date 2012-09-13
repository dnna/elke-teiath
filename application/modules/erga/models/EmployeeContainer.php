<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @MappedSuperclass
 */
abstract class Erga_Model_EmployeeContainer extends Dnna_Model_Object {
    /**
     * Επιστρέφει έναν δισδιάστατο associative πίνακα όπου σαν κλειδιά
     * χρησιμοποιούνται τα id των εργαζομένων και σαν περιεχόμενα τα
     * ονοματεπώνυμα τους.
     * @return array Δισδιάστατος πίνακας με τα id και τα ονοματεπώνυμα των
     * απασχολούμενων.
     */
    public function get_employeesAs2dArray() {
        $array = array();
        foreach($this->_employees as $curEmployee) {
            $array[$curEmployee->get_recordid()] = $curEmployee->get_employee()->get_name().' '.$curEmployee->get_startdate().'–'.$curEmployee->get_enddate();
        }
        return $array;
    }
    
    public function findEmployeeByEmployee(Application_Model_Employee $employee) {
        foreach($this->get_employees() as $curEmployee) {
            if($curEmployee->get_employee() === $employee) {
                return $curEmployee;
            }
        }
        return null;
    }
    
    /**
     * Επιστρέφει έναν δισδιάστατο associative πίνακα όπου σαν κλειδιά
     * χρησιμοποιούνται τα id των εργαζομένων και σαν περιεχόμενα τα ονόματα
     * τους.
     * @return array Δισδιάστατος πίνακας με τα id και τα όνοματα των
     * απασχολούμενων.
     */
    public function get_employeeSurnamesAs2dArray() {
        $array = array();
        foreach($this->_employees as $curEmployee) {
            $array[$curEmployee->get_recordid()] = $curEmployee->get_employee()->get_surname();
        }
        return $array;
    }

    public function get_contractorsAs2dArray() {
        $array = array();
        foreach($this->_contractors as $curContractor) {
            $array[$curContractor->get_recordid()] = $curContractor->get_agency()->get_name();
        }
        return $array;
    }

    public function findContractorByAgency(Application_Model_Contractor $agency) {
        foreach($this->_contractors as $curContractor) {
            if($curContractor->get_agency() === $agency) {
                return $curContractor;
            }
        }
        return null;
    }
}

?>
