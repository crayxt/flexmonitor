<?php
class LicEngine {
  // single instance of self shared among all instances

    private $lmutil_loc = "";
    private $color = Array("#ffffdd","#ff9966", "#ffffaa","#ccccff","#cccccc","#ffcc66","#99ff99","#eeeeee","#66ffff","#ccffff", "#ffff66", "#ffccff","#ff66ff", "yellow","lightgreen","lightblue");
    private static $instance = null;

    //This method must be static, and must return an instance of the object if the object
    //does not already exist.
    public static function getInstance($arg) {
        if (!self::$instance instanceof self) {
            self::$instance = new self($arg);
        }
        return self::$instance;
    }

    // The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
    // thus eliminating the possibility of duplicate objects.
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
    public function __wakeup() {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }

    // private constructor
    private function __construct($lmutil_loc) {
        ini_set('max_execution_time', 0);
        $this->lmutil_loc = $lmutil_loc;
     }


public function build_license_expiration_array($lmutil_loc, $server, &$expiration_array) {

  $total_licenses = 0;

  $file = popen($lmutil_loc . " lmcksum -c " . $server,"r");

  $today = time();

  # Let's read in the file line by line
  while (!feof ($file)) {

    $line = fgets ($file, 1024);

    if ( eregi("INCREMENT .*", $line, $out ) || eregi("FEATURE .*", $line, $out ) ) {
        $license = explode(" ", $out[0]);

	if ( $license[4] )  {
	# UNIX time stamps go only till year 2038 (or so) so convert
	# any license dates 9999 or 0000 (which means infinity) to
	# an acceptable year. 2036 seems like a good number
        $license[4] = strtolower($license[4]);
	$license[4] = str_replace("-9999", "-2036", $license[4]);
        $license[4] = str_replace("-0000", "-2036", $license[4]);
        $license[4] = str_replace("-2099", "-2036", $license[4]);
        $license[4] = str_replace("-2242", "-2036", $license[4]);
        $license[4] = str_replace("-jan-00", "-jan-2036", $license[4]);
        $license[4] = str_replace("-jan-0", "-jan-2036", $license[4]);
        $license[4] = str_replace("-0", "-2036", $license[4]);
	$license[4] = str_replace("permanent", "05-jul-2036", $license[4]);
        $unixdate2 = strtotime($license[4]);

        # Convert the date you got into UNIX time
        $unixdate2 = strtotime($license[4]);
	}


    $days_to_expiration = ceil ((1 + strtotime($license[4]) - $today) / 86400);

    ##############################################################################
    # If there is more than 4000 days = 10 years+ until expiration I will
    # consider the license to be permanent
    ##############################################################################
    if ( $days_to_expiration > 4000 ) {
        $license[4] = "permanent";
        $days_to_expiration = "permanent";
    }

    ##############################################################################
    # Add to the expiration array
    $expiration_array[$license[1]][] = array(
        "expiration_date" => $license[4],
        "num_licenses" => $license[5],
        "days_to_expiration" => $days_to_expiration );
    }


  }

  #echo("<pre>");
  #print_r($expiration_array);


  pclose($file);

  return 1;
}

