<?php
/**
 * Sharif Judge online judge
 * @file scoreboard_table.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<table class="sharif_table">
<thead>
<tr>
<th>#</th><th>Username</th><th>Name</th>
<?php foreach ($problems as $problem): ?>
<th>Problem <?php echo $problem['id'] ?><br>(<?php echo $problem['name'] ?>)</th>
<?php endforeach ?>
<th>Total</th>
</tr>
</thead>
<?php $i=0; foreach ($scoreboard['username'] as $sc_username): ?>
<tr>
<td><?php echo ($i+1) ?></td>
<td><?php echo $sc_username ?></td>
<td><?php echo $this->user_model->get_display_name($sc_username); ?></td>
<?php foreach($problems as $problem): ?>
<td>
	<?php if (isset($scores[$sc_username][$problem['id']]['score'])): ?>
		<?php echo $scores[$sc_username][$problem['id']]['score']; ?>
		<br>
		<span class="scoreboard_hours" title="time"><?php echo floor($scores[$sc_username][$problem['id']]['time']/360)/10 ?> hours</span>
	<?php else: ?>
		-
	<?php endif ?>
</td>
<?php endforeach ?>
<td>
<span style="font-weight: bold;"><?php echo $scoreboard['score'][$i] ?></span>
<br>
<span class="scoreboard_hours" title="total time + submit penalty"><?php echo floor($scoreboard['submit_penalty'][$i]/360)/10 ?> hours</span>
</td>
</tr>
<?php $i++ ?>
<?php endforeach ?>
</table>
