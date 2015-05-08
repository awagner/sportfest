<?php
require_once('config.php');

function getRequiredParam($key) {
    if (empty($_GET[$key])) {
        echo "Parameter {$key} erforderlich";
        die;
    }
    return $_GET[$key];
}

function getOptionalParam($key) {
    if (empty($_GET[$key])) {
        return "";
    }
    return $_GET[$key];
}


/** Aufruf zur Auswertung von Ort und Strassen */
function CurlGet($sURL) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_URL, $sURL);
//curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER,array('HOST: www.kabeldeutschland.de/shop/verfuegbarkeit/adresse'));
//curl_setopt($ch, CURLOPT_COOKIE, "APIstate=".$apikey);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $sMessage);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:19.0) Gecko/20100101 Firefox/19.0");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

    $sResult = curl_exec($ch);
    if (curl_errno($ch)) {
// Fehlerausgabe
        print curl_error($ch);
    } else {
// Kein Fehler, Ergebnis zurückliefern:
        curl_close($ch);
        return $sResult;
    }
}


// $sURL = Internetseite, die aufgerufen werden soll
// $sMessage = Array mit POST-Variablen (optional)
function CurlPost($sURL,$sMessage = "", $apikey) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_URL, $sURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER,array('HOST: www.kabeldeutschland.de/shop/verfuegbarkeit/adresse'));
    curl_setopt($ch, CURLOPT_COOKIE, "APIstate=".$apikey);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $sMessage);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:19.0) Gecko/20100101 Firefox/19.0");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

    $sResult = curl_exec($ch);
    if (curl_errno($ch)) {
// Fehlerausgabe
        print curl_error($ch);
    } else {
// Kein Fehler, Ergebnis zurückliefern:
        curl_close($ch);
        return $sResult;
    }
}

function createCookie() {

    $ckfile = substr(__FILE__, 0, strrpos(__FILE__, 'apicall.php'))."cookie.txt";
    if (file_exists($ckfile)) unlink($ckfile);

    $ch = curl_init ("www.kabeldeutschland.de/shop/verfuegbarkeit/adresse");
    curl_setopt($ch, CURLOPT_HTTPHEADER,array('HOST: www.kabeldeutschland.de/shop/verfuegbarkeit/adresse'));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt ($ch, CURLOPT_COOKIEJAR, $ckfile);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
    $output = curl_exec ($ch);
}


function readCookie() {

    $ckfile = substr(__FILE__, 0, strrpos(__FILE__, 'apicall.php'))."cookie.txt";

    $lines = file($ckfile);

// iterate over lines
    foreach($lines as $line) {

        // we only care for valid cookie def lines
        if($line[0] != '#' && substr_count($line, "\t") == 6) {

            // get tokens in an array
            $tokens = explode("\t", $line);

            // trim the tokens
            $tokens = array_map('trim', $tokens);
            if (isset($tokens[5]) && ($tokens[5] == 'APIstate')) {
                if (isset($tokens[6])) echo $tokens[6];
                die;
            }
        }
    }
}

function getResult($adress, &$returntype) {
//Postparameter

    $apikey = (!empty($_SESSION['apikey']))? $_SESSION['apikey'] : "b0aa002e-baba97f6-b0aa01b9-baba97f6-00000005-842lqfesnkifrbdc0cmivvtmmhlsdqqq4nr1qg8ujquun8e805ps69lk7llas2bcipfso7m3js5kujovlukjv0ggd00h41rpsktcf43";

    $postdata = array();
    $postdata['listindex'] = (isset($adress->listindex))? $adress->listindex : "";
    $postdata['zipcode'] = (isset($adress->zipcode))? $adress->zipcode : "";
    $postdata['city'] = (isset($adress->city))? $adress->city : "";
    $postdata['street'] = (isset($adress->street))? $adress->street : "";
    $postdata['housenumber'] = (isset($adress->housenumber))? $adress->housenumber : "";
    $postdata['housenumberextra'] = (isset($adress->housenumberextra))? $adress->housenumberextra : "";
    $postdata['addressList'] = (isset($adress->listindex))? $adress->listindex : "";
    $postdata['addressSubmit'] = 'addressSubmit';

//erster Post
    $inhalt = CurlPost("https://www.kabeldeutschland.de/shop/verfuegbarkeit/adresse", $postdata, $apikey);

    if (preg_match_all('/addressesList = \'([^<]*)\'/i', $inhalt, $matches)) {


        $adresseslist = json_decode($matches[1][0]);

//bestimme korrekten Listindex
        foreach ($adresseslist as $listitem) {

            if (empty($housenumber)) continue;

            if ($housenumber == $listitem->housenumber) {

                if ($housenumberextra == $listitem->housenumberextra) {
                    $postdata['listindex'] = $listitem->listindex;
                    $postdata['addressList'] = $listitem->listindex;

                    $inhalt = CurlPost("https://www.kabeldeutschland.de/shop/verfuegbarkeit/adresse", $postdata, $apikey);

                    $teil1 = substr($inhalt, 0, strpos($inhalt, '<body'));
                    $teil2 = substr($inhalt, strpos($inhalt, '<!-- DIVO-Change-Ende -->'));

//Test, ob erfolgreich
                    if (strpos($inhalt, '<div id="vpResult">') === false) {

                        echo date('d.m.Y H:i:s', time()).": ";
                        echo "APIKEY muss m&ouml;glicherweise erneuert werden!" ;

                    }
                    $returntype = "html";
                    return $teil1.$teil2;
                }
            }
        }

//falls nicht gefunden JSON-Objekt ausgeben...
        $returntype = "json";
        return $matches[1][0];

    } else {

        $teil1 = substr($inhalt, 0, strpos($inhalt, '<body'));
        $teil2 = substr($inhalt, strpos($inhalt, '<!-- DIVO-Change-Ende -->'));
        $returntype = "html";
        return $teil1.$teil2;
    }
}

