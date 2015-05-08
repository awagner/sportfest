<?php
require_once ('moodlelib.php');

class sportfest {

    var $id = 0;
    var $station = 0;
    var $klasse = 0;
    var $punkte = 0;
    var $_locked = true;
    var $_errors = null;
    var $_mobile = false;

    public function __construct() {

        $lock = optional_param('lock', '-1', PARAM_INT);
        $station = optional_param('station', '0', PARAM_INT);
        

        if (!isset($_SESSION['lock']))
            $_SESSION['lock'] = 1;

        if ($lock == 1) {
            $_SESSION['lock'] = '1';
            $_SESSION['station'] = $station;
        }
        if ($lock == 0) {
            $_SESSION['lock'] = '0';
            $_SESSION['station'] = $station;
        }

        $this->_locked = ($_SESSION['lock'] == 1);
        $this->check_browser();
    }

    private function check_browser() {
        $mobile_browser = '0';

        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $mobile_browser++;
        }

        if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            $mobile_browser++;
        }

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
            'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
            'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
            'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
            'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
            'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
            'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
            'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
            'wapr', 'webc', 'winw', 'winw', 'xda ', 'xda-');

        if (in_array($mobile_ua, $mobile_agents)) {
            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['ALL_HTTP']), 'OperaMini') > 0) {
            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') > 0) {
            $mobile_browser = 0;
        }

        if ($mobile_browser > 0) {
            $this->_mobile = true;
        } else {
            $this->_mobile = false;
        }
    }

    private function check_request() {

        $errors = array();

        $this->id = optional_param('id', 0, PARAM_INT);

        $this->station = optional_param('station', 0, PARAM_INT);
        if (empty($this->station)) {
            $errors['station'] = "Bitte Station wählen";
        }

        $this->klasse = optional_param('klasse', 0, PARAM_INT);
        if (empty($this->klasse)) {
            $errors['klasse'] = "Bitte Klasse wählen";
        }

        $this->punkte = optional_param('punkte', "", PARAM_TEXT);
	    if (!isset($this->punkte) or ($this->punkte === "")) {
            $errors['punkte'] = "Bitte Punkte eingeben.";
        }

        $this->_errors = $errors;
        return count($errors);
    }

    private function save() {
        global $db;

        $sql = "SELECT * FROM ergebnisse WHERE klasse = {$this->klasse} 
            AND station = {$this->station} ";

        $db->setQuery($sql);
        $exists = $db->loadObject($data);



        if ($exists) {

            // $data->punkte = $this->punkte;
            // $data->zeit = time();
            // $db->updateObject('ergebnisse', $data, 'id');
			return false;
        } else {

            $this->zeit = time();
            $db->insertObject('ergebnisse', $this);
			return true;
        }
    }

    private function get_station_select($stationid = null) {
        global $db;


        $html = "";
        if ($this->_locked) {
            $html .= "<input type=\"hidden\" name=\"station\" value=\"{$stationid}\" />";
        }

        $sql = "SELECT * FROM stationen order by sortorder";
        $db->setQuery($sql);
        $stationen = $db->loadObjectList('id');

        $disabled = ($this->_locked) ? "disabled=\"disalbed\"" : "";

        $html .= "<select id =\"station\" name=\"station\" onchange=\"this.form.submit()\" {$disabled}>";

        $choices = array();
        $choices[0] = new stdClass();
        $choices[0]->id = 0;
        $choices[0]->name = "Station wählen";

        $choices = array_merge($choices, $stationen);

        foreach ($choices as $station) {

            $checked = ($station->id == $stationid) ? " selected=\"selected\"" : "";
            $html .= "<option value=\"{$station->id}\" {$checked} >{$station->name}</option>";
        }
        $html .= "</select>";

        if (isset($this->_errors['station'])) {
            $html .= "<span class=\"error\">{$this->_errors['station']}</span>";
        }



        return $html;
    }

    private function get_klasse_select($stationid = null, $klasseid = null) {
        global $db;

        $sql = "SELECT * FROM klassen ";

        if (!empty($stationid) and ($this->_locked)) {
            $sql .= "WHERE id NOT IN (SELECT klasse FROM ergebnisse WHERE station = {$stationid} AND punkte > '0')";
        }

        $sql .= " order by sortorder";

        $db->setQuery($sql);
        $klassen = $db->loadObjectList('id');

        $choices = array();
        $choices[0] = new stdClass();
        $choices[0]->id = 0;
        $choices[0]->name = "Klasse wählen";

        $choices = array_merge($choices, $klassen);

        $html = "<select id =\"klasse\" name=\"klasse\" onchange=\"this.form.submit()\">";
        foreach ($choices as $klasse) {

            $checked = ($klasse->id == $klasseid) ? " selected=\"selected\" " : "";
            $html .= "<option value=\"{$klasse->id}\" {$checked}>{$klasse->name}</option>";
        }
        $html .= "</select>";

        if (isset($this->_errors['klasse'])) {
            $html .= "<span class=\"error\">{$this->_errors['klasse']}</span>";
        }

        return $html;
    }

    private function edit() {
        global $wwwroot;

        echo "<h3>Ergebniseingabe</h3>";

        $this->showMessage();

        // ...get REQUEST params
        $station = optional_param('station', 0, PARAM_INT);

        if ($this->_locked) {
            $station = $_SESSION['station'];
        }


        $klasse = optional_param('klasse', 0, PARAM_INT);
        ?>
        <form method="post" action="<?php echo "{$wwwroot}index.php?task=editstation"; ?>">
            <table>
                <tr>
                    <td>
                        Station
                    </td>
                    <td>
                        <?php echo $this->get_station_select($station) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Klasse
                    </td>
                    <td>
                        <?php echo $this->get_klasse_select($station, $klasse) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Punkte
                    </td>
                    <td>
                        <input type="text" id="punkte" name="punkte" />
                        <?php
                        if (isset($this->_errors['punkte'])) {
                            echo "<span class=\"error\">{$this->_errors['punkte']}</span>";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan ="2" align="center"><input type="submit" name="save" value="Senden" /></td>
                </tr>
            </table>
        </form>
        <?php
        $this->displaystation($station, $klasse);
    }

    private function redirect($url, $msg) {
        global $wwwroot;

        $_SESSION['msg'] = $msg;
        header("Location: {$wwwroot}/{$url}");
    }

    public function showMessage() {
        global $_SESSION;

        if (!empty($_SESSION['msg'])) {
            echo "<div class=\"message\">{$_SESSION['msg']}</div>";
            unset($_SESSION['msg']);
        }
    }

    public function doTask() {

        if (!empty($_REQUEST['save'])) {
            $this->_task = "save";
        } else {
            $this->_task = optional_param('task', '', PARAM_ALPHA);
        }

        switch ($this->_task) {

            case "save" :
                if ($this->check_request() == 0) {

                    if($this->save()){
					
                    $this->redirect('index.php?task=editstation', 'Punkte gespeichert');
					}
					else{
					$this->redirect('index.php?task=editstation', 'Wert für diese Klasse schon vorhanden, Administrator kontaktieren');
					}
                } else {
                    $_SESSION['msg'] = 'Fehler!';
                    $this->_task = "editstation";
                }
                break;
        }
    }

    private function getResults($stationid = null, $klasseid = null) {
        global $db;

        $where = array();
        if (!empty($stationid))
            $where[] = " erg.station = '{$stationid}' ";
        if (!empty($klasseid))
            $where[] = " erg.klasse = '{$klasseid}' ";

        $where = (count($where) > 0) ? " WHERE " . implode(" AND ", $where) : "";

        $sql = "SELECT erg.id, s.name as station, k.name as klasse, erg.punkte, k.id as klasseid FROM ergebnisse erg 
                JOIN klassen k on k.id = erg.klasse 
                JOIN stationen s on s.id = erg.station {$where} order by s.sortorder, k.sortorder ";

        $db->setQuery($sql);
        return $db->loadObjectList();
    }

    public function displaystation($stationid = null, $klasseid = null) {

        $results = $this->getResults($stationid, $klasseid);

        $groupedresults = array();
        foreach ($results as $result) {
            if (!isset($groupedresults[$result->station]))
                $groupedresults[$result->station] = array();
            $groupedresults[$result->station][$result->id] = $result;
        }


        //Ausgabe
        if (!empty($klasseid)) {//klassenweise
            $gesamtpunkte = 0;

            $html .= "<table class=\"smalltable\">";
            $html .= "<tr class=\"ui-header ui-bar-a\"><td>Station</td><td>Punkte</td></tr>";
            foreach ($groupedresults as $stationname => $groupedresult) {

                foreach ($groupedresult as $result) {
                    $html .="<tr><td>{$stationname}</td><td>{$result->punkte}</td></tr>";
                    $gesamtpunkte += $result->punkte;
                }
            }
            $html .= "<tr class=\"sumrow\"><td>gesamt</td><td>{$gesamtpunkte}</td></tr>";
            $html .= "</table>";

            if (!empty($html) and (!empty($result->klasse))) {
                echo "<h3>Ergebnisse {$result->klasse}:</h3>";
                echo $html;
            }
        } else {

            $html = "";
            foreach ($groupedresults as $stationname => $groupedresult) {
                $html .= "<table class=\"smalltable\">";
                $html .= "<tr><td colspan=\"2\" class=\"ui-header ui-bar-a\">{$stationname}</td></tr>";
                foreach ($groupedresult as $result) {
                    $html .="<tr><td>{$result->klasse}</td><td>{$result->punkte}</td></tr>";
                }
                $html .= "</table>";
            }
            if (!empty($html)) {
                echo "<h3>Ergebnisse der Stationen</h3>";
                echo $html;
            }
        }
    }

    private function getKlassen() {
        global $db;

        $sql = "SELECT * FROM klassen order by sortorder";
        $db->setQuery($sql);
        return $db->loadObjectList('id');
    }

    private function getSummResults() {
        global $db;

        $sql = "SELECT klasse, sum(punkte) as summe, count(punkte) as anzahl FROM ergebnisse group by klasse";
        $db->setQuery($sql);
        return $db->loadObjectList('klasse');
    }

    private function getStationen() {
        global $db;

        $sql = "SELECT * FROM stationen order by sortorder";
        $db->setQuery($sql);
        return $db->loadObjectList('id');
    }

    public function display_overview() {

        $results = $this->getResults();
        $klassen = $this->getKlassen();
        $stationen = $this->getStationen();
        $gesamtpunkte = $this->getSummResults();

        $groupedresults = array();
        foreach ($results as $result) {

            if (!isset($groupedresults[$result->station])) {
                $groupedresults[$result->station] = array();
            }
            $groupedresults[$result->station][$result->klasseid] = $result->punkte;
        }

        // Tabelle splitten, falls mobiles Endgerät.
        if ($this->_mobile) $paging = 4;
        else $paging = 100;


        $html = "";
        while (count($klassen) > 0) {

            $html .= "<table class=\"overview-table\">";

            // Klassen.
            $html .= "<tr class=\"headerrow\"><td>Station</td>";

            $counter = 0;
            foreach ($klassen as $klassenid => $klasse) {

                if (isset($gesamtpunkte[$klassenid]->anzahl)) {
                    $percent = $gesamtpunkte[$klassenid]->anzahl / count($stationen);
                } else {
                    $percent = 0;
                }
                if ($percent == 0) {
                    $class = 'bg-red';
                } elseif ($percent == 100) {
                    $class = 'bg-green';
                } elseif ($percent < 50) {
                    $class = 'bg-orange';
                } else {
                    $class = 'bg-yellow';
                }

                $html .= "<td class=\"{$class}\">{$klasse->name}</td>";
                $counter++;
                if ($counter == $paging)
                    break;
            }

            //Ergebnisse.
            foreach ($stationen as $station) {
                $html .= "<tr><td>{$station->name}</td>";
                $counter = 0;
                foreach ($klassen as $klasse) {

                    $html .= (isset($groupedresults[$station->name][$klasse->id])) ? "<td>{$groupedresults[$station->name][$klasse->id]}</td>" : "<td class=\"empty\">&nbsp;</td>";
                    $counter++;
                    if ($counter == $paging)
                        break;
                }
                $html .= "</tr>";
            }

            // Summen.
            $counter = 0;
            $html .= "<tr class=\"sumrow\"><td>Gesamtpunkte</td>";
            foreach ($klassen as $klasse) {

                $html .= (isset($gesamtpunkte[$klasse->id])) ? "<td>{$gesamtpunkte[$klasse->id]->summe}</td>" : "<td class=\"empty\">&nbsp;</td>";
                $counter++;
                if ($counter == $paging)
                    break;
            }
            $html .= "</tr></table><br />";

            $klassen = array_slice($klassen, $paging);
            if (!$this->_mobile) break;
        }

        echo $html;
    }

    private function print_locklink() {
        global $wwwroot;

        $station = optional_param('station', '0', PARAM_INT);

        $lockedstr = ($this->_locked) ? " locked " : "unlocked";
        $stationstr = ($station > 0) ? "&station={$station}" : "";

        if ($this->_locked) {
            $url = $wwwroot . "index.php?task=editstation&lock=0{$stationstr}";
        } else {
            $url = $wwwroot . "index.php?task=editstation&lock=1{$stationstr}";
        }

        $html = "<p>Status: <a href=\"{$url}\">{$lockedstr}</p>";
        echo $html;
    }

    public function display() {
        global $wwwroot;

        //display menu
        $html = "<ul class=\"ui-listview ui-listview-inset ui-corner-all ui-shadow\" data-role=\"listview\">";
        $html .= "<li class=\"ui-li ui-btn-up-a ui-first-child\"  data-corners=\"false\"><a href=\"{$wwwroot}index.php?task=editstation\" tilte=\"Stationseingabe\">Stationseingabe</a></li>";
        //$html .= '<li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-last-child ui-btn-up-a" data-form="ui-btn-up-a"  data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="a">';
        $html .= "<li class=\"ui-li ui-btn-up-a ui-last-child\" data-corners=\"false\"><a href=\"{$wwwroot}index.php\" tilte=\"Übersicht\">Übersicht</a></li>";
        $html .= "</ul>";

        echo $html;

        switch ($this->_task) {

            case "editstation" : $this->edit();
                break;

            default:
                $this->display_overview();
        }
        $this->print_locklink();
    }

}
