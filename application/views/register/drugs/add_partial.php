<div class="row iRow">
    <div class="dashTitle">
        <?=$title;?>
    </div>
    <div class="icontent">
        <?php 
            $attributes = array('class' => 'form-horizontal', 'id' => 'add_partial');
            echo form_open_multipart('register/home/add_partial/'.$this->clean_encrypt->encode($record->urn), $attributes);
        ?>
        <div class="table-responsive">
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
                                <!--<img id="pictureImg" src="<?=base_url()?>index.php/Image/index/<?=$profPic->att_path_name; ?>" width="100%">-->
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
                                <?=$record->name?>     
                            </div>
                        </div>
                
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput"><?=$this->lang->line('f_name')?> : </label>                
                            </div>
                            <div class="textfield btm20padding">
                                  <?=$record->f_name?> 
                            </div>
                        </div>    
                        
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput"><?=$this->lang->line('contact')?> : </label>                
                            </div>
                            <div class="textfield btm20padding">
                                <?=$record->contact?>     
                            </div>
                        </div>
                        
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput"><?=$this->lang->line('addrass')?> : </label>                
                            </div>
                            <div class="textfield stikynote text-wrap" style="min-height:36px;padding:4px 8px; margin-bottom:15px;">
                                <?=nl2br($record->address)?>  
                            </div>
                        </div>    
                    </td>

                    <td scope="col" width="33%" class="iEntry">
                            
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput"><?=$this->lang->line('visit')?> : </label>                
                            </div>
                            <div class="textfield btm20padding">
                                  <?php
                                  $visits = $this->register_model->getStaticName($record->visit,"qu");
                                  if($visits){
                                  echo $visits[0]->name;
                                  }
                                  ?>
                            </div>
                        </div>    
                        
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput"><?=$this->lang->line('fee')?> : </label>                
                            </div>
                            <div class="textfield btm20padding">
                                <?=$record->fee?> 
                            </div>
                        </div>  
                            
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput"><?=$this->lang->line('remains')?> : </label>                
                            </div>
                            <div class="textfield btm20padding">
                                <?=$record->remains?>      
                            </div>
                        </div> 
                        
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput"><?=$this->lang->line('totalFee')?> : </label>                
                            </div>
                            <div class="textfield btm20padding">
                                <?=$record->fee+$record->remains?>      
                            </div>
                        </div>
                    </td>
                </tr>
                <?php
                if($teeth_record){
                    foreach($teeth_record as $row){
                        if($row->ill_type == 1){
                        ?>
                        <tr class="customtr text-nowrap" style="background:#fffde7">
                            <td scope="col" width="33%" class="iEntry">
                                <div class="checkbox-container inrow">
                                    <label class="checkbox-label">
                                        <input type="checkbox" checked = "checked" disabled = "disabled">
                                        <span class="checkbox-custom rectangular"></span>
                                        <span class='clabel'><?=$this->lang->line('fill_teeth')?> </span>
                                    </label>
                                </div>      
                            </td>
                            <td colspan="2" scope="col" width="67%" class="iEntry">
                                <?=$fill_view?>
                            </td>
                        </tr>
                        <?php 
                        }elseif($row->ill_type == 2){
                        ?>
                        <tr class="alert alert-success text-nowrap">
                            <td scope="col" width="33%" class="iEntry">
                                <div class="checkbox-container inrow">
                                    <label class="checkbox-label">
                                        <input type="checkbox" checked = "checked" disabled = "disabled">
                                        <span class="checkbox-custom rectangular"></span>
                                        <span class='clabel'><?=$this->lang->line('cover_teeth')?> </span>
                                    </label>
                                </div>
                            </td>
                            <td scope="col" width="67%" class="iEntry" colspan="2">
                                <?=$cover_view?>
                            </td>
                        </tr>
                        <?php
                        }elseif($row->ill_type == 3){
                        ?>
                        <tr class="alert alert-info text-nowrap">
                            <td scope="col" width="33%" class="iEntry">
                                <div class="checkbox-container inrow">
                                    <label class="checkbox-label">
                                        <input type="checkbox" checked = "checked" disabled = "disabled">
                                        <span class="checkbox-custom rectangular"></span>
                                        <span class='clabel'><?=$this->lang->line('build_teeth')?> </span>
                                    </label>
                                </div>
                            </td>
                            <td scope="col" width="67%" class="iEntry" colspan="2">
                                <?=$build_view?>
                            </td>
                        </tr>
                        <?php   
                        }elseif($row->ill_type == 4){
                        ?>
                        <tr class="alert alert-danger text-nowrap">
                            <td scope="col" width="33%" class="iEntry">
                                <div class="checkbox-container inrow">
                                    <label class="checkbox-label">
                                        <input type="checkbox" checked = "checked" disabled = "disabled">
                                        <span class="checkbox-custom rectangular"></span>
                                        <span class='clabel'><?=$this->lang->line('cleaning')?> </span>
                                    </label>
                                </div>
                            </td>
                            <td scope="col" width="67%" class="iEntry" colspan="2">
                                <?=$clean_view?>
                            </td>
                        </tr>
                        <?php   
                        }elseif($row->ill_type == 5){
                        ?>
                        <tr class="alert alert-warning text-nowrap">
                            <td scope="col" width="33%" class="iEntry">
                                <div class="checkbox-container inrow">
                                    <label class="checkbox-label">
                                        <input type="checkbox" checked = "checked" disabled = "disabled">
                                        <span class="checkbox-custom rectangular"></span>
                                        <span class='clabel'><?=$this->lang->line('ortodancy')?> </span>
                                    </label>
                                </div>
                            </td>
                            <td scope="col" width="67%" class="iEntry" colspan="2">
                                <?=$ortho_view?>
                            </td>
                        </tr>
                        <?php   
                        }elseif($row->ill_type == 7){
                        ?>
                        <tr class="alert alert-success text-nowrap">
                            <td scope="col" width="33%" class="iEntry">
                                <div class="checkbox-container inrow">
                                    <label class="checkbox-label">
                                        <input type="checkbox" checked = "checked" disabled = "disabled">
                                        <span class="checkbox-custom rectangular"></span>
                                        <span class='clabel'><?=$this->lang->line('exodontics')?> </span>
                                    </label>
                                </div>
                            </td>
                            <td scope="col" width="67%" class="iEntry" colspan="2">
                                <?=$exo_view?>
                            </td>
                        </tr>
                        <?php   
                        }    
                    }
                }
                ?>    
                
                <tr>
                    <td scope="col" width="33%" class="iEntry">
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput"><?=$this->lang->line('doctor')?> : </label>                
                            </div>
                            <div class="textfield btm20padding">
                                  <?php 
                                    if($record->doctor != '0'){ 
                                        echo $this->register_model->doctorName($record->doctor);
                                    }else{
                                        echo "";
                                    }
                                  ?>
                            </div>
                        </div>
                    </td>
                    <td scope="col" width="33%" class="iEntry">
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput" style="padding:0px 0px 10px 0px;"><?=$this->lang->line('next_visit_date')?> : </label>                
                            </div>
                            <div class="textfield btm10padding">
                                 <?php
                                    $date_arr   = explode(" ",$record->next_visit);
                                    $date_arr1  = explode("-",$date_arr[0]);
                                    $jdate      = gregorian_to_jalali($date_arr1[0],$date_arr1[1],$date_arr1[2],"/");
                                    $jdate_arr  = explode("/",$jdate);
                                    $jday       = $jdate_arr[2];
                                    $jmonth     = $jdate_arr[1];
                                    $jyear      = $jdate_arr[0];
                                 ?>
                                  <span id="day" name="day" class="form-control nopadding inline" style="padding:6px 20px">
                                        <?php 
                                        if($jday != '0'){
                                            echo $jday;
                                        }else{
                                            echo "00";
                                        }
                                        ?>
                                  </span>
                                  <span id="month" name="month" class="form-control nopadding inline" style="padding:6px 20px">
                                        <?php
                                        if($jmonth != '0'){
                                            echo $this->lang->line('month'.$jmonth);
                                        }else{
                                            echo "00";
                                        }
                                        ?>
                                  </span>
                                  <span id="year" name="year" class="form-control nopadding inline" style="padding:6px 20px">
                                        <?php
                                        if($jyear != '0'){
                                            echo $jyear;
                                        }else{
                                            echo '0000';
                                        }?>
                                  </span>
                            </div>
                        </div>
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput" style="padding:0px 0px 10px 0px;"><?=$this->lang->line('next_visit_time')?> : </label>                
                            </div>
                            <div class="inputfield">
                                  <?php
                                  $time_arr = explode(":",$record->next_time);
                                  ?>
                                  <span id="year" name="year" class="form-control nopadding inline" style="padding:6px 20px">
                                        <?=$time_arr[1]?> : <?=$time_arr[0]?>      
                                  </span>
                            </div>
                        </div>
                    </td>
                    <td scope="col" width="33%" class="iEntry"> 
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput"><?=$this->lang->line('used_drugs')?> : </label>                
                            </div>
                            <div class="textfield btm20padding">
                                <?php
                                    if($used_drugs_rec){
                                        $i = 1;
                                        foreach($used_drugs_rec as $u_d){
                                            echo $i." ) ".$this->register_model->drugName($u_d->name)."<br>";
                                            $i++;    
                                        }
                                    }else{
                                    ?>
                                        <input type="button" id="singlebutton" style="min-width:100px;" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('add_drugs')?>" onclick="bring_page('<?=base_url()?>index.php/register/home/view/<?=$this->clean_encrypt->encode($record->urn)?>/used_drug','')"> 
                                    <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <!--<table>-->
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
                </table>
                <table class="table" id="scopdiv"> 
                </table>
                <table class="table"> 
                    <!-- </thead> -->
                    <tr>
                        <td scope="col" width="100%%" class="iEntry" colspan="3">
                            <input type="submit" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('save')?>">
                            <input type="button" onclick="bring_page('<?=base_url()?>index.php/register/home/view/<?=$this->clean_encrypt->encode($record->urn);?>','')" class="btn btn-danger" value="<?=$this->lang->line('cancel')?>" >
                            <input type="reset"  id="singlebutton" name="singlebutton" class="btn btn-default" value="<?=$this->lang->line('clean')?>">
                        </td>
                    </tr>
                </table>
            </table>
        </div>
        </form> 
    </div>
</div>