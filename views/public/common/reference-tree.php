<?php
if (count($subjects)):
    $queryType = isset($options['query_type']) ? $options['query_type'] : (get_option('reference_query_type') == 'contains' ? 'contains' : 'is+exactly');
    $expanded = isset($options['expanded']) ? $options['expanded'] : get_option('reference_tree_expanded');
    // Dublin Core Subject is always 49.
    $referenceId = 49;
?>
<link href="<?php echo css_src('jquery-simple-folders'); ?>" media="all" rel="stylesheet" type="text/css" />
<div id="reference-headings">
    <?php echo js_tag('jquery-simple-folders'); ?>
    <ul class="tree">
        <?php
            // Create the tree.
            $previous_level = 0;
            foreach ($subjects as $key => $subject) {
                $first = substr($subject, 0, 1);
                $space = strpos($subject, ' ');
                $level = ($first !== '-' || $space === false) ? 0 : $space;
                $subject = trim($level == 0 ? $subject : substr($subject, $space));

                // Close the previous line (done before, because next line is
                // not known yet).
                if ($key == 0) {
                    // Nothing for the first level.
                } elseif ($level > $previous_level) {
                    // Deeper level is always the next one.
                } elseif ($level < $previous_level) {
                    // Higher level.
                   echo '</li>' . PHP_EOL . str_repeat('</ul></li>' . PHP_EOL, $previous_level - $level);
                } else {
                    // First line, deeper or equal level.
                     echo '</li>' . PHP_EOL;
                }

                // Start the line with or without a new sub-list.
                if ($level > $previous_level) {
                    // Deeper level is always the next one.
                    echo PHP_EOL . '<div class="expander' . ($expanded ? ' expanded' : '') . '"></div>';
                    echo '<ul' . ($expanded ? ' class="expanded"' : '') . '><li>';
                } else {
                    echo '<li>';
                }

                if (empty($options['raw'])):
                    echo '<a href="'
                        . url(sprintf(
                            'items/browse?advanced[0][element_id]=%s&amp;advanced[0][type]=%s&amp;advanced[0][terms]=%s',
                            $referenceId,
                            $queryType,
                            urlencode($subject)
                        ))
                        . '">'
                        . $subject
                        . '</a>';
                else:
                    echo $subject;
                endif;

                $previous_level = $level;
            }
            // Manage last line.
            echo '</li>' . PHP_EOL . str_repeat('</ul></li>' . PHP_EOL, $previous_level);
        ?>
    </ul>
</div>
<?php endif;
