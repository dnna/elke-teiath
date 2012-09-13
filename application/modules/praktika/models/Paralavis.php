<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Praktika_Model_Repositories_Praktika") @Table(name="elke_praktika.paralavis")
 */
class Praktika_Model_Paralavis extends Praktika_Model_PraktikoBase {
    const type = "Παραλαβής";
    const formclass = "Aitiseis_Form_PraktikoParalavis";
    const template = "PraktikoParalavis";
}
?>