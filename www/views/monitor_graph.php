<h1>Per Feature License Monitoring: <?php echo $featurename?></h1>
<p>Back to <a href="<?php echo $config['base_url']?>monitor/display/<?php echo $siteid?>">Feature list</a></p>
<p>Data is taken every 15 minutes. It shows usage for past day, past week, past month and past year.</p>
<a href="<?php echo $config['base_url']?>licenses/export/<?php echo $licid?>/<?php echo $featureid?>">Export to Excel</a>
<h2>Day</h2>
<p><img src="<?php echo $config['base_url']?>licenses/image/<?php echo $licid?>/<?php echo $featureid?>/day" alt="<?php echo $featurename?> daily graph"></p>

<h2>Week</h2>
<p><img src="<?php echo $config['base_url']?>licenses/image/<?php echo $licid?>/<?php echo $featureid?>/week" alt="<?php echo $featurename?> weekly graph"></p>

<h2>Month</h2>
<p><img src="<?php echo $config['base_url']?>licenses/image/<?php echo $licid?>/<?php echo $featureid?>/month" alt="<?php echo $featurename?> monthly graph"></p>

<h2>Year</h2>
<p><img src="<?php echo $config['base_url']?>licenses/image/<?php echo $licid?>/<?php echo $featureid?>/year" alt="<?php echo $featurename?> yearly graph"></p>
