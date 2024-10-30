<?php
/*
Plugin Name: Brands 2.0
Plugin URI:
Description: The <a href="http://www.brands20.com">Brands 2.0</a> Ad Engine is a fast and easy way for website site owners to earn money from their photos through "in-photo" advertising.  This service is FREE to use.  You need a <a href="http://www.brands20.com/publishers/wpsiteid.aspx">Brands 2.0 Website ID</a> to use it.  Site owners now have a way to both earn money and enhance photos without distracting from the quality of the site.
Version: 1.0.6
Author: Brands 2.0
Author URI: http://www.brands20.com
*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : support@brands20.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//version
add_option("brands20_version", "1.0.6");

//setup the DB
require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
$wpdb->hide_errors();
$wpdb->brands20_settings = $table_prefix . 'brands20_settings';
$installed = $wpdb->get_results("SELECT value FROM $wpdb->brands20_settings");

if (mysql_errno() == 1146) 
{
	$sql = "CREATE TABLE " . $wpdb->brands20_settings . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			setting VARCHAR(128) NOT NULL,
			value VARCHAR(128) NOT NULL,
			UNIQUE KEY id (id), UNIQUE KEY `setting` (`setting`)
			);";
	$wpdb->query($sql);
}

global $wpdb, $pc_memberid;

//add js to top of page
function pc_js_head()
{
	global $wpdb, $pc_memberid;
	if(!$pc_memberid)	
	{
		$sql = "SELECT value FROM $wpdb->brands20_settings WHERE setting = 'memberid' LIMIT 1";
		$pc_memberid = $wpdb->get_var($sql);
	}
?>
<script type="text/javascript">
  _pcPId = <?=$pc_memberid?>;
</script>
<script language="javascript" type="text/javascript" src="http://brander.brands20.com/"></script>
<?php
}


//setup and function for admin
function pc_add_pages() 
{
	add_options_page('Brands 2.0', 'Brands 2.0', 8, 'brands20', 'pc_brands20');
}

function pc_brands20()
{
	global $wpdb, $table_prefix, $pc_memberid;
	
	//are updating the member id?
	$pc_memberid = $_REQUEST['memberid'];
	if($pc_memberid)
	{
		$sql = "INSERT INTO $wpdb->brands20_settings (setting, value) VALUES ('memberid', '$pc_memberid')";
		$wpdb->hide_errors();
		$result = $wpdb->query($sql);
		if($result)
		{
		?>
<div id="message" class="updated fade">
  <p>Website ID updated successfully.</p>
</div>
<?php
		}
		else
		{
			//try an update
			$sql = "UPDATE $wpdb->brands20_settings SET value = '$pc_memberid' WHERE setting = 'memberid' LIMIT 1";
			$result = $wpdb->query($sql);
			if($result)
			{
		?>
<div id="message" class="updated fade">
  <p>Website ID updated successfully.</p>
</div>
<?php
			}
			else
			{
		?>
<div id="message" class="error">
  <p>Error updating Website ID.</p>
</div>
<?php
			}
		}
	}
	
	//get the member id from the DB?
	if(!$pc_memberid)	
	{
		$sql = "SELECT value FROM $wpdb->brands20_settings WHERE setting = 'memberid' LIMIT 1";
		$pc_memberid = $wpdb->get_var($sql);
	}
	
	?>
<div class="wrap">

  <h2>Brands 2.0 Settings</h2>

  <form action="options-general.php?page=brands20" method="post">
    <br/><br/>Website ID:
    <br/>
    <input type="text" name="memberid" value=""
      <?=$pc_memberid?>" style="width:100px;" />&nbsp;&nbsp;<input type="submit" name="pcsubmit" value="Update" />
      <br />
      <a href="http://www.brands20.com/publishers/wpsiteid.aspx" target="_blank">
        Don't have a <b>Website ID</b>?&nbsp;&nbsp;Get a <b>Website ID</b> here
      </a>
    </form>

  <br/>
  <br/>
  <a href="http://www.brands20.com/help/publishers/" target="_blank">Visit the online help for more assistance with Brands 2.0</a>
  <br />
  <?php
if($pc_memberid)	
	{
	?>
  <br/>
  <br/>
  <a href="http://www.brands20.com/publishers/dashboard/" target="_blank">Manage your Brands 2.0 settings </a>

  <?php
	}
	
?>
</div>
<?php
}

add_action('admin_menu', 'pc_add_pages');
add_filter('wp_head', 'pc_js_head');
//add_action('wp_footer', 'pc_js_footer');	
?>