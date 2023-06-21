<div class="row iRow">
    <div class="dashTitle">
        <?=$title;?>
    </div>
    <div class="icontent">
        <!-- <form class="form-horizontal"> -->
        <?php 
            $attributes = array('class' => 'form-horizontal', 'id' => 'dr_add');
            echo form_open_multipart("xray/home/xray_material_edit/$enc_urn", $attributes);
        ?>
            <div class="table-responsive text-wrap"> 
              <table class="table btm6px"> 
                    <tr id="tardivid"> 
                        <td scope="col" width="25%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('xray_type')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <select id="xray_type" name="xray_type" class="form-control nopadding">
                                        <option value="0"><?=$this->lang->line('select')?></option>
                                        <?php
                                        if($staticData){
                                            foreach($staticData as $static){
                                                if($record->xray_type == $static->urn){
                                                ?>
                                                    <option value="<?=$static->urn?>" selected="selected"><?=$static->name?></option>
                                                <?php
                                                }else{ 
                                                ?>
                                                    <option value="<?=$static->urn?>"><?=$static->name?></option> 
                                                <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </td>
                        <td scope="col" width="25%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('item_price')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <input id="price" name="price" type="text" placeholder="<?=$this->lang->line('item_price')?>" class="form-control iInput" onkeyup="totalThePrice('price','amount','total')" value="<?=$record->price?>">     
                                </div>
                            </div>    
                        </td>
                        <td scope="col" width="25%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('amount')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                      <input id="amount" name="amount" type="text" placeholder="<?=$this->lang->line('amount')?>" class="form-control iInput" onkeyup="totalThePrice('price','amount','total')" value="<?=$record->amount?>"> 
                                </div>
                            </div>    
                        </td>
                        <td scope="col" width="25%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('total')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <input id="total" name="total" type="text" placeholder="<?=$this->lang->line('total')?>" class="form-control iInput" value="<?=$record->total?>">     
                                </div>
                            </div>    
                        </td>  
                    </tr>
              </table>
              <table class="table btm6px"> 
                    <!-- </thead> -->
                    <tr>
                        <td scope="col" width="100%%" class="iEntry" colspan="3">
                            <input type="submit" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('save')?>">
                            <input type="button" onclick="bring_page('<?=base_url()?>index.php/xray/home/material_list','')" class="btn btn-danger" value="<?=$this->lang->line('cancel')?>" >
                        </td>
                    </tr>
              </table>  
            </div>
        </form>
    </div>
</div>