<?php
if (count($references)):
	$reference_hide_empty = (boolean) get_option('reference_hide_empty');
	$reference_show_count = (boolean) get_option('reference_show_count');
	$queryType = get_option('reference_query_type') == 'contains' ? 'contains' : 'is+exactly';
	// Dublin Core Title is always 50.
	$referenceId = $slugData['type'] == 'Element' ? $slugData['id'] : 50;
	// Prepare and display skip links.
	if ($options['skiplinks']):
		// Get the list of headers.
		$collator = new Collator('root');
		uksort($references, array($collator, 'compare'));
		$alphabet = (get_option('reference_list_alphabet') != '' ? explode(' ', get_option('reference_list_alphabet')) : array_fill_keys(range('A', 'Z'), false)); 
		$letters = array('number' => false) + array_fill_keys($alphabet, false);
		foreach ($references as $reference => $referenceData):
			$first_char = mb_substr($reference, 0, 1, 'UTF-8');
			if (strlen($first_char) == 0 || preg_match('/\W|\d/u', $first_char)):
				$letters['number'] = true;
			else:
				$first_char = mb_strtoupper($first_char, 'UTF-8');;
				$letters[$first_char] = true;
			endif;
		endforeach;
		$pagination_list = '<ul class="pagination_list">';
		foreach ($letters as $letter => $isSet):
			$letterDisplay = ($letter == 'number' ? '#0-9' : $letter);
			if ($isSet):
				$pagination_list .= sprintf('<li class="pagination_range"><a href="#%s">%s</a></li>', $letter, $letterDisplay);
			else:
				$pagination_list .= sprintf('<li class="pagination_range"><span>%s</span></li>', $letterDisplay);
			endif;
		endforeach;
		$pagination_list .= '</ul>';
	?>
<div class="pagination reference-pagination" id="pagination-top">
	<?php echo $pagination_list; ?>
</div>
	<?php endif; ?>

<div id="reference-headings">
	<ul class="references" style="list-style-type:none"> 
	<?php
	$linkSingle = (boolean) get_option('reference_link_to_single');
	$current_heading = '';
	$current_id = '';
	foreach ($references as $reference => $referenceData):
		// Add the first character as header if wanted.
		if ($options['headings']):
			$first_char = mb_substr($reference, 0, 1, 'UTF-8');
			if (strlen($first_char) == 0 || preg_match('/\W|\d/u', $first_char)) {
				$first_char = '#0-9';
			}
			$current_first_char = mb_strtoupper($first_char, 'UTF-8');
			if ($current_heading !== $current_first_char):
				$current_heading = $current_first_char;
				$current_id = ($current_heading === '#0-9' ? 'number' : $current_heading);
	?>
	<li><h3 class="reference-heading" id="<?php echo $current_id; ?>"><?php echo $current_heading; ?></h3></li>
	<?php
			endif;
		endif;
	?>

	<p class="reference-record">
		<?php if (empty($options['raw'])):
			if ($linkSingle && $referenceData['count'] === 1):
				$record = get_record_by_id('Item', $referenceData['record_id']);
				echo '<li>' . link_to($record, null, $reference) . ' (1)</li>';
			else:
				if (!$reference_hide_empty || $this->reference()->count($slug) > 0):
					$url = 'items/browse?';
					if ($slugData['type'] == 'ItemType'):
						$url .= 'type=' . $slugData['id'] . '&amp;';
					endif;
					$url .= sprintf('advanced[0][element_id]=%s&amp;advanced[0][type]=%s&amp;advanced[0][terms]=%s',
						$referenceId, $queryType, urlencode($reference));
					echo '<li><a href="' . url($url) . '">' . $reference . '</a>';
					// Can be null when references are set directly.
					if ($reference_show_count && $referenceData['count']) echo ' (' . $referenceData['count'] . ')';
					echo '</li>';
				endif;
			endif;
		else:
			echo $reference;
		endif; ?>
	</p>
	<?php endforeach; ?>
	</ul>
</div>

	<?php if ($options['skiplinks']): ?>
	
<div class="pagination reference-pagination" id="pagination-bottom">
	<?php echo $pagination_list; ?>
</div>
	
	<?php endif;
endif;
