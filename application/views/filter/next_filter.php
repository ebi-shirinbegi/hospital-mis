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
                    <label class="" for="textinput"><?=$this->lang->line('next_visit_date')?> : </label>
                </div>
                <div class="textfield btm20padding">
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
            </div>
        </td>
    </tr>
</table>
<table class="table"> 
    <!-- </thead> -->
    <tr>
        <td scope="col" width="100%%" class="iEntry" colspan="3">
            <input type="button" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('search')?>" onclick="submitSearch('<?=base_url()?>index.php/next_visit/home/filter','filter','list_div1');">   
    </tr>
</table>
<?=form_close()?>