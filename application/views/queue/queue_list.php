<!-- BEGIN CONTENT -->
<div class="row iRow">
	<div class="dashTitle" style="margin-bottom:10px;">
	    <?=$title;?>
	</div>
    
	<div class="icontent">
        <!--queue of this date-->
        <div class="queue_date" style="border-bottom:0.5px solid #444;margin-bottom:20px;">
            <div class="rLabel" style="padding:10px 0px;">
                <label class="" for="textinput"><strong><?=$this->lang->line('queue_of_this')?> : </strong></label>                
            </div>
            <div class="textfield btm20padding" style="padding-bottom:10px;">
                <?php if($jday){?> 
                    <span id="day" name="day" class="form-control nopadding inline" style="padding:6px 20px">
                        <?=$jday?>
                    </span>
                    <span id="month" name="month" class="form-control nopadding inline" style="padding:6px 20px">
                        <?=$this->lang->line('month'.$jmonth)?>
                    </span>
                    <span id="year" name="year" class="form-control nopadding inline" style="padding:6px 20px">
                        <?=$jyear?>
                    </span>
                <?php } else{ ?>
                <?php 
                    $attributes = array('class' => 'form-horizontal', 'id' => 'queue_date');
                    echo form_open_multipart('queue/home/add_date', $attributes);
                ?>
                <select id="day" name="day" class="form-control nopadding inline" style="width:80px">
                    <option value="1"><?=$this->lang->line('day')?></option>
                    <?=$days?>
                </select>
                <select id="month" name="month" class="form-control nopadding inline" style="width:80px">
                    <option value="1"><?=$this->lang->line('month')?></option>
                    <?=$months?>
                </select>
                <select id="year" name="year" class="form-control nopadding inline" style="width:80px">
                    <option value="1"><?=$this->lang->line('year')?></option>
                    <?=$years?>
                </select>
                <input type="submit" id="singlebutton" style="min-width:100px;" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('save_queue')?>">
                <?php form_close();?>
                <?php }?> 
            </div>
        </div>
        <?php if($jday){?>
		<div class="add_tpl">
			<a href="<?= base_url()?>index.php/queue/home/add" class="btn btn-success"><?=$this->lang->line('add_to_queue');?></a>
            <select id="list_type" name="list_type" class="form-control nopadding inline" style="width:220px;position:relative;top:2px;" onchange="loadValue('list_type','list_div1');">  
                <option <?php if($list_type == 0) echo "selected='selected'"; ?> value="<?=base_url()?>index.php/queue/home/listRecords"><?=$this->lang->line('not_visited')?></option>
                <option <?php if($list_type == 1) echo "selected='selected'" ?> value="<?=base_url()?>index.php/queue/home/listRecord"><?=$this->lang->line('visited')?></option>
            </select>
		</div>
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
                                        $checkifregistered = $this->queue_model->checkIfRegistered($row->urn);
                                        //echo "<pre>";print_r($checkifregistered);exit;
                                        if(!$checkifregistered){
                                    ?>
                                        <center><input class="btn btn-danger" value="<?=$this->lang->line("registeration");?>" onclick="window.location = '<?=base_url()?>index.php/register/home/register_add/<?=$this->clean_encrypt->encode($row->urn);?>'" style="width:110px;"></center> 
                                    <?php  
                                        }else{
                                         ?>
                                         <center><input class="btn btn-primary" value="<?=$this->lang->line("view");?>" onclick="window.location = '<?=base_url()?>index.php/register/home/view/<?=$this->clean_encrypt->encode($checkifregistered[0]->urn);?>'" style="width:110px;"></center>
                                         <?php
                                        }}else{              
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
        <?php } ?>
	</div>
</div>   
<!-- END CONTENT -->