    public function get_licmanager_status($server,$port,$type) {
        switch ($type){
            case "LMX":
            $fp = popen($this->lmutil_loc . " -licstat -host " . $server . " -port " . $port, "r");
                while ( !feof ($fp) ) {
                    $line = fgets ($fp, 1024);
                    //Look for an expression like this ie. kalahari: license server UP (MASTER) v6.1
                    if ( eregi (": license server UP", $line) ) {
                        return Array("UP","OK",eregi_replace(".*license server UP ", "", $line));
                        break;
                    }

            		if ( eregi ("Unable to connect to server!", $line, $out) ) {
            			return Array("DOWN",$out[0],"unknown");
            			break;
                    }
                }

                # Close the pipe
                pclose($fp);

                break;
            case "FlexLM":
                $fp = popen($this->lmutil_loc . " lmstat -c " . $port . "@" . $server, "r");
                while ( !feof ($fp) ) {
                    $line = fgets ($fp, 1024);
                    //Look for an expression like this ie. kalahari: license server UP (MASTER) v6.1
                    if ( eregi (": license server UP", $line) ) {
                        return Array("UP","OK",eregi_replace(".*license server UP ", "", $line));
                        break;
                    }

            		if ( eregi ("Cannot connect to license server", $line, $out) ) {
            			return Array("DOWN",$out[0],"unknown");
            			break;
                    }

                    if ( eregi ("Cannot read data", $line, $out) ) {
                        return Array("DOWN",$out[0],"unknown");
                        break;
                    }

                    if ( eregi ("Error getting status", $line, $out) ) {
                        return Array("DOWN",$out[0],"unknown");
                        break;
                    }

                     //Checking if vendor daemon has died evenif lmgrd is still running
                    if ( eregi ("vendor daemon is down", $line, $out) ) {
                        return Array("DOWN",$out[0],"unknown");
                        break;
                    }

                }

                # Close the pipe
                pclose($fp);

                break;
            default:
                return Array("DOWN","NA","unknown");
                break;
        }

    }

    public function get_available_licenses($server,$port,$type) {
        switch ($type) {
            case "LMX":
            $fp = popen($this->lmutil_loc . " -licstat -host " . $server . " -port " . $port, "r");
                while ( !feof ($fp) ) {
                    $line = fgets ($fp, 1024);
                    # Look for features in the output. You will see stuff like
                    # Users of Allegro_Viewer: (Total of 5 licenses available
                    if ( eregi("^Users of (.*)Total ", $line ) )  {
                        $out = explode(" ", $line);
                        # Remove the : in the end of the string
                        $licavailable[] = Array (str_replace(":", "", $out[2]),$out[6]);
                    }
                }

                # Close the pipe
                pclose($fp);

                break;
            case "FlexLM":
                $fp = popen($this->lmutil_loc . " lmstat -a -c " . $port. "@" . $server, "r");
                while ( !feof ($fp) ) {
                    $line = fgets ($fp, 1024);
                    # Look for features in the output. You will see stuff like
                    # Users of Allegro_Viewer: (Total of 5 licenses available
                    if ( eregi("^Users of (.*)Total ", $line ) )  {
                        $out = explode(" ", $line);
                        # Remove the : in the end of the string
                        $licavailable[] = Array (str_replace(":", "", $out[2]),$out[6]);
                    }
                }
                pclose($fp);
                break;
            default:
                break;
        }
        return $licavailable;
    }

    public function get_used_licenses($server,$port,$type) {
        switch ($type) {
        case "FlexLM":
            $fp = popen($this->lmutil_loc . " lmstat -a -c " . $port . "@" . $server, "r");
            while ( !feof ($fp) ) {
                $line = fgets ($fp, 1024);
                # Look for features in the output. You will see stuff like
                # Users of Allegro_Viewer: (Total of 5 licenses available
                if ( eregi("^(Users of) (.*)",$line, $out ) )  {
                    if ( eregi("(Total of) (.*) (license[s]? issued;  Total of) (.*) (license[s]? in use)", $line, $items ) ) {
                        $license_array[] = array ("feature" => FlexmonitorDB::getInstance($db)->get_featureid_by_name(substr($out[2],0,strpos($out[2],":"))),"licenses_used" => $items[4] ) ;
                        unset($out);
                        unset($items);
                    }
                }
            }
            pclose($fp);
            break;
        default:
            break;
        }
        return $license_array;
    }

