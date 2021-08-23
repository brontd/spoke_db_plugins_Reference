<style type="text/css">
.reference-boxes {
    text-align: center;
}
.input-block ul {
    list-style: none outside none;
}
</style>

<?php if (($max = (count($slugs) * 3 + 20)) > ini_get('max_input_vars')): ?>
<p><strong style="color:red;"><?php echo __('WARNING'); ?></strong></p>
<p><?php echo __('There are too many elements and this form cannot be saved. Remove unused elements or increase the parameter \'max_input_vars\' to %d or more in your php.ini.', $max); ?></p>
<?php endif; ?>

<fieldset id="fieldset-reference-general">
    <p>
        <?php echo __('Please note: most of these options for list and for tree can be overridden in the theme.'); ?>
    </p>
</fieldset>

<fieldset id="fieldset-reference-list"><legend><?php echo __('References Indexes'); ?></legend>
    <div class="field">
        <div>
            <?php echo $this->formLabel('reference_slugs', __('Display References')); ?>
        </div>
        <div class="inputs">
            <p class="explanation">
                <?php echo __('Select the elements to display and define a slug so the references will be available at "references/:slug".'); ?>
                <?php echo __('Slugs should be unique.'); ?>
            </p>
            <table id="hide-elements-table">
                <thead>
                    <tr>
                        <th class="references-boxes"><?php echo __('Item Type / Element'); ?></th>
                        <th class="references-boxes"><?php echo __('Display'); ?></th>
                        <th class="references-boxes"><?php echo __('Slug'); ?></th>
                        <th class="references-boxes"><?php echo __('Label'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $current_type = null;
                $current_element_set = null;
                foreach ($slugs as $slug => $slugData):
                    $record = $tables[$slugData['type']]->find($slugData['id']);
                    if (empty($record)) {
                        continue;
                    }
                    $idKey = '[' . $slugData['type'] . '][' . $slugData['id'] . ']';
                    if ($slugData['type'] !== $current_type):
                        $current_type = $slugData['type'];
                    ?>
                    <tr>
                        <th colspan="4">
                            <strong><?php echo Inflector::humanize($current_type, 'all'); ?></strong>
                        </th>
                    </tr>
                    <?php endif;
                    if ($current_type == 'Element'):
                        if ($record->set_name != $current_element_set):
                            $current_element_set = $record->set_name; ?>
                    <tr>
                        <th colspan="4">
                            <strong><?php echo $current_element_set; ?></strong>
                        </th>
                    </tr>
                        <?php endif;
                    endif; ?>
                    <tr>
                        <td><?php echo $record->name; ?></td>
                        <td class="reference-boxes">
                            <?php echo $this->formCheckbox(
                                'actives' . $idKey,
                                true,
                                array('checked' => (bool) $slugData['active'])
                            ); ?>
                        </td>
                        <td class="reference-boxes">
                            <?php echo $this->formText(
                                'slugs' . $idKey,
                                $slug,
                                null
                            ); ?>
                        </td>
                        <td class="reference-boxes">
                            <?php echo $this->formText(
                                'labels' . $idKey,
                                __($slugData['label']), 
                                null
                            ); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel(
                'reference_list_skiplinks',
                __('Print skip links')
            ); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox(
                'reference_list_skiplinks', 
                true,
                array('checked' => (bool) get_option('reference_list_skiplinks'))
            ); ?>
            <p class="explanation">
                <?php echo __('Print skip links at the top and bottom of each page, which link to the alphabetical headers.'); ?>
                <?php echo __('Note that, if headers are turned off, skip links do not work.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel(
                'reference_list_alphabet',
                __('Alphabetical headers')
            ); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formText(
                'reference_list_alphabet', 
                get_option('reference_list_alphabet'), 
                null
            ); ?>
            <p class="explanation">
                <?php echo __('Sets the alphabetical headers to be used with skip links; headers are to be separated by blank space.'); ?>
                <?php echo __('If no value is given, default English alphabet will be used.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel(
                'reference_list_headings',
                __('Print headings')
            ); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox(
                'reference_list_headings', 
                true,
                array('checked' => (bool) get_option('reference_list_headings'))
            ); ?>
            <p class="explanation">
                <?php echo __('Print headers for each section (#0-9 and symbols, A, B, etc.)'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel(
                'reference_query_type',
                __('Query Type')
            ); ?>
        </div>
        <div class='inputs five columns omega'>
            <?php
                echo $this->formRadio(
                    'reference_query_type', 
                    get_option('reference_query_type') ?: 'is exactly', 
                    null, 
                    array(
                        'is exactly' => __('Is Exactly'),
                        'contains' => __('Contains'),
            )); ?>
            <p class="explanation">
                <?php echo __('The type of query defines how elements are regrouped (see the advanced search).'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel(
                'reference_link_to_single',
                __('Link to Single Records')
            ); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox(
                'reference_link_to_single',
                true,
                array('checked' => (bool) get_option('reference_link_to_single'))
            ); ?>
            <p class="explanation">
                <?php echo __('When a reference has only one item, link to it directly instead of to the items/browse page.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel(
                'reference_hide_empty',
                __('Hide empty references')
            ); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox(
                'reference_hide_empty', 
                true,
                array('checked' => (bool) get_option('reference_hide_empty'))
            ); ?>
            <p class="explanation">
                <?php echo __('When a reference has no items, hides the correspondent link.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel(
                'reference_show_count',
                __('Show references count')
            ); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox(
                'reference_show_count', 
                true,
                array('checked' => (bool) get_option('reference_show_count'))
            ); ?>
            <p class="explanation">
                <?php echo __('Shows reference count beside reference name (if more than 1 Item).'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel(
                'reference_link_columns',
                __('References display mode')
            ); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formSelect(
                'reference_link_columns',
                get_option('reference_link_columns'),
                array(),
                array(
                    '1' => __('Whole page'),
                    '2' => __('2 columns'),
                    '3' => __('3 columns')
                )
            ); ?>
            <p class="explanation">
                <?php echo __('Type of layout for the reference links: single page with one column (best choice for few links), or list spread over 2 or 3 columns.'); ?>
            </p>
        </div>
    </div>
</fieldset>

<fieldset id="fieldset-reference-tree"><legend><?php echo __('Hierarchy of Subjects'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel(
                'reference_tree_enabled',
                __('Enable tree view')
            ); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox(
                'reference_tree_enabled', 
                true,
                array('checked' => (bool) get_option('reference_tree_enabled'))
            ); ?>
            <p class="explanation">
                <?php echo __(
                    'Enable the page and display the link "%s" to the hierarchical view in the navigation bar.',
                    '<a href="' . url(ReferencePlugin::REFERENCE_PATH_TREE) . '">' . url(ReferencePlugin::REFERENCE_PATH_TREE) . '</a>'
                ); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel(
                'reference_tree_expanded',
                __('Expand tree')
            ); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox(
                'reference_tree_expanded', 
                true,
                array('checked' => (bool) get_option('reference_tree_expanded'))
            ); ?>
            <p class="explanation">
                <?php echo __('When checked, the tree is displayed already expanded.'); ?>
                <?php echo __('This option can be overridden by the theme.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel(
                'reference_tree_hierarchy',
                __('Set the hierarchy of subjects')
            ); ?>
        </div>
        <div class="inputs five columns omega">
           <div class='input-block'>
                <?php echo $this->formTextarea(
                    'reference_tree_hierarchy',
                    get_option('reference_tree_hierarchy'),
                    array(
                        'rows' => 20,
                        'cols' => 60,
                        'class' => array('textinput'),
                        // The place holder can't use end of line, so a symbol
                        // is used for it.
                        'placeholder' => '
                            Europe ↵
                            - France ↵
                            -- Paris ↵
                            -- Lyon ↵
                            -- Marseille ↵
                            - United Kingdom ↵
                            -- London ↵
                            -- Manchester ↵
                            Asia ↵
                        '
                )); ?>
            </div>
            <p class="explanation">
                <?php echo __('If any, write the hierarchy of all your subjects in order to display them in the "Hierarchy of Subjects" page.'); ?>
                <?php echo __('Format is: one subject per line, preceded by zero, one or more "-" to indicate the hierarchy level. Separate the "-" and the subject with an empty space. Empty lines are not considered.'); ?>
            </p>
        </div>
    </div>
</fieldset>
