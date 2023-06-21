<!-- <form class="form-horizontal"> -->     
<?php 
    $attributes = array('class' => 'form-horizontal', 'id' => 'filter');
    echo form_open_multipart('register/home/filter', $attributes);
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
                    <label class="" for="textinput"><?=$this->lang->line('drugs')?> : </label>                
                </div>
                <div class="textfield btm20padding">
                    <select id="drugs" name="drugs" class="form-control"  tabindex="4">
                        <option value="0"><?=$this->lang->line('select')?></option>
                        <?php
                            if($all_drug){
                                foreach($all_drug->result() as $sd){
                                    ?>
                                    <option value="<?=$sd->urn?>"><?=$sd->name?></option> 
                                    <?php
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td scope="col" width="33%" class="iEntry">
            <div class="inputfield">
                <div class="rLabel">
                    <label class="" for="textinput"><?=$this->lang->line('drug_type')?> : </label>                
                </div>
                <!--<div class="textfield btm20padding">
                    <input id="drug_type" name="drug_type" type="text" placeholder="<?=$this->lang->line('drug_type')?>" class="form-control iInput" value="">     
                </div>-->
                <div class="textfield btm20padding">
                    <select id="drug_type" name="drug_type" class="chosen-select-rtlx form-control nopadding"  tabindex="4">
                        <option value="0"><?=$this->lang->line('select')?></option>
                        <?php
                            if($spent_drugs){
                                foreach($spent_drugs as $sd){
                                    ?>
                                    <option value="<?=$sd->type?>"><?=$sd->type?></option> 
                                    <?php
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>    
        </td>
        <td scope="col" width="33%" class="iEntry">                
            <div class="inputfield">
                <div class="rLabel">
                    <label class="" for="textinput"><?=$this->lang->line('buy_price')?> : </label>                
                </div>
                <div class="textfield btm20padding">
                      <input id="buy_price" name="buy_price" type="text" placeholder="<?=$this->lang->line('buy_price')?>" class="form-control iInput" value=""> 
                </div>
            </div>   
        </td>
    </tr>
</table>
<table class="table"> 
    <!-- </thead> -->
    <tr>
        <td scope="col" width="100%%" class="iEntry" colspan="3">
            <input type="button" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('search')?>" onclick="submitSearch('<?=base_url()?>index.php/drug_store/home/filter','filter','list_div1');">                                            
            <input type="reset"  id="singlebutton" name="singlebutton" class="btn btn-default" value="<?=$this->lang->line('clean')?>">
            <?php if($this->amc_auth->check_myrole('report')){ ?>
            <input type="button" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('print_excel')?>" onclick="do_it2('<?=base_url()?>index.php/drug_store/home/genDBexelprint','filter');">
            <?php } ?>
        </td>
    </tr>
</table>
<?=form_close()?>