    public function get_feature_list($license) {
        //require_once 'HTML/table.php';
        $today = mktime(0,0,0,date("m"),date("d"),date("Y"));

        $tableStyle = "border=\"1\" cellpadding=\"1\" cellspacing=\"2\" ";

        # Create a new table object
        $table = new HTML_Table($tableStyle);
        switch($license['type']){
            case "FlexLM":
                $server = $license[2] . "@" . $license[1];
                $headerStyle = "colspan=\"3\"";
                $colHeaders = array("Server: " . $server . " ( " . $license[3] . " )");
            	$table->addRow($colHeaders, $headerStyle, "TH");

            	include_once("./includes/tools.php");

            	build_license_expiration_array($this->lmutil_loc, $server, $expiration_array);

            	# Define a table header
                $headerStyle = "style=\"background: yellow;\"";
                $colHeaders = array("Feature", "Total licenses", "Number licenses, Days to expiration, Date of expiration");
                $table->addRow($colHeaders, $headerStyle, "TH");

                #######################################################
                # Get names of different colors. These will be used to group visually
                # licenses from the same license server
                #######################################################
                $color = explode(",", $colors);

                #######################################################
                # We should have gotten an array with features
                # their expiration dates etc.
                #######################################################
                foreach ($expiration_array as $key => $feature_array) {
                    $total_licenses = 0;
                    $feature_string = "";
            		$feature_table = new HTML_Table("width=\"100%\"");
            		for ( $p = 0 ; $p < sizeof($feature_array) ; $p++ ) {
            			$total_licenses += $feature_array[$p]["num_licenses"];
            			$feature_table->addRow(array($feature_array[$p]["num_licenses"] . " license(s) expire(s) in ". $feature_array[$p]["days_to_expiration"] . " day(s) Date of expiration: " . $feature_array[$p]["expiration_date"] ), "colspan=\"3\"");
            			if ( $feature_array[$p]["days_to_expiration"] <= $lead_time && $feature_array[$p]["days_to_expiration"] != "permanent" ){
                            $feature_table->updateRowAttributes( ($feature_table->getRowCount() - 1) , "class=\"expires_soon\"");
                        }
                        if ( $feature_array[$p]["days_to_expiration"] <= 0 && $feature_array[$p]["days_to_expiration"] != "permanent" ){
                            $feature_table->updateRowAttributes( ($feature_table->getRowCount() - 1) , "class=\"already_expired\"");
                        }
                    }

            		$table->addRow(array(
            			$key,
            			$total_licenses,
            			$feature_table->toHTML(),
                    ));
                }

                ########################################################
                # Center columns 2. Columns start with 0 index
                ########################################################
                $table->updateColAttributes(1,"align=\"center\"");
            	$table->display();
            break;
            case "LMX":
                echo "<h2> LMX Features and expiration dates not yet available </h2>";
            break;
            default:
                echo "<h2>Feature unavailable</h2>";
            break;
        }
    }

