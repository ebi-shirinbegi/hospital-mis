<!-- BEGIN CONTENT -->   
<div class="row iRow">
    <div class="dashTitle" style="margin-bottom:10px;">
        <?=$title;?>
    </div>
    
    <div class="icontent"> 
        <div class="filterDiv" style="padding-top:12px;">
            <button class="btn btn-default filter_button" id='show_btn' onclick="toggleThis('filterForm','show_btn','hide_btn')"><?=$this->lang->line("show_search")?></button>
            <button class="btn btn-default filter_button" id='hide_btn' onclick="toggleThis('filterForm','hide_btn','show_btn')" style='display:none'><?=$this->lang->line("hide_search")?></button>
            <div id="filterForm" class="filterForm">
                <?=$filter?>    
            </div>
        </div>
        <div class="page-content-wrapper" id="list_div1">
            <div class="table-responsive table-scrollable customC">
                <table class="table table-striped table-bordered table-advance table-hover">
                        <thead>
                            <tr>
                                <th width="10%">
                                    <span><?=$this->lang->line('id')?></span>
                                </th>
                                
                                <th width="15%">
                                    <span><?=$this->lang->line('serial_no')?></span>                                        
                                </th>
                                
                                <th width="18%">
                                    <span><?=$this->lang->line('name')?></span>
                                </th>

                                <th width="18%">
                                    <span><?=$this->lang->line('f_name')?></span>
                                </th>

                                <th width="10%">
                                    <span><?=$this->lang->line('visit')?></span>                                        
                                </th>
                                
                                <th width="20%">
                                    <span><?=$this->lang->line('registerDate')?></span>                                        
                                </th>
                                
                                <th width="10%">
                                    <center><span><?=$this->lang->line('actions')?></span></center>                                        
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if($records){
                            $i = $page+1;
                            foreach($records->result() as $row){
                            ?>
                            <tr class="prs">
                                <td><center><?=$i?></center></td>
                                <td><?=$row->patient_id?></td> 
                                <td><?=$row->name?></td>
                                <td><?=$row->f_name?></td>
                                <td>
                                  <?php
                                  $visits = $this->register_model->getStaticName($row->visit,"qu");
                                  if($visits){
                                    echo $visits[0]->name;
                                  }
                                  ?>
                                </td>
                                
                                <td>
                                    <?php
                                    if($row->registerdate){
                                        $reg_date   = explode(" ",$row->registerdate);
                                        $date_arr1  = explode("-",$reg_date[0]);
                                        $jdate      = gregorian_to_jalali($date_arr1[0],$date_arr1[1],$date_arr1[2],"/");
                                        $jdate_arr  = explode("/",$jdate);
                                        $jday       = $jdate_arr[2];
                                        $jmonth     = $jdate_arr[1];
                                        $jyear      = $jdate_arr[0];
                                        echo $jday." - ".$this->lang->line('month'.$jmonth)." - ".$jyear;
                                     }?>
                                </td>
                                <td>
                                    <center><input class="btn btn-success" value="<?=$this->lang->line("view");?>" onclick="javascript:bring_page('<?=base_url()?>index.php/remains/home/view/<?=$this->clean_encrypt->encode($row->urn);?>','<?=$row->urn;?>')" style="width:110px;"></center> 
                                </td>
                            </tr>
                            <?php $i++; }} ?>
                        </tbody>
                </table>
            </div> 
            <!-- end of row  -->
            <ul class= "leftpagination">
                <li><?= $total ?>  </li>
            </ul>
            <ul class="pagination">
                <?= $links ?> 
            </ul>                                        
        </div>
    </div>
</div>   
<!-- END CONTENT -->