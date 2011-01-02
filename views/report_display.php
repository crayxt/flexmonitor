<h1>License Monitoring Report for <?php echo $featurename?></h1>
<p>Back to <a href="<?php echo $config['base_url']?>report/">Reports</a></p>
<h2>Month</h2>
<p><img src="<?php echo $config['base_url']?>licenses/image/<?php echo $licenses?>/<?php echo $featureid?>/month" alt="<?php echo $featurename?> monthly graph"></p>
<h2>Year</h2>
<p><img src="<?php echo $config['base_url']?>licenses/image/<?php echo $licenses?>/<?php echo $featureid?>/year" alt="<?php echo $featurename?> yearly graph"></p>
<a href="<?php echo $config['base_url']?>licenses/export/<?php echo $licenses?>/<?php echo $featureid?>">Export to Excel</a>
<hr />
<?php
foreach($sitenames as $licid=>$sitename){
?>Graphes for <a href="<?php echo $config['base_url']?>monitor/graph/<?php echo $licid?>/<?php echo $featureid?>"><?php echo $sitename?></a>
<a href="<?php echo $config['base_url']?>licenses/export/<?php echo $licid?>/<?php echo $featureid?>">Export to Excel</a><br />
<?php
}
?>