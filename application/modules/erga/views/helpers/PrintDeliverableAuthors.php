<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_View_Helper_PrintDeliverableAuthors extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    /**
     * @param int $approved 0. Δεν έχει αποφασιστεί, 1. Εκγρίθηκε, 2. Απορρίφθηκε
     * @param array $entries Οι αιτήσεις
     */
    public function printDeliverableAuthors($curDeliverable) {
        if($curDeliverable->get_contractor() != null) {
            $output = $curDeliverable->get_contractor()->get_agency()->get_name();
        } else if($curDeliverable->get_authors() != null && $curDeliverable->get_authors()->count() > 0) {
            $authors = array();
            foreach($curDeliverable->get_authors() as $curKey => $curAuthor) {
                $authors[$curKey] = $curAuthor->get_employee()->get_employee()->get_firstnameInitial().'.'.$curAuthor->get_employee()->get_employee()->get_surname().$curAuthor->get_rateOrAmount();
            }
            $output = implode(', ', $authors);
        } else {
            $output = '-';
        }
        return $output;
    }
}
?>