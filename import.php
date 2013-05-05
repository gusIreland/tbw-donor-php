<?php 
    require_once('includes/config.php'); 
    ini_set('auto_detect_line_endings',TRUE);
    ob_start();
    
    include('includes/sc-includes.php');
    $pagetitle = 'Import Spreadsheet';

    function dataEmpty($dataRow){
        $data_empty = true;
        foreach($dataRow as $data_value) {
            if($data_value != '')
                $data_empty = false;
        }
        return $data_empty;
    }
    
    if (!empty($_GET['csv']) && $_GET['csv'] == 'import' && $_FILES['csv']['tmp_name']) { 
        $row = 1;
        $handle = fopen ($_FILES['csv']['tmp_name'],"r");
        $cf = array();
        
        while ($data = fgetcsv($handle, 1000, ",")) {
            //custom field array
            if ($row == 1) {
            	foreach ($data as $key => $value) {
                   // echo $value;
                   // echo "<br>";
            		if ($key > 200) {
            			$cf[$key] = $value; 
            		}
            	}
            }
            //
            
            //end add extra fields
        
            if($row > 1 && !dataEmpty($data)){
        
                // check if we already have a donor by this name / email
            
                $matching_emails = "SELECT * FROM contacts WHERE contact_email = '".addslashes($data[15])."'";
                $donor_id = -1;
            
                $result_matching_emails = mysql_query($matching_emails);
            
                $row_in_email_check = mysql_fetch_row($result_matching_emails);
            
                if($row_in_email_check){
                    $donor_id = $row_in_email_check[0];
                    echo $donor_id;
                }
    
                else {
                    $donor_query = "INSERT INTO contacts (contact_first, contact_last, contact_title, contact_company, contact_street, contact_city, contact_state, contact_zip, contact_country, contact_email, contact_phone, contact_fax, contact_web, contact_profile) VALUES
                    (
                         '".addslashes($data[1])."',
                         '".addslashes('')."',
                         '".addslashes('')."',
                         '".addslashes('')."',
                         '".addslashes($data[3])."',
                         '".addslashes($data[12])."',
                         '".addslashes($data[13])."',
                         '".addslashes('')."',
                         '".addslashes($data[14])."',
                         '".addslashes($data[15])."',
                         '".addslashes('')."',
                         '".addslashes('')."',
                         '".addslashes('')."',
                         '".addslashes('')."'
                    )";
        
                    $result =  mysql_query($donor_query);
            
                    if(!$result){
                        $message  = 'Invalid query: ' . mysql_error() . "\n";
                        $message .= 'Whole query: ' . $donor_query;
                        die($message);
                    }
                
                    $donor_id = mysql_insert_id();
                    echo $donor_id;
                }
        
                $php_dt_date_record = strtotime($data[4]);
                $mysql_dt_date_record = date('Y-m-d H:i:s', $php_dt_date_record);
                echo "<br>";
                echo $php_dt_date_record;
                echo "<br>";
                echo $mysql_dt_date_record;
                echo "<br>";
                
                $php_date_added = strtotime($data[5]);
                $mysql_added = date('Y-m-d H:i:s', $php_date_added);
                
                $get_donation_for_donor = "SELECT * 
                                           FROM contacts, donations
                                           WHERE contacts.contact_id = donations.donor_id
                                           AND donations.donor_id = " . $donor_id . "
                                           AND donations.dt_date_record = '" . $mysql_dt_date_record . "'
                                           AND donations.legal_amount = " . $data[6] . "";
        
                $get_matching_donations = mysql_query($get_donation_for_donor);
                //Warning: mysql_fetch_row() expects parameter 1 to be resource, boolean given in /opt/lampp/htdocs/tbw-donor-php/import.php on line 139
                $row_for_matching_donation = mysql_fetch_row($get_matching_donations);
        
                if(!$row_for_matching_donation){
                    $donation_query = "INSERT INTO donations
                                       VALUES ('', 
                                       '".addslashes($data[0])."', 
                                       '".$mysql_dt_date_record."', 
                                       '".$mysql_added."',
                                       '".addslashes($data[6])."',
                                       '".addslashes($data[7])."',
                                       '".addslashes($data[8])."',
                                       '".addslashes($data[9])."',
                                       '".addslashes($data[10])."',
                                       '".addslashes($data[11])."',
                                       '".addslashes($data[16])."', 
                                       '".addslashes($data[17])."',
                                       '".addslashes($donor_id)."');";
                    $result = mysql_query($donation_query);
          
                    if (!$result) {
                        $message  = 'Invalid query: ' . mysql_error() . "\n";
                        $message .= 'Whole query: ' . $donation_query;
                        die($message);
                    }
                }
            }
            $row++;
        }
    // header('Location: contacts.php');
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
    <h2> Import Contacts </h2>
    <table width="540" border="0" cellpadding="0" cellspacing="0">
      
      <tr>
        <td colspan="2">Click on &quot;Export Contacts&quot; below to see how to set up your CSV file for importing.</td>
      </tr>
      <tr>
        <td colspan="2"><form name="form1" id="form1" enctype="multipart/form-data" method="post" action="?csv=import">
            <input name="csv" type="file" id="csv" size="40" />
            <br />
            <input name="submit" type="submit" value="Import File" />
            <a href="csv.php"></a> 
            <?php
                if(!$_FILES['csv']['tmp_name']){
                    echo "<br><br>You didn't select any file to upload.  Please try uploading your file again.";
                }
            ?>

        </form></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><a href="csv.php"><strong>+ Export Contacts</strong></a></td>
      </tr>
    </table>    
    <p>&nbsp; </p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
  </div>
  <?php include('includes/right-column.php'); ?>
  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
