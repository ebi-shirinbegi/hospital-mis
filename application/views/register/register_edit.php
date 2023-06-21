<div class="row iRow">
    <div class="dashTitle">
        <?=$title;?>
    </div>
    <div class="icontent">
        <div class="alert alert-info" style="text-align:center;padding:2px;margin:0">
            <label class="" for="textinput"><?=$this->lang->line('serial_no')?> : <strong><?=$record->patient_id?></strong></label>
        </div>
        <!-- <form class="form-horizontal"> -->
        <?php 
            $attributes = array('class' => 'form-horizontal', 'id' => 'r_edit');
            echo form_open_multipart('register/home/register_edit/'.$enc_urn, $attributes);
        ?>
            <?php 
            if($record){
            ?>
            <div class="table-responsive text-nowrap"> 
                  <table class="table">
                    <!-- <thead> -->
                      <tr>
                        <td scope="col" width="33%" class="iEntry profilePic">
                            <!-- File Button -->
                            <div class="profile viewpic">
                                <?php
                                    if($profPic){
                                    ?>
                                    <img id="pictureImg" src="<?=base_url()?>uploads/<?=$profPic->att_path_name; ?>" width="100%">                            
                                    <?php
                                    }else {
                                ?>
                                <img id="pictureImg" src="<?=base_url()?>assets/images/profile1.jpg" width="100%">
                                <?php } ?>
                            </div>
                        </td>
                        <td scope="col" width="33%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('name')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <input id="name" name="name" type="text" placeholder="<?=$this->lang->line('name')?>" class="form-control iInput" value="<?=$record->name?>">     
                                </div>
                            </div>
                
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('f_name')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                      <input id="f_name" name="f_name" type="text" placeholder="<?=$this->lang->line('f_name')?>" class="form-control iInput" value="<?=$record->f_name?>"> 
                                </div>
                            </div>    
                        
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('contact')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <input id="contact" name="contact" type="text" placeholder="<?=$this->lang->line('contact')?>" class="form-control iInput" value="<?=$record->contact?>">     
                                </div>
                            </div>   
                            
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('addrass')?> : </label>                
                                </div>
                                <div class="textfield btm10padding">
                                    <textarea class="form-control stikynote" id="addrass" name="addrass" rows="1"><?=$record->address?></textarea>
                                </div>
                            </div> 
                        </td>

                        <td scope="col" width="33%" class="iEntry">
                            
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('visit')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                      <select id="visit" name="visit" class="form-control nopadding">
                                        <option value="0" <?php if($record->visit == 0){echo "selected";}?>><?=$this->lang->line('select')?></option>
                                        <?php
                                        if($next_visit){
                                            foreach($next_visit as $next){
                                                if($record->visit==$next->urn){
                                                    ?>
                                                    <option value="<?=$next->urn?>" selected='selected'><?=$next->name?></option>
                                                    <?php   
                                                }else{
                                                    ?>
                                                    <option value="<?=$next->urn?>"><?=$next->name?></option> 
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>    
                        
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('fee')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <input id="fee" name="fee" type="text" placeholder="<?=$this->lang->line('fee')?>" class="form-control iInput" value="<?=$record->fee?>" onkeyup="totalTheFee('fee','remains','totalFee')"> 
                                </div>
                            </div>  
                            
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('remains')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <input id="remains" name="remains" type="text" placeholder="<?=$this->lang->line('remains')?>" class="form-control iInput" value="<?=$record->remains?>" onkeyup="totalTheFee('fee','remains','totalFee')">     
                                </div>
                            </div>
                            
                            <div class="inputfield">
                                <?php
                                    $totalFee = $record->fee+$record->remains;
                                ?>
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('totalFee')?> : </label>
                                </div>
                                <div class="textfield btm20padding">
                                    <input id="totalFee" name="totalFee" type="text" placeholder="<?=$this->lang->line('totalFee')?>" class="form-control iInput" value="<?=$totalFee?>">     
                                </div>
                            </div> 
                        </td>
                      </tr>
                      <tr class="customtr" style="background:#fffde7">
                        <td scope="col" width="33%" class="iEntry">
                            <div class="checkbox-container inrow">
                                <label class="checkbox-label">
                                    <input type="checkbox"  id="fillit" onclick="showHide('fillit','fill')" name = "fill" value="1" <?php if($record->fill_teeth == 1){echo "checked";}?>>
                                    <span class="checkbox-custom rectangular"></span>
                                    <span class='clabel'><?=$this->lang->line('fill_teeth')?> </span>
                                </label>
                            </div>      
                        </td>
                        <td colspan="2" scope="col" width="67%" class="iEntry">
                            <?php if($record->fill_teeth == 1){
                                echo $fill_edit;
                            }else{
                                echo $fill_add;
                            }
                            ?>
                        </td>
                    </tr>
                    <tr class="alert alert-success">
                        <td scope="col" width="33%" class="iEntry">
                            <div class="checkbox-container inrow">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="coverit" onclick="showHide('coverit','cover')" name="cover" value="1" <?php if($record->cover_teeth == 1){echo "checked";}?>>
                                    <span class="checkbox-custom rectangular"></span>
                                    <span class='clabel'><?=$this->lang->line('cover_teeth')?> </span>
                                </label>
                            </div>
                        </td>
                        <td scope="col" width="67%" class="iEntry" colspan="2">
                            <?php if($record->cover_teeth == 1){
                                echo $cover_edit;
                            }else{
                                echo $cover_add;
                            }?>
                        </td>
                    </tr>
                    <tr class="alert alert-info">
                        <td scope="col" width="33%" class="iEntry">
                            <div class="checkbox-container inrow">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="buildit" onclick="showHide('buildit','build')" name="build" value="1" <?php if($record->build_teeth == 1){echo "checked";}?>>
                                    <span class="checkbox-custom rectangular"></span>
                                    <span class='clabel'><?=$this->lang->line('build_teeth')?> </span>
                                </label>
                            </div>
                        </td>
                        <td scope="col" width="67%" class="iEntry" colspan="2">
                            <?php 
                            if($record->build_teeth == 1){
                                echo $build_edit;
                            }else{
                                echo $build_add;
                            }?>
                        </td>
                    </tr>
                    <tr class="alert alert-danger">
                        <td scope="col" width="33%" class="iEntry">
                            <div class="checkbox-container inrow">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="cleanit" onclick="showHide('cleanit','clean')" name="clean" value="1" <?php if($record->clean == 1){echo "checked";}?>>
                                    <span class="checkbox-custom rectangular"></span>
                                    <span class='clabel'><?=$this->lang->line('cleaning')?> </span>
                                </label>
                            </div>
                        </td>
                        <td scope="col" width="67%" class="iEntry" colspan="2">
                            <?php if($record->clean == 1){
                                echo $clean_edit;
                            }else{
                                echo $clean_add;
                            }?>
                        </td>
                    </tr>
                    <tr class="alert alert-warning">
                        <td scope="col" width="33%" class="iEntry">
                            <div class="checkbox-container inrow">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="orthodant" onclick="showHide('orthodant','orthodan')" name="orthodant" value="1" <?php if($record->ortodancy == 1){echo "checked";}?>>
                                    <span class="checkbox-custom rectangular"></span>
                                    <span class='clabel'><?=$this->lang->line('ortodancy')?> </span>
                                </label>
                            </div>
                        </td>
                        <td scope="col" width="67%" class="iEntry" colspan="2">
                            <?php if($record->ortodancy == 1){
                                echo $ortho_edit;
                            }else{
                                echo $ortho_add;
                            }?>
                        </td>
                    </tr>
                    <tr class="alert alert-success">
                        <td scope="col" width="33%" class="iEntry">
                            <div class="checkbox-container inrow">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="exod" onclick="showHide('exod','exo')" name="exo" value="1" <?php if($record->exodontics == 1){echo "checked";}?>>
                                    <span class="checkbox-custom rectangular"></span>
                                    <span class='clabel'><?=$this->lang->line('exodontics')?> </span>
                                </label>
                            </div>
                        </td>
                        <td scope="col" width="67%" class="iEntry" colspan="2">
                            <?php if($record->exodontics == 1){
                                echo $exo_edit;
                            }else{
                                echo $exo_add;
                            }?>
                        </td>
                    </tr>
                    <tr>
                        <td scope="col" width="33%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('doctor')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                      <select id="doctor" name="doctor" class="form-control nopadding">
                                        <option value="0"><?=$this->lang->line('select')?></option>
                                        <?php
                                            if($doctors){
                                                foreach($doctors as $doc){
                                                    if($record->doctor == $doc->urn){
                                                    ?>
                                                    <option selected value="<?=$doc->urn?>"><?=$doc->name?></option> 
                                                    <?php
                                                    }else{
                                                    ?>
                                                    <option value="<?=$doc->urn?>"><?=$doc->name?></option>
                                                    <?php
                                                    }
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
                                    <label class="" for="textinput"><?=$this->lang->line('next_visit_date')?> : </label>
                                </div>
                                <div class="textfield btm10padding">
                                      <select id="day" name="day" class="form-control nopadding inline" style="width:80px">
                                            <option value=""><?=$this->lang->line('day')?> </option>
                                            <?=$days?>
                                      </select>
                                      <select id="month" name="month" class="form-control nopadding inline" style="width:80px">
                                            <option value=""><?=$this->lang->line('month')?> </option>
                                            <?=$months?>
                                      </select>
                                      <select id="year" name="year" class="form-control nopadding inline" style="width:80px">
                                            <option value=""><?=$this->lang->line('year')?></option>
                                            <?=$years?>
                                      </select>                                
                                </div>
                            </div>
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('next_visit_time')?> : </label>
                                </div>
                                <div class="textfield btm10padding">
                                      <select id="minute" name="minute" class="form-control nopadding inline" style="width:80px">
                                            <option value="" ><?=$this->lang->line('minute')?> </option>
                                            <?=$nminute?>
                                      </select>
                                      <select id="hour" name="hour" class="form-control nopadding inline" style="width:80px">
                                            <option value="" ><?=$this->lang->line('hour')?> </option>
                                            <option value="">00</option>
                                            <?=$nhour?>
                                      </select>
                                </div>
                            </div>
                        </td>
                        <td scope="col" width="33%" class="iEntry"> 
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('used_drugs')?> : </label>                
                                </div>
                                <div class="textfield">
                                    <?php
                                    if($used_drugs_rec){
                                        $cnt = 1;
                                        foreach($used_drugs_rec AS $usd_drg){
                                            ?>
                                            <input type="hidden" id="used_drug_urn[]" name="used_drug_urn[]" value="<?=$usd_drg->urn?>">                                      
                                            <select id="used_drugs[]" name="used_drugs[]" class="form-control nopadding" style="width:255px;display:inline;margin-bottom:5px">
                                                <option value="0"><?=$this->lang->line('select')?></option>
                                                <?php
                                                    if($used_drugs){
                                                        foreach($used_drugs as $ud){
                                                            if($usd_drg->name == $ud->urn){
                                                            ?>
                                                            <option selected='selected' value="<?=$ud->urn?>"><?=$ud->name?></option> 
                                                            <?php
                                                            }else{
                                                            ?>
                                                            <option value="<?=$ud->urn?>"><?=$ud->name?></option> 
                                                            <?php
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </select>
                                            <?php if($cnt == 1){ ?>
                                            <input type="button" class="btn btn-success" value="+" onclick="addMultiple('<?=base_url()?>index.php/register/home/multiple_used_drug','target_drug',0)" style="min-width:40px;margin-right:5px">
                                            <?php
                                            }
                                            ?>
                                            <br>
                                            <?php
                                            $cnt++;
                                        }
                                    }else{
                                    ?>
                                    <select id="used_drugs[]" name="used_drugs[]" class="form-control nopadding" style="width:255px;display:inline">
                                        <option value="0"><?=$this->lang->line('select')?></option>
                                        <?php
                                            if($used_drugs){
                                                foreach($used_drugs as $ud){
                                                    ?>
                                                    <option value="<?=$ud->urn?>"><?=$ud->name?></option> 
                                                    <?php
                                                }
                                            }
                                        ?>
                                    </select>
                                    <input type="button" class="btn btn-success" value="+" onclick="addMultiple('<?=base_url()?>index.php/register/home/multiple_used_drug','target_drug',0)" style="min-width:40px;margin-right:5px">
                                    <?php } ?>
                                </div>
                                <div class="textfield" id="target_drug">
                                </div> 
                            </div>
                        </td>
                    </tr>
                    <?php 
                    if($spent_drugs_record){
                        $cnt1 = 1;
                        $count = count($spent_drugs_record);
                        foreach($spent_drugs_record AS $apnt_drg){
                            if($cnt1 == 1){
                    ?>
                    <tr>
                        <td scope="col" width="33%" class="iEntry" colspan="3">
                            <span class="btn btn-success ino">1</span><input type="button" class="btn btn-success" value="+" onclick="addMultiple('<?=base_url()?>index.php/register/home/multiple','scopdiv',<?=$count?>)">
                        </td>
                    </tr>
                            <?php } ?>
                    <input type="hidden" id="spent_drug_urn[]" name="spent_drug_urn[]" value="<?=$apnt_drg->urn?>"> 
                    <tr id="tardivid">
                        <td scope="col" width="33%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('drugs')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <select id="drug[]" name="drug[]" class="chosen-select-rtlx form-control nopadding"  tabindex="4">
                                        <option value="0"><?=$this->lang->line('select')?></option>
                                        <?php
                                            if($spent_drugs){
                                                foreach($spent_drugs as $sd){
                                                    if($apnt_drg->name == $sd->urn){
                                                    ?>
                                                    <option selected="selected" value="<?=$sd->urn?>"><?=$sd->name?></option> 
                                                    <?php
                                                    }else{
                                                    ?>
                                                    <option value="<?=$sd->urn?>"><?=$sd->name?></option> 
                                                    <?php
                                                    }
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div> 
                        </td> 
                        <td scope="col" width="67%" class="iEntry" colspan="2">
                            <table class="table" style="margin-bottom:0px;"> 
                                <tr>
                                    <td scope="col" width="20%" style="border:none;padding:0;">
                                        <div class="inputfield">
                                            <div class="rLabel">
                                                <label class="" for="textinput"><?=$this->lang->line('price')?> : </label>                
                                            </div>
                                            <div class="textfield btm20padding">
                                                <input id="price<?=$cnt1?>" name="price[]" type="text" placeholder="<?=$this->lang->line('price')?>" class="form-control iInput" style="min-width:140px; width:140px; display:inline;" onkeyup="totalThePrice('price<?=$cnt1?>','amount<?=$cnt1?>','total<?=$cnt1?>')" value="<?=$apnt_drg->price?>"> 
                                            </div>
                                        </div>
                                    </td>
                                    <td scope="col" width="20%" style="border:none;padding:0;">
                                        <div class="inputfield">
                                            <div class="rLabel">
                                                &nbsp;&nbsp;&nbsp;<label class="" for="textinput"><?=$this->lang->line('amount')?> : </label>                
                                            </div>
                                            <div class="textfield btm20padding">
                                                &nbsp;&nbsp;<input id="amount<?=$cnt1?>" name="amount[]" type="text" placeholder="<?=$this->lang->line('amount')?>" class="form-control iInput" style="width:120px; min-width:120px; display:inline;" onkeyup="totalThePrice('price<?=$cnt1?>','amount<?=$cnt1?>','total<?=$cnt1?>')" value="<?=$apnt_drg->amout?>"> 
                                            </div>
                                        </div>
                                    </td>
                                    <td scope="col" width="60%" style="border:none;padding:0;">
                                        <div class="inputfield">
                                            <div class="rLabel">
                                                &nbsp;&nbsp;&nbsp;<label class="" for="textinput"><?=$this->lang->line('total')?> : </label>                
                                            </div>
                                            <div class="textfield btm20padding">
                                                &nbsp;&nbsp;<input id="total<?=$cnt1?>" name="total[]" type="text" placeholder="<?=$this->lang->line('total')?>" class="form-control iInput" style="width:155px; min-width:157px; display:inline;" onkeyup="totalThePrice('price<?=$cnt1?>','amount<?=$cnt1?>','total<?=$cnt1?>')" value="<?=$apnt_drg->total_price?>"> 
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>   
                    </tr>
                    <?php $cnt1++; }}else{ ?>
                    <tr>
                        <td scope="col" width="33%" class="iEntry" colspan="3">
                            <span class="btn btn-success ino">1</span><input type="button" class="btn btn-success" value="+" onclick="addMultiple('<?=base_url()?>index.php/register/home/multiple','scopdiv',0)">
                        </td>
                    </tr>
                    <tr id="tardivid">
                        <td scope="col" width="33%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('drugs')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <select id="drug[]" name="drug[]" class="chosen-select-rtlx form-control nopadding"  tabindex="4">
                                        <option value="0"><?=$this->lang->line('select')?></option>
                                        <?php
                                            if($spent_drugs){
                                                foreach($spent_drugs as $sd){
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
                        <td scope="col" width="67%" class="iEntry" colspan="2">
                            <table class="table"> 
                                <tr>
                                    <td scope="col" width="20%" style="border:none;padding:0;">
                                        <div class="inputfield">
                                            <div class="rLabel">
                                                <label class="" for="textinput"><?=$this->lang->line('price')?> : </label>                
                                            </div>
                                            <div class="textfield btm20padding">
                                                <input id="price1" name="price[]" type="text" placeholder="<?=$this->lang->line('price')?>" class="form-control iInput" style="min-width:140px; width:140px; display:inline;" onkeyup="totalThePrice('price1','amount1','total1')"><!--<span style="border: 0.6px solid #ddd;padding: 6px 10px 9px;"><strong><?=$this->lang->line('c_unit');?></strong></span>--> 
                                            </div>
                                        </div>
                                    </td>
                                    <td scope="col" width="20%" style="border:none;padding:0;">
                                        <div class="inputfield">
                                            <div class="rLabel">
                                                &nbsp;&nbsp;&nbsp;<label class="" for="textinput"><?=$this->lang->line('amount')?> : </label>                
                                            </div>
                                            <div class="textfield btm20padding">
                                                &nbsp;&nbsp;<input id="amount1" name="amount[]" type="text" placeholder="<?=$this->lang->line('amount')?>" class="form-control iInput" style="width:120px; min-width:120px; display:inline;" onkeyup="totalThePrice('price1','amount1','total1')"> 
                                            </div>
                                        </div>
                                    </td>
                                    <td scope="col" width="60%" style="border:none;padding:0;">
                                        <div class="inputfield">
                                            <div class="rLabel">
                                                &nbsp;&nbsp;&nbsp;<label class="" for="textinput"><?=$this->lang->line('total')?> : </label>                
                                            </div>
                                            <div class="textfield btm20padding">
                                                &nbsp;&nbsp;<input id="total1" name="total[]" type="text" placeholder="<?=$this->lang->line('total')?>" class="form-control iInput" style="width:155px; min-width:157px; display:inline;" onkeyup="totalThePrice('price1','amount1','total1')"> 
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>   
                    </tr>
                    <?php } ?>
                </table>
                <table class="table" id="scopdiv"> 
                </table
                <table class="table"> 
                    <!-- </thead> -->
                    <tr>
                        <td scope="col" width="100%%" class="iEntry" colspan="3">
                            <input type="submit" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('save')?>">
                            <input type="button" onclick="bring_page('<?=base_url()?>index.php/register/home/view/<?=$this->clean_encrypt->encode($record->urn);?>','')" class="btn btn-danger" value="<?=$this->lang->line('cancel')?>" > 
                            <!--<input type="reset"  id="singlebutton" name="singlebutton" class="btn btn-default" value="<?=$this->lang->line('clean')?>">-->
                        </td>
                    </tr>
                </table>

            </div>
            <?php } ?>
        </form>
    </div>
</div>