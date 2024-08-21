<?php
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 
$referencelabel = "";



if (count($references)):
    $queryType = get_option('reference_query_type') == 'contains' ? 'contains' : 'is+exactly';
    // Dublin Core Title is always 50.
    $referenceId = $slugData['type'] == 'Element' ? $slugData['id'] : 50;
    // Prepare and display skip links.
    if ($options['skiplinks']):
        // Get the list of headers.
        $letters = array('number' => false) + array_fill_keys(range('A', 'Z'), false);
        foreach ($references as $reference => $referenceData):
        if (strpos($reference, '{') !== false) {
            $reference = json_decode($reference);
            //$firstname = $reference->{'first'};
            //$middlename = $reference->{'middle'};
            
            if (!empty($reference->{'first'})) {
            $firstname = $reference->{'first'};
            } else { $firstname = ""; }

            if (!empty($reference->{'middle'})) {
            $middlename = $reference->{'middle'};
            } else { $middlename = ""; }
            
            if (!empty($reference->{'last'})) {
            $lastname = $reference->{'last'};
            } else { $lastname = ""; }        
            
            //$lastname = $reference->{'last'};
            $referencelabel = $lastname . ', ' . $firstname . ' ' . $middlename;
            $reference = $firstname . ' ' . $middlename . ' ' . $lastname;
        }   
            $first_char = function_exists('mb_substr') ? mb_substr($reference, 0, 1) : substr($reference, 0, 1);
            if (strlen($first_char) == 0 || preg_match('/\W|\d/u', $first_char)):
                $letters['number'] = true;
            else:
                $letters[strtoupper($first_char)] = true;
            endif;
        endforeach;
        $pagination_list = '<ul class="pagination_list">';
        foreach ($letters as $letter => $isSet):
            $letterDisplay = $letter == 'number' ? '#0-9' : $letter;
            if ($isSet):
                $pagination_list .= sprintf('<li class="pagination_range"><a href="' . WEB_ROOT . '/references/' . $slug . '?Head%s">%s</a></li>', $letter, $letterDisplay);
            else:
                $pagination_list .= sprintf('<li class="pagination_range"><span>%s</span></li>', $letterDisplay);
            endif;
        endforeach;
        $pagination_list .= '</ul>';
    ?>
<script type="text/javascript">

    $(window).load(function(){
      
var alphabeticallyOrderedDivs = $('.reference-record').sort(function(a, b) {
			return String.prototype.localeCompare.call($(a).data('site').toLowerCase(), $(b).data('site').toLowerCase());
		});
	
var container = $("#aphaOrderRefs");
container.detach().empty().append(alphabeticallyOrderedDivs);
$('#reflist').append(container);

    });

</script>
<div class="pagination reference-pagination" id="pagination-top">
    <?php echo $pagination_list; ?>
</div>
    <?php endif; ?>
    
    <?php
    
                if (strpos($actual_link, 'Head') !== false) {
            $last_let = substr($actual_link, -1); 
            } 
    ?>
    
    
        <h2 class="reference-heading"><?php echo $last_let; ?></h2>
    
    
<div id="reflist" style="display: contents;"></div>
<div id="reference-headings">
        <div id="aphaOrderRefs" style="display: contents;">
    <?php
    $linkSingle = (boolean) get_option('reference_link_to_single');
    $current_heading = '';
    $current_id = '';
    foreach ($references as $reference => $referenceData):
    if (strpos($reference, '{') !== false) {
        $reference = json_decode($reference);
        //$firstname = $reference->{'first'};
        //$middlename = $reference->{'middle'};

        if (!empty($reference->{'first'})) {
            $firstname = $reference->{'first'};
            } else { $firstname = ""; }

        if (!empty($reference->{'middle'})) {
            $middlename = $reference->{'middle'};
            } else { $middlename = ""; }
            
        if (!empty($reference->{'last'})) {
            $lastname = $reference->{'last'};
            } else { $lastname = ""; }

        //$lastname = $reference->{'last'};
        $referencelabel = $lastname . ', ' . $firstname . ' ' . $middlename;
        $reference = $firstname . ' ' . $middlename . ' ' . $lastname;
        }      
        // Add the first character as header if wanted.
        if ($options['headings']):
            
            if ($referencelabel):
            $first_char = function_exists('mb_substr') ? mb_substr($referencelabel, 0, 1) : substr($referencelabel, 0, 1);
            else:
            $first_char = function_exists('mb_substr') ? mb_substr($reference, 0, 1) : substr($reference, 0, 1);
                endif;
                
            if (strlen($first_char) == 0 || preg_match('/\W|\d/u', $first_char)) {
                $first_char = '#0-9';
            }
            $current_first_char = strtoupper($first_char);
            
            if (strpos($actual_link, 'Head') !== false) {
            $last = substr($actual_link, -1); 
            } elseif (strpos($actual_link, 'Head') !== true) {
            $last = 'number';
            }  
            if ($first_char === $last):
            if ($current_heading !== $current_first_char):
                $current_heading = $current_first_char;
                $current_id = $current_heading === '#0-9' ? 'number' : $current_heading;
    ?>
            

    


    <?php
            endif;
            endif;
            endif;
        
        if ($first_char === $last):

    ?>

    <p class="reference-record" data-site="<?php echo $referencelabel ?>">
        <?php if (empty($options['raw'])):
            if ($linkSingle && $referenceData['count'] === 1):
                $record = get_record_by_id('Item', $referenceData['record_id']);
                if ($referencelabel):
                //echo link_to($record, null, $referencelabel);
                $singlelink = link_to($record, null, $referencelabel);
                $singlelink = str_replace("%3A/",":/",$singlelink);
                echo $singlelink;
                else:
                //echo link_to($record, null, $reference);
                $singlelink = link_to($record, null, $reference);
                $singlelink = str_replace("%3A/",":/",$singlelink);
                echo $singlelink;
                
                    endif;
            else:
                $url = 'items/browse?';
                if ($slugData['type'] == 'ItemType'):
                    $url .= 'type=' . $slugData['id'] . '&amp;';
                endif;
                
               // search?query="Rankin+C.+Blount"&query_type=keyword 
                if ($referencelabel):
                    if (!$middlename):
                              // $middlename = urlencode ($middlename);
                                //$firstname = urlencode ($firstname);
                                //$lastname = urlencode ($lastname);
                    //$url = WEB_ROOT . '/items/browse?search=&advanced%5B0%5D%5Bjoiner%5D=and&advanced%5B0%5D%5Belement_id%5D=50&advanced%5B0%5D%5Btype%5D=contains&advanced%5B0%5D%5Bterms%5D=' . $firstname . '+' . $lastname . '&submit_search=Search+for+interviews';
                    $lastname = trim($lastname,'"');
