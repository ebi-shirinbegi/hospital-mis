<div class="row iRow">
    <div class="dashTitle">
        <?=$title;?>
    </div>
    <div class="icontent">
        <!-- <form class="form-horizontal"> -->
        <?php 
            $attributes = array('class' => 'form-horizontal', 'id' => 'dr_add');
            echo form_open_multipart('xray/home/xray_add_new', $attributes);
        ?>
            <div class="add_tpl alert alert-info">
                <p class=""><?=$this->lang->line('xray_add_new')?></p>
            </div>
            <div class="table-responsive text-wrap"> 
              <table class="table btm6px">
                <tr>
                    <td scope="col" width="50%" class="iEntry" colspan="1">
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput"><?=$this->lang->line('serial_no')?> : </label>
                            </div>
                            <div class="textfield btm20padding">
                                  <select id="p_id" name="p_id" class="form-control nopadding chosen-select" onchange="bringPatientName('<?=base_url()?>index.php/xray/home/patientNameByIdNew','p_id','name')">
                                    <option value="0"><?=$this->lang->line('select')?></option>
                                    <?php
                                    if($patientid){
                                        foreach($patientid as $p_id){
                                            if(mb_substr($p_id->patient_id, 0, 1, "UTF-8") == "X"){
                                            ?>
                                            <option value="<?=$p_id->patient_id?>"><?=$p_id->patient_id?></option>
                                            <?php
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </td>
                    <td scope="col" width="50%" class="iEntry" colspan="2">
                        <div class="rLabel">
                            <label class="" for="textinput"><?=$this->lang->line('patient_name')?> : </label>                
                        </div>
                        <div class="textfield btm20padding">
                            <input id="name" name="name" type="text" placeholder="<?=$this->lang->line('patient_name')?>" class="form-control iInput" >     
                        </div>
                    </td>
                </tr>
              </table> 
              <table class="table btm6px">
                    <tr>
                        <td scope="col" width="33%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('fee')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <input id="fee" name="fee" type="text" placeholder="<?=$this->lang->line('fee')?>" class="form-control iInput" onkeyup="totalTheFee('fee','remains','totalFee')"> 
                                </div>
                            </div>  
                        </td>
                        <td scope="col" width="33%" class="iEntry"> 
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('remains')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <input id="remains" name="remains" type="text" placeholder="<?=$this->lang->line('remains')?>" class="form-control iInput" onkeyup="totalTheFee('fee','remains','totalFee')">     
                                </div>
                            </div>
                        </td>
                        <td scope="col" width="33%" class="iEntry"> 
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('totalFee')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <input id="totalFee" name="totalFee" type="text" placeholder="<?=$this->lang->line('totalFee')?>" class="form-control iInput">     
                                </div>
                            </div>     
                        </td>
                    </tr>
              </table>
              <table class="table btm6px"> 
                    <tr>
                        <td scope="col" width="30%" class="iEntry alert alert-success" style="vertical-align:middle;text-align:center;font-size:28px;background-color:#d6e9c6">
                            <label class="" for="textinput"><?=$this->lang->line('teeth_no')?></label>     
                        </td>
                        <td scope="col" width="70%" class="iEntry alert-success" colspan="2">
                            <?=$teeth_add?>
                        </td>
                    </tr>
              </table>
              <table class="table btm6px"> 
                    <!-- </thead> -->
                    <tr>
                        <td scope="col" width="100%%" class="iEntry" colspan="3">
                            <input type="submit" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('save')?>">
                            <input type="button" onclick="bring_page('<?=base_url()?>index.php/xray/home/listRecords','')" class="btn btn-danger" value="<?=$this->lang->line('cancel')?>" >
                            <input type="reset"  id="singlebutton" name="singlebutton" class="btn btn-default" value="<?=$this->lang->line('clean')?>">
                        </td>
                    </tr>
              </table>  
            </div>
        </form>
    </div>
</div>