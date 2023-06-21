<div class="row iRow">
    <div class="dashTitle">
        <?=$title;?>
    </div>
    <?php if($record){ ?>
    <div class="icontent">
            <div class="table-responsive text-nowrap"> 
                  <table class="table btm6px">
                    <tr id="tardivid"> 
                        <td scope="col" width="33%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('drugs')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <?=$record->name?>     
                                </div>
                            </div>
                        </td>
                        <td scope="col" width="34%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('drug_type')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <?=$record->type?>    
                                </div>
                            </div>    
                        </td>
                        <td scope="col" width="33%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('amount')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                      <?=$record->amout?>
                                </div>
                            </div>    
                        </td>
                    </tr>
                    <tr>
                        <td scope="col" width="33%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('buy_price')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <?=$record->buy_price?>    
                                </div>
                            </div>    
                        </td>
                        <td scope="col" width="33%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('sale_price')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <?=$record->sale_price?>     
                                </div>
                            </div>    
                        </td>
                        <td scope="col" width="34%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('remark')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <?=$record->remark?> 
                                </div>
                            </div>    
                        </td>   
                    </tr>
                </table>
                </table
                <table class="table btm6px"> 
                    <!-- </thead> -->
                    <tr>
                        <td scope="col" width="100%%" class="iEntry" colspan="3">
                            <input type="button" onclick="bring_page('<?=base_url()?>index.php/drug_store/home/listRecords','')" class="btn btn-default" value="<?=$this->lang->line('backToList')?>" >
                            
                            <input type="button" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('edit')?>" onclick="javascript:bring_page('<?=base_url()?>index.php/drug_store/home/edit/<?=$this->clean_encrypt->encode($record->urn);?>','')">
                        </td>
                    </tr>
                </table>
            </div>
    </div>
    <?php } ?>
</div>