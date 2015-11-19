<?php
class DateTimeFrench extends DateTime {
    public function format($format) {
        $english_days = array('Monday', 'Mon', 'Tuesday', 'Tue', 'Wednesday', 'Wed', 'Thursday', 'Thu', 'Friday', 'Fri', 'Saturday', 'Sat', 'Sunday', 'Sun');
        $french_days = array('Lundi', 'Lun', 'Mardi', 'Mar', 'Mercredi', 'Mer', 'Jeudi', 'Jeu', 'Vendredi', 'Ven', 'Samedi', 'Sam', 'Dimanche', 'Dim');
        $english_months = array('January', 'Jan', 'February', 'Feb', 'March', 'Mar', 'April', 'Apr', 'May', 'May', 'June', 'Jun', 'July', 'Jul', 'August', 'Aug', 'September', 'Sep', 'October', 'Oct', 'November', 'Nov', 'December', 'Dec');
        $french_months = array('Janvier', 'Jan', 'Février', 'Fév', 'Mars', 'Mar', 'Avril', 'Avr', 'Mai', 'Mai', 'Juin', 'Juin', 'Juillet', 'Juil', 'Août', 'Aou', 'Septembre', 'Sep', 'Octobre', 'Oct', 'Novembre', 'Nov', 'Décembre', 'Dec');
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, parent::format($format)));
    }
}

$date = new DateTime('now');
for ($i=0; $i< 12; $i++) {
    echo $date->format('D d M, l d F H:i:s');
    echo "\n";

    $date->add(new DateInterval('P30D'));
}

echo "-----\n";

$date = new DateTimeFrench('now');
for ($i=0; $i< 12; $i++) {
    echo $date->format('D d M, l d F H:i:s');
    echo "\n";

    $date->add(new DateInterval('P30D'));
}