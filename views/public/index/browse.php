<?php
$pageTitle = __('References');
echo head(array(
	'title' => $pageTitle,
	'bodyclass' => 'reference',
));
$reference_hide_empty = (boolean) get_option('reference_hide_empty');
$reference_show_count = (boolean) get_option('reference_show_count');
?>
<div id="primary">
	<h1><?php echo $pageTitle; ?></h1>
	<nav class="items-nav navigation secondary-nav">
		<?php echo public_nav_items(); ?>
	</nav>
	<?php if (empty($types)): ?>
		<p><?php echo __('No reference available.'); ?></p>
	<?php else: ?>
		<ul class='references' style="list-style-type:none; padding-left: 0">
		<?php
		// References are ordered: Item Types, then Elements.
		$type = null;
		$first = true;
		foreach ($references as $slug => $slugData):
			$changedType = $slugData['type'] != $type;
			if ($changedType):
				if ($first):
					$first = false;
				else: ?>
					</ul></li>
			<?php endif; ?>
			<li>
			<?php
				echo '<b>' . ($slugData['type'] == 'ItemType' ?  __('Main Types of Items') : __('Metadata')) . '</b>';
				$type = $slugData['type'];
			?><ul>
		<?php endif; ?>
		<?php 
			if (!$reference_hide_empty || $this->reference()->count($slug) > 0) {
				echo '<li>';
				if ($reference_show_count) {
					echo sprintf('<a href="%s" title="%s">%s</a> (%d)',
						html_escape(url(array('slug' => $slug), 'reference_list')),
						__('Browse %s', $slugData['label']),
						__($slugData['label']),
						$this->reference()->count($slug)
					);
				} else {
					echo sprintf('<a href="%s" title="%s">%s</a>',
						html_escape(url(array('slug' => $slug), 'reference_list')),
						__('Browse %s', $slugData['label']),
						__($slugData['label'])
					);
				}
				echo '</li>';
			}
		?>
		<?php endforeach; ?>
		</ul></li>
	</ul>
	<?php endif; ?>
</div>
<?php echo foot();
