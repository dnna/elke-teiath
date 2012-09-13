<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Dnna_Action_Helper_Rest_PostOrPut extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($controller, $classname, Dnna_Form_FormBase $form, $id = null) {
        $contenttype = $controller->getRequest()->getHeader('Content-Type');
        if(strpos($contenttype, 'application/xml') !== false || strpos($contenttype, 'text/xml') !== false) {
            $xmltoarray = new Dnna_Plugin_XML2Array();
            $params = $xmltoarray->xml2array($controller->getRequest()->getRawBody(), 0);
            $params = array_pop($params);
        } else if(strpos($contenttype, 'application/json') !== false) {
            $params = json_decode($controller->getRequest()->getRawBody(), true);
        } else {
            $params = $controller->getRequest()->getUserParams();
        }

        $controller->getHelper('viewRenderer')->setNoRender(TRUE);
        // If we are editing we should need to provide every field
        if(isset($id)) {
            $em = Zend_Registry::get('entityManager');
            $object = $em->getRepository($classname)->find($id);
        }
        if(!isset($object)) {
            $created = true;
            $object = new $classname();
        }
        $form->populate($object);
        //if($form->isValid(array_merge_recursive($form->getValues(), $params))) { // Φαίνεται να οδηγεί σε corruption της επιστημονικής επιτροπής
        if($form->isValid($params)) {
            $created = false;
            if(isset($created) && $created == true) {
                $object->save(); // Για να πάρει id
            }
            $object->setOptions($form->getValues());
            $object->save();
            $newurl = htmlspecialchars($controller->view->serverUrl().$controller->view->url(array('id' => $object->get_id())));
            if($created == true) {
                $this->getResponse()->setRedirect($newurl, 201); // Created
            } else {
                $this->getResponse()->setHttpResponseCode(204);
                //$this->getResponse()->setRedirect($newurl, 204); // OK (No Content)
            }
        } else {
            throw new Exception('Κάποια στοιχεία δεν συμπληρώθηκαν ή δεν είναι έγκυρα.');
            /*echo '<?xml version="1.0" encoding="UTF-8"?>
            <error>
                <code>0</code>
                <message>Κάποια στοιχεία δεν συμπληρώθηκαν ή δεν είναι έγκυρα</message>
                <details>
                </details>
                <params>
                </params>
            </error>';*/
        }
    }
}
?>