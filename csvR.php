<?php 
    require_once('includes/config.php');
    include('includes/sc-includes.php');
    // get the variables from the post
    if(isset($_POST["start_date"]) && isset($_POST["end_date"]) && isset($_POST["order"]) && isset($_POST["pp"]) && isset($_POST["offset"])){
        $startDateString = isset($_POST["start_date"]) ? $_POST["start_date"] : "";
        $endDateString = isset($_POST["end_date"]) ? $_POST["end_date"] : "";
        $sorder = isset($_POST["order"]) ? $_POST["order"] : "";
        $pp = isset($_POST["pp"]) ? $_POST["pp"] : "";
        $offset = isset($_POST["offset"]) ? $_POST["offset"] : "";
        $limit = "LIMIT $offset, $pp";

        $query = "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id where date_added between $startDateString and $endDateString $sorder $limit;";
    } 

    elseif(isset($_POST['comparison_string'])) {
        $comparison = $_POST['comparison_string'];
        if(isset($comparison) && preg_match("/^\d+$/", $comparison)) {
            $query = "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount = '".$comparison."' ORDER BY legal_amount DESC";
        }
    
        elseif(isset($comparison) && preg_match("/^<\d+$/", $comparison)) {
            $query = "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount < '".substr($comparison, 1)."' ORDER BY legal_amount DESC";
        }
    
        elseif(isset($comparison) && preg_match("/^>\d+$/", $comparison)) {
            $query = "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount > '".substr($comparison, 1)."' ORDER BY legal_amount DESC";
        }
    
        elseif(isset($comparison) && preg_match("/^<=\d+$/", $comparison)) {
            $query = "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount <= '".substr($comparison, 2)."' ORDER BY legal_amount DESC";
        }
    
        elseif(isset($comparison) && preg_match("/^>=\d+$/", $comparison)) {
            $query = "SELECT * FROM donations INNER JOIN contacts ON contact_id = donor_id WHERE legal_amount >= '".substr($comparison, 2)."' ORDER BY legal_amount DESC";
        }
    } 

    else {
        exit;
    }
    
    //get donations list
    record_set('donationlist',$query);
    // create the headers
    $csv_fields = array();
    // $csv_fields[0] = 'id';
    $csv_fields[0] = 'contact_id';
    $csv_fields[1] = 'contact_first';
    $csv_fields[2] = 'contact_last';
    $csv_fields[3] = 'contact_title';
    $csv_fields[4] = 'contact_tags';
    $csv_fields[5] = 'contact_company';
    $csv_fields[6] = 'contact_street';
    $csv_fields[7] = 'contact_city';
    $csv_fields[8] = 'contact_state';
    $csv_fields[9] = 'contact_country';
    $csv_fields[10] = 'contact_zip';
    $csv_fields[11] = 'contact_email';
    $csv_fields[12] = 'id';
    $csv_fields[13] = 'cmu_transaction_id';
    $csv_fields[14] = 'dt_date_record';
    $csv_fields[15] = 'date_added';
    $csv_fields[16] = 'legal_amount';
    $csv_fields[17] = 'credit_amount';
    $csv_fields[18] = 'transaction_type_description';
    $csv_fields[19] = 'alloc_short_name';
    $csv_fields[20] = 'prsp_manager';
    $csv_fields[21] = 'receipt_number';
    $csv_fields[22] = 'prim_comment';
    $csv_fields[23] = 'match_company_name';
    $csv_fields[24] = 'donor_id';
    
    foreach ($csv_fields as $key => $value) {
        $csv_output .= "\"".$value."\",";
    }
    
    $csv_output .= "\n"; 
    
    // because the record_set function already gets the first row, we can't increment until we've added the first row.
    if($totalRows_donationlist > 0) {

        $csv_output_r = array();
        // $csv_output_r[0] = $row_donationlist['id'];
        $csv_output_r[0] = $row_donationlist['contact_id'];
        $csv_output_r[1] = $row_donationlist['contact_first'];
        $csv_output_r[2] = $row_donationlist['contact_last'];
        $csv_output_r[3] = $row_donationlist['contact_title'];
        $csv_output_r[4] = $row_donationlist['contact_tags'];
        $csv_output_r[5] = $row_donationlist['contact_company'];
        $csv_output_r[6] = $row_donationlist['contact_street'];
        $csv_output_r[7] = $row_donationlist['contact_city'];
        $csv_output_r[8] = $row_donationlist['contact_state'];
        $csv_output_r[9] = $row_donationlist['contact_country'];
        $csv_output_r[10] = $row_donationlist['contact_zip'];
        $csv_output_r[11] = $row_donationlist['contact_email'];
        $csv_output_r[12] = $row_donationlist['id'];
        $csv_output_r[13] = $row_donationlist['cmu_transaction_id'];
        $csv_output_r[14] = $row_donationlist['dt_date_record'];
        $csv_output_r[15] = $row_donationlist['date_added'];
        $csv_output_r[16] = $row_donationlist['legal_amount'];
        $csv_output_r[17] = $row_donationlist['credit_amount'];
        $csv_output_r[18] = $row_donationlist['transaction_type_description'];
        $csv_output_r[19] = $row_donationlist['alloc_short_name'];
        $csv_output_r[20] = $row_donationlist['prsp_manager'];
        $csv_output_r[21] = $row_donationlist['receipt_number'];
        $csv_output_r[22] = $row_donationlist['prim_comment'];
        $csv_output_r[23] = $row_donationlist['match_company_name'];
        $csv_output_r[24] = $row_donationlist['donor_id'];
    
        foreach ($csv_output_r as $key => $value) {
            $csv_output .= "\"".$value."\",";
        }
    
        // somehow fixes the blank line bug?
        // $csv_output .= "\n";
    }

    // generate 2nd - nth rows
    do {
        $csv_output_r = array();
        // $csv_output_r[0] = $row_donation_list['id'];
        $csv_output_r[0] = $row_donation_list['contact_id'];
        $csv_output_r[1] = $row_donation_list['contact_first'];
        $csv_output_r[2] = $row_donation_list['contact_last'];
        $csv_output_r[3] = $row_donation_list['contact_title'];
        $csv_output_r[4] = $row_donation_list['contact_tags'];
        $csv_output_r[5] = $row_donation_list['contact_company'];
        $csv_output_r[6] = $row_donation_list['contact_street'];
        $csv_output_r[7] = $row_donation_list['contact_city'];
        $csv_output_r[8] = $row_donation_list['contact_state'];
        $csv_output_r[9] = $row_donation_list['contact_country'];
        $csv_output_r[10] = $row_donation_list['contact_zip'];
        $csv_output_r[11] = $row_donation_list['contact_email'];
        $csv_output_r[12] = $row_donation_list['id'];
        $csv_output_r[13] = $row_donation_list['cmu_transaction_id'];
        $csv_output_r[14] = $row_donation_list['dt_date_record'];
        $csv_output_r[15] = $row_donation_list['date_added'];
        $csv_output_r[16] = $row_donation_list['legal_amount'];
        $csv_output_r[17] = $row_donation_list['credit_amount'];
        $csv_output_r[18] = $row_donation_list['transaction_type_description'];
        $csv_output_r[19] = $row_donation_list['alloc_short_name'];
        $csv_output_r[20] = $row_donation_list['prsp_manager'];
        $csv_output_r[21] = $row_donation_list['receipt_number'];
        $csv_output_r[22] = $row_donation_list['prim_comment'];
        $csv_output_r[23] = $row_donation_list['match_company_name'];
        $csv_output_r[24] = $row_donation_list['donor_id'];
    
        foreach ($csv_output_r as $key => $value) {
            $csv_output .= "\"".$value."\",";
        }
    
        $csv_output .= "\n";
    } while($row_donation_list = mysql_fetch_array($donationlist));
    
    //You cannot have the breaks in the same feed as the content. 
    header("Content-type: application/vnd.ms-excel");
    header("Content-disposition: csv; filename=tbwdonations.csv");
    print $csv_output;
    exit;
?>