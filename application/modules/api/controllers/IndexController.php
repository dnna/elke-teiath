<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_IndexController extends Dnna_Controller_ApicontentsController
{
    const name = 'Ευρετήριο API';

    protected $_allowAnonymous = false;
    protected $_returnhtml = false;

    public function getAction() {
        throw new Exception('Δεν υποστηρίζεται');
    }

    public function postAction() {
        throw new Exception('Δεν υποστηρίζεται');
    }

    public function putAction() {
        throw new Exception('Δεν υποστηρίζεται');
    }

    public function deleteAction() {
        throw new Exception('Δεν υποστηρίζεται');
    }

    public function schemaAction() {
        throw new Exception('Δεν έχει οριστεί schema για το συγκεκριμένο resource.');
    }

    public function get_returnhtml() {
        return $this->_returnhtml;
    }
}
?>