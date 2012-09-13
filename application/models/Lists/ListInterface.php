<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
interface Application_Model_Lists_ListInterface {
    public function get_id();
    
    public function get_name();
    
    public function __toString();
}
?>