/** ermittelt die Orte, die für die Postleitzahl code hinterlegt ist
 *
 * @global object $db, das globale Datenbank-Objekt
 * @global String $apicallurl, die URL von Kabeldeutschland zum Auslesen des JSON-Objektes für den Ort
 * @param String $code, die Postleitzahl
 * @return array, eine Liste von Orten.
 */
function getCities($code) {
    global $db, $apicallurl;

    $sUrl = $apicallurl."term=city&action=city&code={$code}";
    $json = CurlGet($sUrl);

    $cities = json_decode($json); //Array von Orten zur Postleitzahl

    if (count($cities) == 0) {
        echo "[".time()."] Ort {$code} unbekannt";
    }

    return $cities;
}

/** ermittelt die Strassen, die für einen Ort hinterlegt sind und speichert diese
 * in der Datenbanktabelle strassen ab.
 *
 * @global object $db, das globale Datenbank-Objekt
 * @global String $apicallurl, die URL von Kabeldeutschland zum Auslesen des JSON-Objektes für den Ort
 * @param String $code, die Postleitzahl
 * @param array $cities, eine Liste von Ortsobjekten
 * @return array, eine Liste von Einträgen in der Tabelle strassen.
 */
function getStrassen($code, $cities) {

    global $db, $apicallurl, $sesskey;

    $buchstaben = array("A", "Ö", "B", "C", "D", "E", "F", "G", "H", "I", "J",
            "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
            "U", "V", "W", "X", "Y", "Z",  "Ä", "Ü");

    foreach ($cities as $city) {

        $sUrl = $apicallurl."term=streets&action=streets&code={$code}&city=".urlencode($city->value);

        $json = CurlGet($sUrl);

        $strassen = json_decode($json);

        //ermittle letzten Buchstaben
        $letztestrasse = end($strassen);
        $searchindex = array_search($letztestrasse->value[0], $buchstaben);

        while ($searchindex < count($buchstaben)) {

            $buchstabe = $buchstaben[$searchindex];
            $sUrl = $apicallurl."term=streets&action=streets&code={$code}&city=".urlencode($city->value)."&street=".$buchstabe;
            $json = CurlGet($sUrl);
            $temp = json_decode($json);

            if (count($temp) > 0) $strassen = array_merge($strassen, $temp);
            $searchindex++;
        }

        if (count($strassen) == 0) {
            echo "[".time()."] Strassen konnten nicht ermittelt werden.";
        }

        foreach ($strassen as $strasse) {

            $data = new stdClass();
            $data->code = $code;
            $data->city = $city->value;
            $data->street = $strasse->value;
            $data->sesskey = $sesskey;

//prüfen, ob Ort vorhanden
            $sql = "SELECT id FROM strassen WHERE code = {$data->code} AND city = '{$data->city}' AND street = '{$data->street}'";
            $db->setQuery($sql);
            $id = $db->loadResult();

            if ($id) {//falls der Ort vorhanden ist session updaten.

                $data->id = $id;
                $db->updateObject('strassen', $data,'id');
                echo "[".date('H:i:s', time())."] Strasse ".$data->code." ".$data->city." ".$data->street." "."aktualisiert\n";

            } else {

                $db->insertObject('strassen', $data);
                echo "[".date('H:i:s', time())."] Strasse ".$data->code." ".$data->city." ".$data->street." "."eingefügt\n";
            }
        }
    }
    return $strassen;
}


