<?php
if (count($references)):
    $reference_hide_empty = (bool) get_option('reference_hide_empty');
    $reference_show_count = (bool) get_option('reference_show_count');
    $reference_link_columns = intval(get_option('reference_link_columns'));
    if ($reference_link_columns == 0) $reference_link_columns = 1;
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
                $pagination_list .= sprintf(
                    '<li class="pagination_range"><a href="#%s">%s</a></li>',
                    $letter,
                    $letterDisplay
                );
            else:
                $pagination_list .= sprintf(
                    '<li class="pagination_range"><span>%s</span></li>',
                    $letterDisplay
                );
            endif;
        endforeach;

        $pagination_list .= '</ul>';
    ?>
<div class="pagination reference-pagination" id="pagination-top">
    <?php echo $pagination_list; ?>
</div>
    <?php endif; ?>

<div id="reference-headings">
    <div class="references" style="list-style-type:none; column-count: <?php echo $reference_link_columns; ?>">
    <?php
    $linkSingle = (bool) get_option('reference_link_to_single');
    $current_heading = '';
    $current_id = '';
    $ul_is_open = false;

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
                if ($ul_is_open) echo "</ul>\n";
    ?>
    <ul class="reference-list"><li><h3 class="reference-heading" id="<?php echo $current_id; ?>"><?php echo $current_heading; ?></h3></li>
    <?php
                $ul_is_open = true;
            endif;
        endif;
    ?>

    <li class="reference-record">
        <?php if (empty($options['raw'])):
            if ($linkSingle && $referenceData['count'] === 1):
                $record = get_record_by_id('Item', $referenceData['record_id']);
                echo link_to($record, null, $reference);
            else:
                if (!$reference_hide_empty || $this->reference()->count($slug) > 0):
                    $url = 'items/browse?';
                    if ($slugData['type'] == 'ItemType'):
                        $url .= 'type=' . $slugData['id'] . '&amp;';
                    endif;
                    $url .= sprintf(
                        'advanced[0][element_id]=%s&amp;advanced[0][type]=%s&amp;advanced[0][terms]=%s',
                        $referenceId,
                        $queryType,
                        urlencode($reference)
                    );
                    echo '<a href="' . url($url) . '">' . $reference . '</a>';
                    // Can be null when references are set directly.
                    if ($reference_show_count && $referenceData['count'] > 1) echo ' (' . $referenceData['count'] . ')';
                endif;
            endif;
        else:
            echo $reference;
        endif; ?>
    </li>
    <?php endforeach; ?>
    </div>
</div>

    <?php if ($options['skiplinks']): ?>
<div class="pagination reference-pagination" style="margin-top: 1em" id="pagination-bottom">
    <?php echo $pagination_list; ?>
</div>
    <?php endif;

endif;
