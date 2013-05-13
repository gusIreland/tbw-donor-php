c<?php require_once('includes/config.php');
include('includes/sc-includes.php');
$pagetitle = 'donation';

$update = 0;
if (isset($_GET['id'])) {
$update = 1;
}

//
record_set('donation',"SELECT * FROM donations WHERE id = -1");
if ($update==1) {
record_set('donation',"SELECT * FROM donations WHERE id = ".$_GET['id']."");
}
//



//add donation
if(isset($_POST['donation_first']))
if (!$update && $_POST['donation_first']) {

  mysql_query("INSERT INTO donations () VALUES 

	(
	
	)

	");

$cid = mysql_insert_id();


//insert tags
$tags = str_replace("","",addslashes($_POST['donation_tags']));
$tags = explode(",",$tags);

foreach ($tags as $key => $value) {

$value = trim($value);

	if ($value) {
		mysql_query("DELETE FROM tags WHERE tag_description = '".addslashes($value)."'");
		mysql_query("INSERT INTO tags (tag_description) VALUES
		
		(
			'".addslashes($value)."'
		)
		
		");
	$tid = mysql_insert_id();


}

}

	$redirect = "contact-details.php?id=$cid";
	redirect('Donor Added',$redirect);
}
//end add donation

//update donation


if ($update && $_POST &&  $_POST['legal_amount']) {

mysql_query("UPDATE donations SET

	

	legal_amount = '".insert('legal_amount')."',
	credit_amount = '".insert('credit_amount')."',
	dt_date_record = '".insert('dt_date_record')."',
	match_company_name = '".insert('match_company_name')."'
	

WHERE id = ".$_GET['id']."
");

//add extra fields
mysql_query("DELETE FROM fields_assoc WHERE cfield_donation = ".$_GET['id']."");
record_set('fields',"SELECT * FROM fields ORDER BY field_title ASC");
do {

	$fv = "";
	
	if ($_POST['donation_f_'].$row_fields['field_id']) {
		
		$fv = $_POST['donation_f_'.$row_fields['field_id']];
		
		if (!empty($fv)) {
		mysql_query("INSERT INTO fields_assoc (cfield_field, cfield_donation, cfield_value) VALUES
			
			(
				'".$row_fields['field_id']."',
				'".$_GET['id']."',
				'".$fv."'
			)
		
		");
		}
	}

} while ($row_fields = mysql_fetch_assoc($fields));

	$pid = $_GET['id'];

//insert tags


//

	$cid = $_GET['id'];

	$redirect = "contact-details.php?id=$cid";

	redirect('Donor Updated',$redirect);
}

//custom fields
record_set('fields',"SELECT * FROM fields ORDER BY field_title ASC");
//

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php if ($update==0) { echo "Add Donation"; } ?><?php echo $row_donation['donation_first']; ?> <?php echo $row_donation['donation_last']; ?></title>
<script src="includes/lib/prototype.js" type="text/javascript"></script>
<script src="includes/src/effects.js" type="text/javascript"></script>
<script src="includes/validation.js" type="text/javascript"></script>
<script src="includes/src/scriptaculous.js" type="text/javascript"></script>
<script language="javascript">
function toggleLayer(whichLayer)
{
if (document.getElementById)
{
// this is the way the standards work
var style2 = document.getElementById(whichLayer).style;
style2.display = style2.display? "":"block";
}
else if (document.all)
{
// this is the way old msie versions work
var style2 = document.all[whichLayer].style;
style2.display = style2.display? "":"block";
}
else if (document.layers)
{
// this is the way nn4 works
var style2 = document.layers[whichLayer].style;
style2.display = style2.display? "":"block";
}
}
</script>


<script type="text/javascript">

<!--COUNTRY/STATE
function showState(d) {
	if(d=="US") {
	document.getElementById("state").style.display="block";
	document.getElementById("state_b").style.display="none";
	document.getElementById("state_canada").style.display="none";
	}
	
	if (d=="CA") {
	document.getElementById("state_canada").style.display="block";
	document.getElementById("state_b").style.display="none";
	document.getElementById("state").style.display="none";
	}
	
	if (d!="CA" && d!="US") {
	document.getElementById("state_canada").style.display="none";
	document.getElementById("state_b").style.display="block";
	document.getElementById("state").style.display="none";
	}

}

//-->

</script>

<link href="includes/style.css" rel="stylesheet" type="text/css" />
<link href="includes/simplecustomer.css" rel="stylesheet" type="text/css" />
</head>

<body <?php if ($row_donation['donation_state']) { ?>onload="showState('<?php echo $row_donation['donation_state']; ?>')"<?php } ?>>
<?php include('includes/header.php'); ?>
<div class="container">
  <div class="leftcolumn">
    <h2><?php if ($update==1) { echo 'Update'; } else { echo 'Add'; } ?> Donor </h2>
    <p>&nbsp;</p>
    <form action="" method="POST" enctype="multipart/form-data" name="form1" id="form1">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="28%">Legal amount<br />
            <input name="legal_amount" type="text" class="required" id="legal_amount" value="<?php echo $row_donation['legal_amount']; ?>" size="25" /></td>
          <td width="72%">Credit Amount<br />
                <input name="credit_amount" type="text" class="required" id="credit_amount" value="<?php echo $row_donation['credit_amount']; ?>" size="25" />
            </p></td>
        </tr>
        <tr>
          <td>Date<br />            <input name="dt_date_record" type="text" id="dt_date_record" value="<?php echo $row_donation['dt_date_record']; ?>" size="25" />          </td>
          <td>Matching Company<br />
            <input name="match_company_name" type="text" id="match_company_name" value="<?php echo $row_donation['match_company_name']; ?>" size="35" /></td>
        </tr>
        <tr>
        </tr>
        <tr>
      	 <td colspan="2"><p>
            <input type="submit" name="Submit2" value="<?php echo $update==1 ? 'Update' : 'Add'; ?> donation" />
          </p></td>
        </tr>

            </table>  
</div>          
          <p>&nbsp;</p></td>
        </tr>



      </table>
    </form>

<script type="text/javascript">
	var valid2 = new Validation('form1', {useTitles:true});
</script>

  </div>
  <?php include('includes/right-column.php'); ?>

  <br clear="all" />
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
