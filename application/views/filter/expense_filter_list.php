<div class="table-responsive table-scrollable customC">
    <table class="table table-striped table-bordered table-advance table-hover">
            <thead>
                <tr>
                    <th width="10%">
                        <span><?=$this->lang->line('id')?></span>
                    </th>
                    
                    <th width="20%">
                        <span><?=$this->lang->line('name')?></span>
                    </th>
                    
                    <th width="20%">
                        <span><?=$this->lang->line('the_amount')?></span>
                    </th>
                         
                    <th width="20%">
                        <span><?=$this->lang->line('item_price')?></span>                                        
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
                    <td><?=$row->name?></td>
                    <td><?=$row->amount?></td>
                    <td><?=$row->price?></td>
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
                        <center><input class="<?php echo ($row->is_updated == 1 ? 'btn btn-primary' : 'btn btn-success');?>" value="<?=$this->lang->line("edit");?>" onclick="javascript:bring_page('<?=base_url()?>index.php/expenses/home/edit/<?=$this->clean_encrypt->encode($row->urn);?>','<?=$row->urn;?>')" style="width:110px;"></center> 
                    </td>
                </tr>
                <?php $i++; }} ?>
            </tbody>
    </table>
    <table class="table table-striped table-bordered table-advance table-hover">  
        <tr>
            <td>
                <ul class= "leftpagination">
                    <li><?= $total ?>  </li>
                </ul>
                <ul class="pagination">
                    <?= $links ?> 
                </ul>
            </td>
        </tr>
    </table>
</div> 