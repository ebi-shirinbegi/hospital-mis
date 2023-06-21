<div class="row iRow">
    <div class="dashTitle">
        <?=$title;?>
    </div>
    <div class="icontent">
        <div class="alert alert-info" style="text-align:center;padding:2px;margin:0">
            <label class="" for="textinput"><?=$this->lang->line('serial_no')?> : <strong><?=$record->patient_id?></strong></label>
        </div>
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
            <table>
            <table class="table" style="margin:0">
                <?php
                if($drug_record){
                    $c = 1;
                    ?>
                    <tr>
                        <td scope="col" width="100%" class="iEntry" colspan="3" style="padding:0">
                            <div class="alert alert-info" style="text-align:center;padding:2px;margin-bottom:0"><?=$this->lang->line('drugs_part')?></div>
                        </td>
                    </tr>
                    <?php
                    $rowspan = count($drug_record);
                    $main_total = 0;
                    foreach($drug_record as $row1){
                        $main_total += $row1->total_price;
                        ?>
                        <tr id="tardivid">     
                            <td scope="col" width="33%" class="iEntry">
                                <table class="table" style="margin:0"> 
                                    <tr>
                                        <td scope="col" width="3%" style="border:none;padding:0;line-height:44px;border-left:0.5px solid #ccc;text-align:center"> 
                                            <div class="badge badge-info">
                                                <?=$c?>
                                            </div>
                                        </td>
                                        <td scope="col" width="30%" style="border:none;padding:0 10px 0px 0px;">    
                                            <div class="inputfield">
                                                <div class="rLabel">
                                                    <label class="" for="textinput"><?=$this->lang->line('drugs')?> : </label>                
                                                </div>
                                                <div class="textfield">
                                                    <?=$this->register_model->spentDrugsByUrn($row1->name)?>      
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table> 
                            </td> 
                            <td scope="col" width="50%" class="iEntry" colspan="1">
                                <table class="table" style="margin:0"> 
                                    <tr>
                                        <td scope="col" width="20%" style="border:none;padding:0;">
                                            <div class="inputfield">
                                                <div class="rLabel">
                                                    <label class="" for="textinput"><?=$this->lang->line('price')?> : </label>
                                                </div>
                                                <div class="textfield">
                                                    <?=$row1->price?> 
                                                </div>
                                            </div>
                                        </td>
                                        <td scope="col" width="20%" style="border:none;padding:0;">
                                            <div class="inputfield">
                                                <div class="rLabel">
                                                    <label class="" for="textinput"><?=$this->lang->line('amount')?> : </label>
                                                </div>
                                                <div class="textfield">
                                                    <?=$row1->amout?> 
                                                </div>
                                            </div>
                                        </td>
                                        <td scope="col" width="60%" style="border:none;padding:0;">
                                            <div class="inputfield">
                                                <div class="rLabel">
                                                    <label class="" for="textinput"><?=$this->lang->line('total')?> : </label>
                                                </div>
                                                <div class="textfield">
                                                    <?=$row1->total_price?> 
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>  
                            <?php if($c==1){?>
                            <td scope="col" width="17%" class="iEntry" rowspan="<?=$rowspan?>" style="text-align:center"> 
                                    <div class="alert alert-info" style="margin-bottom:4px; background-color:#fff">
                                        <label class="" for="textinput"><?=$this->lang->line('priceOfAllDrugs')?> : <strong id="main_total"></strong> &nbsp;<?=$this->lang->line('c_unit');?></label>
                                    </div>    
                            </td> 
                            <?php }?>
                        </tr>
                        <?php
                        $c++;
                    }  
                    $instotal = $main_total;
                }else{
                ?>
                    <tr>
                        <td scope="col" width="33%" class="iEntry" colspan="3">
                            <div class="textfield btm20padding" style="padding-bottom:10px;">
                                <input type="button" id="singlebutton" style="min-width:100px;" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('add_drugs')?>" onclick="bring_page('<?=base_url()?>index.php/register/home/view/<?=$this->clean_encrypt->encode($record->urn)?>/drug','')"> 
                            </div>
                        </td>
                    </tr>
                <?php
                }
                ?>
                <!-- </thead> -->
                <tr>
                    <td scope="col" width="100%%" class="iEntry" colspan="3">
                        <?php
                        if($record->queue_urn != 0)
                        {
                        ?>
                        <!--<input type="button" onclick="bring_page('<?=base_url()?>index.php/queue/home/listRecords','')" class="btn btn-default" value="<?=$this->lang->line('back_to_que_list')?>" >--> 
                        <?php } ?>
                        <input type="button" onclick="bring_page('<?=base_url()?>index.php/register/home/register_list','')" class="btn btn-default" value="<?=$this->lang->line('back_to_list')?>" >
                        <input type="submit" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('edit')?>" onclick="javascript:bring_page('<?=base_url()?>index.php/register/home/register_edit/<?=$this->clean_encrypt->encode($record->urn);?>','<?//=$row->urn;?>')">
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function() {
    document.getElementById('main_total').innerHTML = <?=$instotal?>;
});
</script>