    public function get_feature_usage($license) {
    switch ($license['type']) {
            case "FlexLM":
			# Execute lmutil lmstat -A -c 27000@license or similar
            $fp = popen($this->lmutil_loc . " lmstat -A -c " . $license['port'] . "@" . $license['hostname'], "r");

			# Feature counter
			$i = -1;

			################################################################################
			# Loop through the output. Look for lines starting with Users. Then look for any
			# consecutive entries showing who is using it
			################################################################################
			while ( !feof ($fp) ) {

				$line = fgets ($fp, 1024);

				# Look for features in the output. You will see stuff like
				# Users of Allegro_Viewer: (Total of 5 licenses available
				if ( preg_match('/(Users of) (.*)(\(Total of) (\d+) (.*) (Total of) (\d+) /i', $line, $out) && !eregi("No such feature exists", $line) ) {
					$i++;
					$license_array[$i]["feature"] = $out[2];
					$license_array[$i]["num_licenses"] = $out[4];
					$license_array[$i]["licenses_used"] = $out[7];
				}

				# Count the number of licenses. Each used license has ", start" string in it
				if ( eregi(", start", $line ) ){
					$users[$i][] = $line;
				}
			}

			################################################################################
			# Check whether anyone is using licenses from this particular license server
			################################################################################
			if ( $i > - 1 ) {

				# Create a new table
				$tableStyle = "width=\"100%\"";

				$table = new HTML_Table($tableStyle);

				# Show a banner with the name of the serve@port plus description
				$headerStyle = "colspan=\"4\"";
				$colHeaders = array("Server: " . $license['hostname'] . " ( " . $license['name'] . " )");
				$table->addRow($colHeaders, $headerStyle, "TH");

				$headerStyle = "style=\"background: lightblue;\"";
				$colHeaders = array("Feature", "# cur. avail", "Details","Time checked out");
				$table->addRow($colHeaders, $headerStyle, "TH");

				# Get current UNIX time stamp
				$now = time ();

				###########################################################################
				# Loop through the used features
				###########################################################################
				for ( $j = 0 ; $j <= $i ; $j++ ) {
						# How many licenses are currently used
						$licenses_available = $license_array[$j]["num_licenses"] - $license_array[$j]["licenses_used"];
						$license_info = "Total of " . $license_array[$j]["num_licenses"] . " licenses, " .
						$license_array[$j]["licenses_used"] . " currently in use, <b>" . $licenses_available . " available</b>";

						$table->addRow(array($license_array[$j]["feature"], "$licenses_available", $license_info), "style=\"background: " .$this->color[$j].";\"");

						for ( $k = 0; $k < sizeof($users[$j]) ; $k++ ) {
							################################################################
							# I want to know how long a license has been checked out. This
							# helps in case some people forgot to close an application and
							# have licenses checked out for too long.
							# LMstat view will contain a line that says
							# jdoe machine1 /dev/pts/4 (v4.0) (licenserver/27000 444), start Thu 12/5 9:57
							# the date after start is when license was checked out
							################################################################
							$line = split(", start ", $users[$j][$k]);
							preg_match("/(.+?) (.+?) (\d+):(\d+)/i", $line[1], $line2);

							# Convert the date and time ie 12/5 9:57 to UNIX time stamp
							$time_checkedout = strtotime ($line2[2] . " " . $line2[3] . ":" . $line2[4]);

							$time_difference = "";

							################################################################
							# This is what I am not very clear on but let's say a license has been
							# checked out on 12/31 and today is 1/2. It is unclear to me whether
							# strotime will handle the conversion correctly ie. 12/31 will actually
							# be 12/31 of previous year and not the current. Thus I will make a
							# little check here. Will just append the previous year if now is less
							# then time_checked_out
							################################################################
                            require_once 'tools.php';
							if ( $now < $time_checkedout ){
								$time_checkedout = strtotime ($line2[2] . "/" . (date("Y") - 1) . " " . $line2[3]);
							}else {
								# Get the time difference
								$t = new timespan( $now, $time_checkedout );

								# Format the date string
								if ( $t->years > 0) $time_difference .= $t->years . " years(s), ";
								if ( $t->months > 0) $time_difference .= $t->months . " month(s), ";
								if ( $t->weeks > 0) $time_difference .= $t->weeks . " week(s), ";
								if ( $t->days > 0) $time_difference .= " " . $t->days . " day(s), ";
								if ( $t->hours > 0) $time_difference .= " " . $t->hours . " hour(s), ";
								$time_difference .= $t->minutes . " minute(s)";
							}

							# Output the user line
							$table->addRow(array( "&nbsp;", "" ,$users[$j][$k], $time_difference), "style=\"background: ".$this->color[$j].";\"");
							}
					}

					$table->updateColAttributes(2,"align=\"center\"");
					$table->updateColAttributes(4,"align=\"center\"");
					$table->updateColAttributes(0,"align=\"center\"");


			     # Display the table
			     if ( $table->getRowCount() > 2 ){
				$table->display();
			     }

			} else {
				echo("<p style=\"color: red;\">No licenses are currently being used on " . $servers[$current_server[$n]]. " ( " . $license['name'] . " )</p>");
			}
			pclose($fp);
                break;
            case "LMX":
                echo "<h2>Real Time usage feature not yet available</h2>";
                break;

            default:
                echo "<h2>Feature not implemented</h2>";
                break;
        }
    }
}
?>
