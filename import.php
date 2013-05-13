<?php 
    require_once('includes/config.php'); 
    ini_set('auto_detect_line_endings',TRUE);
    ob_start();
    
    include('includes/sc-includes.php');
    $pagetitle = 'Import Spreadsheet';

    function data_empty($dataRow, $type) {
        if($type == "donation") {
            $data_empty = true;
            foreach($dataRow as $data_value) {
                if($data_value != '')
                    $data_empty = false;
            }
            return $data_empty;
        }
        else {
            return !($dataRow[2] && $dataRow[3] && $dataRow[4]);
        }
    }

    function duplicate_donor($data) {
        $matching_full_names = "SELECT * FROM contacts WHERE contact_first = '".addslashes($data[2])."' AND contact_last = '".addslashes($data[3])."'";
        $result_matching_full_names = mysql_query($matching_full_names);
        $row_matching_full_names = mysql_fetch_assoc($result_matching_full_names);

        return ($row_matching_full_names && ($row_matching_full_names['contact_email'] == addslashes($data[4])));
    }

    function duplicate_donation($data) {
        $recipt_number = $data[11];
        $receipt_number_query = "SELECT * FROM donations WHERE receipt_number = '". $recipt_number . "' AND match_company_name = '" . $data[17] . "'";
        $result_receipt_number = mysql_query($receipt_number_query);
        return (mysql_num_rows($result_receipt_number) > 0);
    }

    function find_donor($data) {
        $email = $data[15];
        $name_to_match = $data[1];
        
        $donors_query = "SELECT * FROM contacts";
        $donors_result = mysql_query($donors_query);

        $donor_id = -1;

        if ($donors_result) {
            while($row = mysql_fetch_assoc($donors_result)) {
                $possible_match = ltrim($row['contact_first']) . " " . $row['contact_last'];
                if(strpos($name_to_match, $possible_match) > -1) {
                    $donor_id = $row['contact_id'];
                }
            }
        }
        return $donor_id;
    }
    
    if (!empty($_GET['csv']) && $_GET['csv'] == 'import' && $_FILES['csv']['tmp_name']) { 
        $row = 1;
        $handle = fopen ($_FILES['csv']['tmp_name'],"r");
        $cf = array();
        
        $csv_type = "donation";

        while ($data = fgetcsv($handle, 1000, ",")) {
            //custom field array
            if ($row == 1) {
            	foreach ($data as $key => $value) {
            		if ($key > 200) {
            			$cf[$key] = $value; 
            		}

                    // if the last value (since we are in a foreach loop is "Comments", then we know that we are trying to import a donor csv)
                    if($value == "Comments")
                        $csv_type = "donor";
            	}
            }
            //
            
            //end add extra fields
        
            if($row > 1 && !data_empty($data, $csv_type)) {
                // we are importing new donors
                if($csv_type == "donor"){
                    if(!duplicate_donor($data)) {
                        $donor_insert_query = mysql_query("INSERT INTO contacts (contact_first, contact_last, contact_email) VALUES
                        (
                             '".addslashes(ltrim(rtrim($data[2])))."',
                             '".addslashes(ltrim(rtrim($data[3])))."',
                             '".addslashes(ltrim(rtrim($data[4])))."'
                        )");
    
                        if(!$donor_insert_query){
                            $message  = 'Invalid query: ' . mysql_error() . "\n";
                            $message .= 'Whole query: ' . $donor_insert_query;
                            die($message);
                        }
    
                        $donor_id = mysql_insert_id();
    
                        $contact_field = mysql_fetch_assoc(mysql_query("SELECT * FROM fields WHERE field_title = 'contact'"));
                        $anonymous_field = $field = mysql_fetch_assoc(mysql_query("SELECT * FROM fields WHERE field_title = 'anonymous'"));
    
                        $cf_query = mysql_query("INSERT INTO fields_assoc (cfield_contact, cfield_field, cfield_value) VALUES
                                    (
                                        '".$donor_id."',
                                        '".$anonymous_field['field_id']."',
                                        '".addslashes(ltrim(rtrim($data[5])))."'
                                    )");
    
                        $cf_query = mysql_query("INSERT INTO fields_assoc (cfield_contact, cfield_field, cfield_value) VALUES
                                    (
                                        '".$donor_id."',
                                        '".$contact_field['field_id']."',
                                        '".addslashes(ltrim(rtrim($data[6])))."'
                                    )");

                        $donor_has_no_donations = mysql_query("INSERT INTO notes (note_contact, note_text, note_date, note_status, note_user, note_pin)
                                                              VALUES ('" . $donor_id . "', '" . 'This donor has no donations!' . "', '" . time() . "', '1', '0', 1)");


                        if($data[7]){
                            $note_insert_query = mysql_query("INSERT INTO notes (note_contact, note_text, note_date, note_status, note_user)
                                                              VALUES ('" . $donor_id . "', '" . addslashes(ltrim(rtrim($data[7]))) . "', '" . time() . "', '1', '0')");
    
                            if(!$note_insert_query){
                                $message  = 'Invalid query: ' . mysql_error() . "\n";
                                $message .= 'Whole query: ' . $note_insert_query;
                                die($message);
                            }
                        }
                    }
                }
                
                // we imported a donor spreadsheet
                else {
                    // $donor_query = "INSERT INTO contacts (contact_first, contact_street, contact_city, contact_state, contact_country, contact_email) VALUES
                    // (
                    //      '".addslashes($data[1])."',
                    //      '".addslashes($data[3])."',
                    //      '".addslashes($data[12])."',
                    //      '".addslashes($data[13])."',
                    //      '".addslashes($data[14])."',
                    //      '".addslashes($data[15])."'
                    // )";
        
                    // $result =  mysql_query($donor_query);
            
                    // if(!$result){
                    //     $message  = 'Invalid query: ' . mysql_error() . "\n";
                    //     $message .= 'Whole query: ' . $donor_query;
                    //     die($message);
                    // }
                    if(!(duplicate_donation($data))) {
                        $donor_id = find_donor($data);
                        if($donor_id != -1) {

                            $get_donor_information = mysql_query("SELECT * FROM contacts WHERE contact_id = '" . $donor_id . "'");
                            $donor_information = mysql_fetch_assoc($get_donor_information);
                            // var_dump($donor_information);

                            if(!$donor_information['contact_email']) {
                                mysql_query("UPDATE contacts SET contact_email = " . addslashes($data[15]) . " WHERE contact_id = '" . $donor_id . "'");
                            }

                            if(!$donor_information['contact_street']) {
                                mysql_query("UPDATE contacts SET contact_street = '" .addslashes($data[3]). "' WHERE contact_id = '" . $donor_id . "'");
                            }    

                            if(!$donor_information['contact_city']) {
                                mysql_query("UPDATE contacts SET contact_city = '" .addslashes($data[12]). "' WHERE contact_id = '" . $donor_id . "'");
                            }    

                            if(!$donor_information['contact_state']) {
                                mysql_query("UPDATE contacts SET contact_state = '" .addslashes($data[13]). "' WHERE contact_id = '" . $donor_id . "'");
                            }           


                
                            $php_dt_date_record = strtotime(ltrim(rtrim($data[4])));
                            $mysql_dt_date_record = date('Y-m-d H:i:s', $php_dt_date_record);
                            
                            $php_date_added = strtotime(ltrim(rtrim($data[5])));
                            $mysql_added = date('Y-m-d H:i:s', $php_date_added);
                            
                            $donation_query = "INSERT INTO donations
                                               VALUES ('', 
                                               '".addslashes(ltrim(rtrim($data[0])))."', 
                                               '".$mysql_dt_date_record."', 
                                               '".$mysql_added."',
                                               '".addslashes(ltrim(rtrim($data[6])))."',
                                               '".addslashes(ltrim(rtrim($data[7])))."',
                                               '".addslashes(ltrim(rtrim($data[8])))."',
                                               '".addslashes(ltrim(rtrim($data[9])))."',
                                               '".addslashes(ltrim(rtrim($data[10])))."',
                                               '".addslashes(ltrim(rtrim($data[11])))."',
                                               '".addslashes(ltrim(rtrim($data[16])))."', 
                                               '".addslashes(ltrim(rtrim($data[17])))."',
                                               '".addslashes(ltrim(rtrim($donor_id)))."');";
                            $result = mysql_query($donation_query);

                            $find_no_donations_note = mysql_query("SELECT * FROM notes WHERE note_contact = '" . $donor_id . "' AND note_text = 'This donor has no donations!'");
                            $find_no_donations_result = mysql_fetch_assoc($find_no_donations_note);

                            if($find_no_donations_result) {
                                mysql_query("DELETE FROM notes WHERE note_id = " . $find_no_donations_result['note_id']);
                                echo "DELETE FROM notes WHERE note_id = " . $find_no_donations_result['note_id'];
                            }

                            if (!$result) {
                                $message  = 'Invalid query: ' . mysql_error() . "\n";
                                $message .= 'Whole query: ' . $donation_query;
                                die($message);
                            }
                        }
                    }
                }
            }
            $row++;
        }
    // exit;
    header('Location: contacts.php?import=success');
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title><?php echo $pagetitle; ?></title>
        <script src="includes/lib/prototype.js" type="text/javascript"></script>
        <script src="includes/src/effects.js" type="text/javascript"></script>
        <script src="includes/validation.js" type="text/javascript"></script>
        <script src="includes/src/scriptaculous.js" type="text/javascript"></script>
        
        <link href="includes/style.css" rel="stylesheet" type="text/css" />
        <link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <?php include('includes/header.php'); ?>
        <div class="container">
            <div class="leftcolumn">
                <h2> Import Donations </h2>
                <table width="540" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <form name="form1" id="form1" enctype="multipart/form-data" method="post" action="?csv=import">
                                <input name="csv" type="file" id="csv" size="40" />
                                <br />
                                <input name="submit" type="submit" value="Import File" />
                                <a href="csv.php"></a> 
                                <?php
                                    if(!$_FILES['csv']['tmp_name'] && isset($_GET['csv'])){
                                        echo "<br><br>You didn't select any file to upload.  Please try uploading your file again.";
                                    }
                                ?>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2"><a href="csv.php"><strong>+ Export Donors</strong></a></td>
                    </tr>
                </table>    
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
        </div>
        <?php include('includes/right-column.php'); ?>
        <br clear="all" />
    </div>
    <?php include('includes/footer.php'); ?>
    </body>
</html>