$lastname = str_replace('"', '&advanced%5B1%5D%5Bjoiner%5D=and&advanced%5B1%5D%5Belement_id%5D=249&advanced%5B1%5D%5Btype%5D=contains&advanced%5B1%5D%5Bterms%5D=',$lastname);
$firstname = trim($firstname,'"');
$firstname = str_replace('"', '&advanced%5B1%5D%5Bjoiner%5D=and&advanced%5B1%5D%5Belement_id%5D=249&advanced%5B1%5D%5Btype%5D=contains&advanced%5B1%5D%5Bterms%5D=',$firstname);
                    $url = WEB_ROOT . '/items/browse?search=&advanced%5B0%5D%5Bjoiner%5D=and&advanced%5B0%5D%5Belement_id%5D=249&advanced%5B0%5D%5Btype%5D=contains&advanced%5B0%5D%5Bterms%5D=' . $firstname . '&advanced%5B1%5D%5Bjoiner%5D=and&advanced%5B1%5D%5Belement_id%5D=249&advanced%5B1%5D%5Btype%5D=contains&advanced%5B1%5D%5Bterms%5D=' . $lastname . '&range=&collection=&type=&tags=&featured=&submit_search=Search+for+interviews';
                elseif (!$lastname):
                               // $middlename = urlencode ($middlename);
                                //$firstname = urlencode ($firstname);
                                //$lastname = urlencode ($lastname);
                    //$url = WEB_ROOT . '/items/browse?search=&advanced%5B0%5D%5Bjoiner%5D=and&advanced%5B0%5D%5Belement_id%5D=50&advanced%5B0%5D%5Btype%5D=contains&advanced%5B0%5D%5Bterms%5D=' . $firstname . '+' . $middlename . '&submit_search=Search+for+interviews';
                    
$middlename = trim($middlename,'"');
$middlename = str_replace('"', '&advanced%5B1%5D%5Bjoiner%5D=and&advanced%5B1%5D%5Belement_id%5D=249&advanced%5B1%5D%5Btype%5D=contains&advanced%5B1%5D%5Bterms%5D=',$middlename);
$firstname = trim($firstname,'"');
$firstname = str_replace('"', '&advanced%5B1%5D%5Bjoiner%5D=and&advanced%5B1%5D%5Belement_id%5D=249&advanced%5B1%5D%5Btype%5D=contains&advanced%5B1%5D%5Bterms%5D=',$firstname);
                    $url = WEB_ROOT . '/items/browse?search=&advanced%5B0%5D%5Bjoiner%5D=and&advanced%5B0%5D%5Belement_id%5D=249&advanced%5B0%5D%5Btype%5D=contains&advanced%5B0%5D%5Bterms%5D=' . $firstname . '&advanced%5B1%5D%5Bjoiner%5D=and&advanced%5B1%5D%5Belement_id%5D=249&advanced%5B1%5D%5Btype%5D=contains&advanced%5B1%5D%5Bterms%5D=' . $middlename . '&range=&collection=&type=&tags=&featured=&submit_search=Search+for+interviews';
                elseif (!$firstname):
                                //$middlename = urlencode ($middlename);
                                //$firstname = urlencode ($firstname);
                                //$lastname = urlencode ($lastname);
                    //$url = WEB_ROOT . '/items/browse?search=&advanced%5B0%5D%5Bjoiner%5D=and&advanced%5B0%5D%5Belement_id%5D=50&advanced%5B0%5D%5Btype%5D=contains&advanced%5B0%5D%5Bterms%5D=' . $middlename . '+' . $lastname . '&submit_search=Search+for+interviews';
                    
