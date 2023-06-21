<!-- BEGIN CONTENT -->
<div class="row iRow">
    <div class="dashTitle" style="margin-bottom:10px;">
        <?=$title;?>
    </div>
    
    <div class="icontent">
        <div class="queue_date" style="border-bottom:0.5px solid #444;margin-bottom:20px;">
            <div class="rLabel" style="padding:4px 0px;">
                <label class="" for="textinput"><strong><?=$this->lang->line('search_part')?> : </strong></label>
            </div>
            <div class="textfield btm20padding" style="padding-bottom:10px;">
                <select id="search_form" name="search_form" class="form-control nopadding inline" style="width:280px" onchange="bringForm('search_form','search_div');">
                    <option value="0"><?=$this->lang->line('search_feild')?></option>
                    <option value="<?=base_url()?>index.php/search/home/bringForm/reg"><?=$this->lang->line('register_part')?></option>
                    <option value="<?=base_url()?>index.php/search/home/bringForm/drug"><?=$this->lang->line('drug_store_part')?></option>
                    <option value="<?=base_url()?>index.php/search/home/bringForm/remns"><?=$this->lang->line('remains_part')?></option>
                    <option value="<?=base_url()?>index.php/search/home/bringForm/expns"><?=$this->lang->line('expense_part')?></option>
                </select>
            </div>
        </div>   
        <div id="search_div">
        </div>
    </div>
</div>   
<!-- END CONTENT -->