function _saveAdress($adresse, &$aktualisiert, &$eingefuegt) {

    global $db, $wwwroot;

    //prüfen, ob Adresse schon in DB vorhanden
    $sql = "SELECT id FROM adressen ".
            "WHERE zipcode = {$adresse->zipcode} ".
            "AND city = '{$adresse->city}' ".
            "AND district = '{$adresse->district}' ".
            "AND street = '{$adresse->street}' ".
            "AND housenumber = '{$adresse->housenumber}' ".
            "AND housenumberextra = '{$adresse->housenumberextra}' ".
            "AND addressid = '{$adresse->attributes->addressid}' ".
            "AND region = '{$adresse->attributes->region}' ";

    $db->setQuery($sql);
    $id = $db->loadResult();
    $adresse->region = $adresse->attributes->region;
    $adresse->addressid = $adresse->attributes->addressid;

    unset($adresse->attributes);

    if ($id) {//falls

        $adresse->id = $id;
        $db->updateObject('adressen', $adresse,'id');
        $aktualisiert++;

    } else {

        $db->insertObject('adressen', $adresse);
        $eingefuegt++;
    }
}

/** holt die Ergebnisse für eine Strasse
 *
 * @global object $db, das globale Datenbank-Objekt
 * @global String $wwwroot, Pfad zum Web-Rootverzeichnis
 * @param String $code,m Postleitzahl
 * @param String $street, Strasse
 */
function getDatenProStrasse($code, $street) {
    global $db, $wwwroot;

    //hole den Eintrag aus der Strassentabelle
    $sql = "SELECT * FROM strassen WHERE code = '{$code}' AND street = '{$street}'";
    $db->setQuery($sql);
    $data = $db->loadObjectList();

    //falls es gleichlautende Strassen in verschiedenen Orten zur gleichen Postleitzahl gibt, besteht
    //$data aus mehreren Objekten.
    foreach ($data as $date) {

        $aktualisiert = 0;
        $eingefuegt = 0;

//index.php?zipcode=93133&city=Burglengenfeld&street=Franz-Liszt-Str.
//$sUrl = urlencode($wwwroot."index.php?zipcode={$code}&{$date->city}&{$date->strasse}");
        $returntype = '';
        $json = getResult($date, $returntype);

        //Falls ein Ergebnis ohne Hausnummer zurückgegeben wird, könnte dieses auch schon gültig sein!
        if (strpos($json, '<div id="vpResult">') !== false) {
            
            $adresse = new stdClass();
            $adresse->zipcode = $date->code;
            $adresse->city = $date->city;
            $adresse->district = "";
            $adresse->street = $date->street;
            $adresse->housenumber = "";
            $adresse->housenumberextra = "";
            $adresse->attributes->addressid = "";
            $adresse->attributes->region = "";
            $adresse->result = $json;
            _saveAdress($adresse, $aktualisiert, $eingefuegt);
            echo "[".date('H:i:s', time())."] ".$adresse->street." [".$adresse->city."] ($aktualisiert aktualisiert; $eingefuegt eingefügt)\n";

        } else {

            //alle Adressen dieser Strasse holen.
            $adressen = json_decode($json);

            if (!is_array($adressen)) return;

            //Resultate holen
            foreach ($adressen as $adresse) {

                $adresse->result = getResult($adresse, $returntype);
                _saveAdress($adresse, $aktualisiert, $eingefuegt);
            }

            echo "[".date('H:i:s', time())."] ".$adresse->street." [".$adresse->city."] ($aktualisiert aktualisiert; $eingefuegt eingefügt)\n";
        }
    }
}

/** holt die JSON-codierten Strassen */
function loadStreets($code) {
    global $db;

    $sql = "SELECT * FROM strassen WHERE code = '{$code}' ";
    $db->setQuery($sql);
    $data = $db->loadObjectList();

    return json_encode($data);

}

//++++++++ Hauptprogramm +++++++++++++++++++++++++++++++++++++++++++++++++++++++

//APIKey setzen:
$apiparam = getOptionalParam('apikey');
if (!empty($apiparam)) {
    $_SESSION['apikey'] = $apiparam;
    echo "Neuer Session APIkey gesetzt: ".$_SESSION['apikey'];
    die;
}

//weitere Aktionen
$action = $_GET['action'];
$apicallurl = "https://www.kabeldeutschland.de/static/services/streetcode/?";

switch ($action) {

    case "createCookie" :
        createCookie();
        break;

    case "readCookie" :
        readCookie();
        break;

    case "city" :
        $code = getRequiredParam('code');
        $cities = getCities($code);

        break;

    case "streets":

        $code = getRequiredParam('code');
        $cities = getCities($code);
        $strassen = getStrassen($code, $cities);

        break;

    case "results" :
        $code = getRequiredParam('code');
        $street = getRequiredParam('street');
        getDatenProStrasse($code, $street);

        break;

    case "json" :
        $code = getRequiredParam('code');
        $type = getRequiredParam('type');
        if ($type=='streets') echo loadStreets($code);
        break;

    default:
        echo "Parameter action erforderlich";
        break;
}
?>