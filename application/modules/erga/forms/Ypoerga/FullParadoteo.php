<?php
class Erga_Form_Ypoerga_FullParadoteo extends Erga_Form_Ypoerga_Paradoteo {

    public function __construct($deliverable, $view = null) {
        parent::__construct($view, $deliverable);
    }

    public function init() {
        parent::init();
        $this->getSubForm('default')->removeSubForm('authors');
        $authors = new Dnna_Form_SubFormBase();
        $i = 1;
        foreach($this->_deliverable->get_authors() as $curAuthor) {
            $author = new Erga_Form_Subforms_Author($i, $this->_view);
            $author->set_empty(false);
            $authors->addSubForm($author, $i++);
        }
        $this->addSubForm($authors, 'authors');
    }
}
?>