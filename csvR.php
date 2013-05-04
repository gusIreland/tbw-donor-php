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
    }

    // generate a new query based on the information
    $limit = "LIMIT $offset, $pp";
    $query = "SELECT * FROM donations where date_added between $startDateString and $endDateString $sorder $limit;";

    
    //get donations list
    record_set('donationlist',$query);
    
    // create the headers
    $csv_fields = array();
    $csv_fields[0] = 'id';
    $csv_fields[1] = 'cmu_transaction_id';
    $csv_fields[2] = 'dt_date_record';
    $csv_fields[3] = 'date_added';
    $csv_fields[4] = 'legal_amount';
    $csv_fields[5] = 'credit_amount';
    $csv_fields[6] = 'transaction_type_description';
    $csv_fields[7] = 'alloc_short_name';
    $csv_fields[8] = 'prsp_manager';
    $csv_fields[9] = 'receipt_number';
    $csv_fields[10] = 'prim_comment';
    $csv_fields[11] = 'match_company_name';
    $csv_fields[12] = 'donor_id';
    
    foreach ($csv_fields as $key => $value) {
        $csv_output .= "\"".$value."\",";
    }
    
    $csv_output .= "\n"; 
    
    // because the record_set function already gets the first row, we can't increment until we've added the first row.
    if($totalRows_donationlist > 0) {

        $csv_output_r = array();
        $csv_output_r[0] = $row_donationlist['id'];
        $csv_output_r[1] = $row_donationlist['cmu_transaction_id'];
        $csv_output_r[2] = $row_donationlist['dt_date_record'];
        $csv_output_r[3] = $row_donationlist['date_added'];
        $csv_output_r[4] = $row_donationlist['legal_amount'];
        $csv_output_r[5] = $row_donationlist['credit_amount'];
        $csv_output_r[6] = $row_donationlist['transaction_type_description'];
        $csv_output_r[7] = $row_donationlist['alloc_short_name'];
        $csv_output_r[8] = $row_donationlist['prsp_manager'];
        $csv_output_r[9] = $row_donationlist['receipt_number'];
        $csv_output_r[10] = $row_donationlist['prim_comment'];
        $csv_output_r[11] = $row_donationlist['match_company_name'];
        $csv_output_r[12] = $row_donationlist['donor_id'];
    
        foreach ($csv_output_r as $key => $value) {
            $csv_output .= "\"".$value."\",";
        }
    
        // somehow fixes the blank line bug?
        // $csv_output .= "\n";
    }

    // generate 2nd - nth rows
    do {
        $csv_output_r = array();
        $csv_output_r[0] = $row_donation_list['id'];
        $csv_output_r[1] = $row_donation_list['cmu_transaction_id'];
        $csv_output_r[2] = $row_donation_list['dt_date_record'];
        $csv_output_r[3] = $row_donation_list['date_added'];
        $csv_output_r[4] = $row_donation_list['legal_amount'];
        $csv_output_r[5] = $row_donation_list['credit_amount'];
        $csv_output_r[6] = $row_donation_list['transaction_type_description'];
        $csv_output_r[7] = $row_donation_list['alloc_short_name'];
        $csv_output_r[8] = $row_donation_list['prsp_manager'];
        $csv_output_r[9] = $row_donation_list['receipt_number'];
        $csv_output_r[10] = $row_donation_list['prim_comment'];
        $csv_output_r[11] = $row_donation_list['match_company_name'];
        $csv_output_r[12] = $row_donation_list['donor_id'];
    
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