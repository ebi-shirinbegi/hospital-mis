<div class="page-content-wrapper" id="list_div1">
    <div class="table-responsive table-scrollable customC">
        <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                    <tr>
                        <th width="15%">
                            <span><?=$this->lang->line('id')?></span>
                        </th>
                        
                        <th width="15%">
                            <span><?=$this->lang->line('queue_no')?></span>
                        </th>

                        <th width="30%">
                            <span><?=$this->lang->line('name')?></span>
                        </th>

                        <th width="30%">
                            <span><?=$this->lang->line('f_name')?></span>                                        
                        </th>
                        
                        <th width="10%">
                            <center><span><?=$this->lang->line('actions')?></span></center>                                        
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <?php if($records){
                    $i = $page+1;
                    foreach($records as $row){
                    ?>
                    <tr class="prs">
                        <td><?=$i?></td>
                        <td><?=$row->no?></td>
                        <td><?=$row->name?></td>
                        <td><?=$row->f_name?></td>
                        <td>
                        <?php
                        if($list_type == 1){
                        ?>
                            <center><input class="btn btn-danger" value="<?=$this->lang->line("registeration");?>" onclick="doit('<?=base_url()?>index.php/queue/home/visit','<?=$row->urn;?>')" style="width:110px;"></center> 
                        <?php  
                        }else{              
                        ?>
                            <center><input class="btn btn-success" value="<?=$this->lang->line("refered");?>" onclick="doit('<?=base_url()?>index.php/queue/home/visit','<?=$row->urn;?>')" style="width:110px;"></center> 
                        <?php
                        }
                        ?>
                        </td>
                    </tr>
                    <?php $i++; }} ?>
                </tbody>
        </table>
    </div>
    <!-- end of row  -->
    <ul class="pagination">
        <?php foreach ($links as $link) {
             echo "<li>". $link."</li>";
        } ?>
   </ul>        
</div>