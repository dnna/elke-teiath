<?php
class Application_Action_Helper_DeleteHelper extends Zend_Controller_Action_Helper_Abstract
{
    function direct(Zend_Controller_Action $controller, $idfield, $class, $viewentryname, $allowinnoncomplex = true) {
        if(!isset($allowinnoncomplex)) {
            $allowinnoncomplex = true;
        }
        if($this->getRequest()->getParam($idfield) == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            throw new Exception('Δεν έχει οριστεί '.$idfield);
        }

        // Έλεγχος ότι το αντικείμενο υπάρχει
        $entry = Zend_Registry::get('entityManager')->getRepository($class)->find($this->getRequest()->getParam($idfield));
        if($entry == null) {
            throw new Exception("Το συγκεκριμένο ".$idfield." δεν υπάρχει.");
        }
        // Έλεγχος ότι επιτρέπεται η διαγραφή
        $controller->view->$viewentryname = $entry;
        /*if($allowinnoncomplex == false && $controller->view->getProject()->get_iscomplex() == 0) {
            throw new Exception('Δεν είναι δυνατή αυτή η διαγραφή σε απλά έργα.');
        }*/

        $entry->remove();
        $request = $controller->getRequest();
        $controller->getHelper('flashMessenger')->addMessage('Το αντικείμενο διαγράφτηκε με επιτυχία');
        $controller->getHelper('redirector')->gotoUrlAndExit(urldecode($request->getUserParam('return'))); // Επιστρέφουμε από εκεί που ήρθαμε*/
        /*$controller->getHelper('redirector')->gotoSimple('index',
                                               $request->getControllerName(),
                                               $request->getModuleName()); // Επιστρέφουμε από εκεί που ήρθαμε*/
        return;
    }
}
?>