$lastname = trim($lastname,'"');
$lastname = str_replace('"', '&advanced%5B1%5D%5Bjoiner%5D=and&advanced%5B1%5D%5Belement_id%5D=249&advanced%5B1%5D%5Btype%5D=contains&advanced%5B1%5D%5Bterms%5D=',$lastname);
$middlename = trim($middlename,'"');
$middlename = str_replace('"', '&advanced%5B1%5D%5Bjoiner%5D=and&advanced%5B1%5D%5Belement_id%5D=249&advanced%5B1%5D%5Btype%5D=contains&advanced%5B1%5D%5Bterms%5D=',$middlename);
                    $url = WEB_ROOT . '/items/browse?search=&advanced%5B0%5D%5Bjoiner%5D=and&advanced%5B0%5D%5Belement_id%5D=249&advanced%5B0%5D%5Btype%5D=contains&advanced%5B0%5D%5Bterms%5D=' . $middlename . '&advanced%5B1%5D%5Bjoiner%5D=and&advanced%5B1%5D%5Belement_id%5D=249&advanced%5B1%5D%5Btype%5D=contains&advanced%5B1%5D%5Bterms%5D=' . $lastname . '&range=&collection=&type=&tags=&featured=&submit_search=Search+for+interviews';
                else:
                                //$middlename = urlencode ($middlename);
                                //$firstname = urlencode ($firstname);
                                //$lastname = urlencode ($lastname);
                    //$url = WEB_ROOT . '/items/browse?search=&advanced%5B0%5D%5Bjoiner%5D=and&advanced%5B0%5D%5Belement_id%5D=50&advanced%5B0%5D%5Btype%5D=contains&advanced%5B0%5D%5Bterms%5D=' . $firstname . '+' . $middlename . '+' . $lastname . '&submit_search=Search+for+interviews';
                    
$lastname = trim($lastname,'"');
$lastname = str_replace('"', '&advanced%5B1%5D%5Bjoiner%5D=and&advanced%5B1%5D%5Belement_id%5D=249&advanced%5B1%5D%5Btype%5D=contains&advanced%5B1%5D%5Bterms%5D=',$lastname);
$firstname = trim($firstname,'"');
$firstname = str_replace('"', '&advanced%5B1%5D%5Bjoiner%5D=and&advanced%5B1%5D%5Belement_id%5D=249&advanced%5B1%5D%5Btype%5D=contains&advanced%5B1%5D%5Bterms%5D=',$firstname);
$middlename = trim($middlename,'"');
$middlename = str_replace('"', '&advanced%5B1%5D%5Bjoiner%5D=and&advanced%5B1%5D%5Belement_id%5D=249&advanced%5B1%5D%5Btype%5D=contains&advanced%5B1%5D%5Bterms%5D=',$middlename);
                    $url = WEB_ROOT . '/items/browse?search=&advanced%5B0%5D%5Bjoiner%5D=and&advanced%5B0%5D%5Belement_id%5D=249&advanced%5B0%5D%5Btype%5D=contains&advanced%5B0%5D%5Bterms%5D=' . $firstname . '&advanced%5B1%5D%5Bjoiner%5D=and&advanced%5B1%5D%5Belement_id%5D=249&advanced%5B1%5D%5Btype%5D=contains&advanced%5B1%5D%5Bterms%5D=' . $middlename . '&advanced%5B2%5D%5Bjoiner%5D=and&advanced%5B2%5D%5Belement_id%5D=249&advanced%5B2%5D%5Btype%5D=contains&advanced%5B2%5D%5Bterms%5D=' . $lastname . '&range=&collection=&type=&tags=&featured=&submit_search=Search+for+interviews';
                endif;

                echo '<a href="' . $url . '">' . $referencelabel . '</a>';
                    else:
                    $url .= sprintf('advanced[0][element_id]=%s&amp;advanced[0][type]=%s&amp;advanced[0][terms]=%s',
                    $referenceId, $queryType, urlencode($reference));
                echo '<a href="' . url($url) . '">' . $reference . '</a>';
                        endif;
                
                // Can be null when references are set directly.
                if ($referenceData['count']):
                    echo ' (' . $referenceData['count'] . ')';
                endif;
            endif;
        else:
            echo $reference;
        endif; ?>
    </p>
    
    <?php
    else:
        echo "";
        endif;
   

    ?>
    

    <?php endforeach; ?>
</div>  </div> 

    <?php if ($options['skiplinks']): ?>
<div class="pagination reference-pagination" id="pagination-bottom">
    <?php echo $pagination_list; ?>
</div>
    <?php endif;
endif;
