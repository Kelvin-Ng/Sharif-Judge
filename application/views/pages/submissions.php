<?php
/**
 * Sharif Judge online judge
 * @file all_submissions.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<link rel='stylesheet' type='text/css' href='<?php echo base_url("assets/snippet/jquery.snippet.css") ?>'/>
<link rel='stylesheet' type='text/css' href='<?php echo base_url("assets/snippet/themes/github.css") ?>'/>
<script type='text/javascript' src="<?php echo base_url("assets/snippet/jquery.snippet.js") ?>"></script>

<link rel='stylesheet' type='text/css' href='<?php echo base_url("assets/reveal/reveal.css") ?>'/>
<script type='text/javascript' src="<?php echo base_url("assets/reveal/jquery.reveal.js") ?>"></script>
<script type='text/javascript' src="<?php echo base_url("assets/js/shj_submissions.js") ?>"></script>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>"{$view}_submissions")); ?>
<?php
$finish = strtotime($assignment['finish_time']);
?>

<div id="main_container" class="scroll-wrapper">
<div class="scroll-content">

	<div id="page_title">
		<img src="<?php echo base_url("assets/images/icons/{$view}_submissions.png") ?>"/>
		<span><?php echo $title ?></span>
		<span class="title_menu_item">
			<a href="<?php echo $excel_link ?>"><i class="splashy-document_small_download"></i> Excel</a>
		</span>
		<?php if ($filter_user): ?>
		<span class="title_menu_item">
			<a href="<?php echo site_url('submissions/'.$view.($filter_problem?'/problem/'.$filter_problem:'')) ?>"><i class="splashy-tag_remove"></i> Remove Username Filter</a>
		</span>
		<?php endif ?>
		<?php if ($filter_problem): ?>
		<span class="title_menu_item">
			<a href="<?php echo site_url('submissions/'.$view.($filter_user?'/user/'.$filter_user:'')) ?>"><i class="splashy-tag_remove"></i> Remove Problem Filter</a>
		</span>
		<?php endif ?>
	</div>

	<div id="main_content">
		<p><?php echo ucfirst($view); ?> Submissions of <?php echo $assignment['name']; ?></p>
		<?php if ($view == "all"): ?>
		<p><i class="splashy-warning_triangle"></i> You cannot change your final submissions after assignment finishes.</p>
		<?php endif ?>
		<table class="sharif_table">
			<thead>
				<tr>
				<?php if ($view=='all'): ?>
					<th width="1%" rowspan="2">Final</th>
				<?php endif ?>
				<?php if ($user_level>0): ?>
						<?php if ($view=='all'): ?>
						<th width="3%" rowspan="2">ID</th>
						<?php else: ?>
						<th width="2%" rowspan="2">#</th>
						<th width="3%" rowspan="2">ID</th>
						<?php endif ?>
						<th width="6%" rowspan="2">Username</th>
						<th width="14%" rowspan="2">Name</th>
						<th width="4%" rowspan="2">Problem</th>
						<th width="14%" rowspan="2">Submit Time</th>
						<th colspan="3">Score</th>
						<th width="1%" rowspan="2">Language</th>
						<th width="6%" rowspan="2">Status</th>
						<th width="6%" rowspan="2">Code</th>
						<?php if ($view=="final"): ?>
						<th width="6%" rowspan="2">Log</th>
						<?php endif ?>
						<?php if ($user_level>=2): ?>
						<th width="1%" rowspan="2">Actions</th>
						<?php endif ?>
						<th width="1%" rowspan="2">#</th>
					</tr>
					<tr>
						<th width="5%" class="score">Score</th>
						<th width="5%" class="score">Delay<br>%</th>
						<th width="5%" class="score">Final Score</th>
					</tr>
				<?php else: ?>
						<th width="10%" rowspan="2">Problem</th>
						<th width="30%" rowspan="2">Submit Time</th>
						<th width="7%" colspan="3">Score</th>
						<th width="1%" rowspan="2">Language</th>
						<th width="30%" rowspan="2">Status</th>
						<th width="15%" rowspan="2">Code</th>
						<th width="5%" rowspan="2">#</th>
					</tr>
					<tr>
						<th width="7%" class="score">Score</th>
						<th width="7%" class="score">Delay<br>%</th>
						<th width="7%" class="score">Final Score</th>
					</tr>
				<?php endif ?>
			</thead>
			<?php $i=0; $j=0; $un=''; ?>
			<?php foreach ($items as $item): ?>
				<?php
				$i++;
				if ($item['username']!=$un)
					$j++;
				$un = $item['username'];
				?>
				<tr data-u="<?php echo $item['username'] ?>" data-a="<?php echo $item['assignment'] ?>" data-p="<?php echo $item['problem'] ?>" data-s="<?php echo $item['submit_id'] ?>" <?php if ($view=='final' && $j%2==0){ echo 'class="hl"';} ?>>
				<?php if ($view=='all'): ?>
					<td>
					<?php
						$checked='';
						if (isset($final_items[$item['username']][$item['problem']]['submit_id']))
							if ($final_items[$item['username']][$item['problem']]['submit_id'] == $item['submit_id'])
								$checked='checked';
					?>
					<div title="Set as Final Submission" class="set_final check p<?php echo $item['problem'] ?> <?php echo $checked ?>"></div>
					</td>
				<?php endif ?>
				<?php if ($user_level>0): ?>
					<?php if ($view=='all'): ?>
						<td><?php echo $item['submit_id'] ?></td>
					<?php else: ?>
						<td><?php echo ($page_number-1)*$per_page+$i ?></td>
						<td><?php echo $item['submit_id'] ?></td>
					<?php endif ?>

					<td><a href="<?php echo site_url('submissions/'.$view.'/user/'.$item['username'].($filter_problem?'/problem/'.$filter_problem:'')) ?>"><?php echo $item['username'] ?></a></td>
					<td><?php
						if(!isset($name[$item['username']]))
							$name[$item['username']]=$this->user_model->get_display_name($item['username']);
						echo $name[$item['username']];
					?></td>
				<?php endif ?>
					<td><?php
						echo '<a href="'.site_url('submissions/'.$view.($filter_user?'/user/'.$filter_user:'').'/problem/'.$item['problem']).'" title="'.$problems[$item['problem']]['name'].'">'.$item['problem'].'</a>';
					?></td>
					<td><?php echo $item['time'] ?></td>
					<td><?php
						$pre_score = ceil($item['pre_score']*$problems[$item['problem']]['score']/10000);
						echo $pre_score;
					?></td>
					<td><?php
						$delay = strtotime($item['time'])-$finish;

						if ($item['coefficient'] === 'error')
							$final_score = 0;
						else
							$final_score = ceil($pre_score*$item['coefficient']/100);

						$neg = FALSE;
						if ($delay<0){
							$delay = 0;
							$neg = TRUE;
						}
						$delay /= 60;
						$h = floor($delay/60);
						$m = floor($delay%60);
						if ($h<10)
							$h="0$h";
						if ($m<10)
							$m="0$m";

						echo '<span style="font-size: 80%; opacity:0.7; '.($neg?'':'color:red;').'">';
						if ($delay === 0)
							echo 'No Delay';
						else
							echo '<span title="Hours">'.$h.'</span>:<span title="Minutes">'.$m.'</span>';
						echo '</span><br>';

						echo $item['coefficient'];
					?></td>
					<td style="font-weight: bold;"><?php echo $final_score ?> </td>
					<td>
						<?php echo filetype_to_language($item['file_type']) ?>
					</td>
					<td class="status"><?php
						if ($item['status'] == 'Uploaded')
							echo 'Uploaded';
						else {
							?><div class="<?php echo status_to_class($item['status'], $item['pre_score']) ?>" data-type="result" ><?php
								if ($item['status']==='SCORE')
									echo $final_score;
								else
									echo $item['status'];
							?></div>
						<?php } ?>
					</td>
					<td>
						<?php if ($item['file_type'] === 'zip' OR $item['file_type'] === 'pdf'): ?>
							<div class="btn shj-orange" data-type="download">Download</div>
						<?php else: ?>
							<div class="btn shj-orange" data-type="code" >Code</div>
						<?php endif ?>
					</td>
					<?php if ($view === 'final' && $user_level > 0): ?>
					<td>
						<?php if ($item['status'] == 'Uploaded'): ?>
							---
						<?php else: ?>
							<div class="btn" data-type="log" >Log</div>
						<?php endif ?>
					</td>
					<?php endif ?>
					<?php if ($user_level>=2): ?>
						<td>
							<a href="#" class="shj_rejudge"><i class="splashy-refresh"></i></a>
						</td>
					<?php endif ?>
					<td><?php echo $item['submit_count']; ?></td>
				</tr>
			<?php endforeach ?>
		</table>

		<p>
		<?php echo $this->shj_pagination->create_links(); ?>
		</p>

	</div> <!-- main_content -->
</div> <!-- scroll-content -->
</div> <!-- main_container -->

<div id="shj_modal" class="reveal-modal xlarge">
	<div class="modal_inside">
		<div style="text-align: center;">Loading<br><img src="<?php echo base_url('assets/images/loading.gif') ?>"/></div>
	</div>
	<a class="close-reveal-modal">&#215;</a>
</div>