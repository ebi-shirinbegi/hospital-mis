<!-- <form class="form-horizontal"> -->     
<?php 
    $attributes = array('class' => 'form-horizontal', 'id' => 'filter');
    echo form_open_multipart('xray/home/filter', $attributes);
?>
<table class="table">
    <!-- <thead> -->
    <tr>
        <td scope="col" width="33%" class="iEntry">
            <div class="inputfield">
                <div class="rLabel">
                    <label class="" for="textinput"><?=$this->lang->line('registerDate')?> : </label>
                </div>
                <div class="textfield btm20padding">
                      <label class="" for="textinput"><?=$this->lang->line('of')?> &nbsp;&nbsp;&nbsp;: </label>
                      <select id="fday" name="fday" class="form-control nopadding inline" style="width:80px">
                            <option value="00"><?=$this->lang->line('day')?> </option>
                            <?=$days?>
                      </select>
                      <select id="fmonth" name="fmonth" class="form-control nopadding inline" style="width:80px">
                            <option value="00"><?=$this->lang->line('month')?> </option>
                            <?=$months?>
                      </select>
                      <select id="fyear" name="fyear" class="form-control nopadding inline" style="width:80px">
                            <option value="0000"><?=$this->lang->line('year')?></option>
                            <?=$years?>
                      </select>
                </div>
                <div class="textfield btm20padding">
                      <label class="" for="textinput"><?=$this->lang->line('to')?> : </label>
                      <select id="tday" name="tday" class="form-control nopadding inline" style="width:80px">
                            <option value="1"><?=$this->lang->line('day')?> </option>
                            <?=$days?>
                      </select>
                      <select id="tmonth" name="tmonth" class="form-control nopadding inline" style="width:80px">
                            <option value="1"><?=$this->lang->line('month')?> </option>
                            <?=$months?>
                      </select>
                      <select id="tyear" name="tyear" class="form-control nopadding inline" style="width:80px">
                            <option value="1"><?=$this->lang->line('year')?></option>
                            <?=$years?>
                      </select>
                </div>
            </div>
        </td>
        <td scope="col" width="33%" class="iEntry">
            <div class="inputfield">
                <div class="rLabel">
                    <label class="" for="textinput"><?=$this->lang->line('serial_no')?> : </label>                
                </div>
                <div class="textfield btm20padding">
                    <input id="patient_id" name="patient_id" type="text" placeholder="<?=$this->lang->line('serial_no')?>" class="form-control iInput" value="">     
                </div>
            </div>
            
            <div class="inputfield">
                <div class="rLabel">
                    <label class="" for="textinput"><?=$this->lang->line('patient_name')?> : </label>                
                </div>
                <div class="textfield btm20padding">
                    <input id="name" name="name" type="text" placeholder="<?=$this->lang->line('patient_name')?>" class="form-control iInput" value="">     
                </div>
            </div>
        </td>
    </tr>
</table>
<table class="table"> 
    <!-- </thead> -->
    <tr>
        <td scope="col" width="100%%" class="iEntry" colspan="3">
            <input type="button" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('search')?>" onclick="submitSearch('<?=base_url()?>index.php/xray/home/filter','filter','list_div1');">                                            
            <input type="reset"  id="singlebutton" name="singlebutton" class="btn btn-default" value="<?=$this->lang->line('clean')?>">
            <?php if($this->amc_auth->check_myrole('report')){ ?>
            <input type="button" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('print_excel')?>" onclick="do_it2('<?=base_url()?>index.php/xray/home/genDBexelprint','filter');">
            <?php } ?>
        </td>   
    </tr>
</table>
<?=form_close()?>