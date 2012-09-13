<?php
class  Erga_Form_Position extends Dnna_Form_SubFormBase {
    protected $_partnerFieldsCount = 20;
    protected $_project;

    public function __construct($view = null, $project = null) {
        $this->_project = $project;
        parent::__construct($view);
    }

    public function init() {
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/erga/projectposition.js', 'text/javascript'));
        // financialdetailsid
        if($this->getElement('positionid') == null) {
            $this->addElement('hidden', 'positionid', array(
                'readonly' => true,
                )
            );
        }
        
        // Θέση του ΤΕΙ Αθήνας στο έργο
        $subform = new Dnna_Form_SubFormBase();
        $subform->addElement('select', 'teirole', array(
            'label' => 'Θέση του ΤΕΙ Αθήνας στο έργο:',
            'required' => true,
            'multiOptions' => Array(0 => 'Τελικός Δικαιούχος', 1 => 'Ανάδοχος', 2 => 'Εταίρος', 3 => 'Υπεργολάβος'),
        ));
        // ΤΕΙ Αθήνας Συντονιστής
        $subform->addElement('checkbox', 'teiiscoordinator', array(
            'label' => 'ΤΕΙ Αθήνας συντονιστής;',
            'checked' => 'true',
            'class' => 'unique',
        ));
        $this->addSubForm($subform, 'default');
        $this->getSubForm('default')->setLegend('Θέση του ΤΕΙ Αθήνας στο έργο');
        $this->getSubForm('default')->getElement('teiiscoordinator')->getDecorator('elementDiv')->setOption('class', 'unique');
        
        $this->addSubForm(new Erga_Form_Subforms_Anadoxos(), 'anadoxos');
        foreach($this->getSubForm('anadoxos')->getElements() as $curElement) {
            $curElement->setRequired(true);
        }
        $this->getSubForm('anadoxos')->getElement('id')->setRequired(false);
        
        // Συνεργαζόμενοι Φορείς
        $subform = new Dnna_Form_SubFormBase();
        for($i = 1; $i <= $this->_partnerFieldsCount; $i++) {
            $subform->addSubForm(new Erga_Form_Subforms_Partner($i), $i, null, 'position-partners');
            $subform->getSubForm($i)->setAttrib('class', 'partner');
            $subform->getSubForm($i)->getElement('iscoordinator')->getDecorator('elementDiv')->setOption('class', 'unique');
        }

        $subform->addElement('button', 'addPartner', array(
            'label' => 'Προσθήκη Νέου Φορέα',
            'class' => 'partnerbuttons addButton',
        ));
        $this->addSubForm($subform, 'partners');
        $this->getSubForm('partners')->setLegend('Συνεργαζόμενοι Φορείς');
        // Τέλος Συνεργαζόμενων Φορέων
    }
    
    public function isValid($data) {
        // Bug fix για να λειτουργεί σωστά το API
        if(!isset($data['position']) && isset($data['default']['teiiscoordinator'])) {
            $data['position'] = $data;
        }
        // Κλειδώνουμε το teirole γιατί θα αφαιρεθούν στοιχεία από τη φόρμα.
        $value = $data['position']['default']['teirole'];
        if($value != null) {
            $options = $this->getSubForm('default')->getElement('teirole')->getMultiOptions();
            $this->getSubForm('default')->getElement('teirole')->setMultiOptions(array($value => $options[$value]));
            $this->getSubForm('default')->getElement('teirole')->setAttrib('readonly', 'true');
        }
        // 1. Αφαιρούμε είτε τα στοιχεία αναδόχου φορέα είτε τους συντονιστές ανάλογα με την τιμή του πεδίου TEIrole
        if($data['position']['default']['teirole'] == 0 || $data['position']['default']['teirole'] == 1 || $data['position']['default']['teirole'] == 2) {
            unset($data['position']['anadoxos']);
            $this->getSubForm('anadoxos')->setRequired(false);
        } else if($data['position']['default']['teirole'] == 3) {
            unset($data['position']['partners']);
            $this->getSubForm('partners')->setRequired(false);
        }
        if(isset($data['position']['partners'])) {
            // 2. Αφαιρούμε τα extra πεδία αν δεν είναι συντονιστής
            if(isset($data['position']['partners'][0])) {
                array_splice($data['position']['partners'], 0, 0, '');
            }
            $i = 1;
            while(isset($data['position']['partners'][$i])) {
                if(!isset($data['position']['partners'][$i]['iscoordinator']) || $data['position']['partners'][$i]['iscoordinator'] != 1) {
                    $this->getSubForm('partners')->getSubForm($i)->setRequired(false);
                }
                $i++;
            }
            // 3. Ελέγχουμε ότι δεν υπάρχουν 2 συνονιστές
            $i = 1;
            $foundchecked = false;
            if(isset($data['position']['default']['teiiscoordinator']) && $data['position']['default']['teiiscoordinator'] == 1) {
                $foundchecked = true;
            }
            $i = 1;
            while(isset($data['position']['partners'][$i])) {
                if(isset($data['position']['partners'][$i]['iscoordinator']) && $data['position']['partners'][$i]['iscoordinator'] == 1) {
                    if(!$foundchecked) {
                        $foundchecked = true;
                    } else {
                        return false; // Βρέθηκαν 2 συντονιστές άρα η φόρμα δεν είναι έγκυρη
                    }
                }
                $i++;
            }
        }
        return parent::isValid($data);
    }
}
?>