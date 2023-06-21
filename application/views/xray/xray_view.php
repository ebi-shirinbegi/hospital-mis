<div class="row iRow">
    <div class="dashTitle">
        <?=$title;?>
    </div>
    <div class="icontent">
        <!-- <form class="form-horizontal"> -->
        <div class="table-responsive text-wrap"> 
          <?php if($record){?>  
          <table class="table btm6px">
            <tr>
                <td scope="col" width="50%" class="iEntry" colspan="1">
                    <div class="inputfield">
                        <div class="rLabel">
                            <label class="" for="textinput"><?=$this->lang->line('serial_no')?> : </label>
                        </div>
                        <div class="textfield btm20padding">
                            <?=$record->patient_id;?>
                        </div>
                    </div>
                </td>
                <td scope="col" width="50%" class="iEntry" colspan="2">
                    <div class="rLabel">
                        <label class="" for="textinput"><?=$this->lang->line('patient_name')?> : </label>                
                    </div>
                    <div class="textfield btm20padding">
                        <?=$record->name;?>
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
                                <?=$record->fee;?> 
                            </div>
                        </div>  
                    </td>
                    <td scope="col" width="33%" class="iEntry"> 
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput"><?=$this->lang->line('remains')?> : </label>                
                            </div>
                            <div class="textfield btm20padding">
                                <?=$record->remains;?>     
                            </div>
                        </div>
                    </td>
                    <td scope="col" width="33%" class="iEntry"> 
                        <div class="inputfield">
                            <div class="rLabel">
                                <label class="" for="textinput"><?=$this->lang->line('totalFee')?> : </label>                
                            </div>
                            <div class="textfield btm20padding">
                                <?=$record->fee+$record->remains;?>     
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
                    <td scope="col" width="70%" class="iEntry alert-success" colspan="2" style="vertical-align:middle">
                        <?php
                        if($view){
                            echo $teeth_view;
                        }else{
                            echo "<p class='alert alert-danger' style='margin:0px;'>".$this->lang->line('not_teeth_selected')."</p>"; 
                        }
                        ?>
                    </td>
                </tr>
          </table>
          <?php } ?>  
          <table class="table btm6px"> 
                <!-- </thead> -->
                <tr>
                    <td scope="col" width="100%%" class="iEntry" colspan="3">
                        <input type="submit" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('edit')?>" onclick="javascript:bring_page('<?=base_url()?>index.php/xray/home/edit/<?=$this->clean_encrypt->encode($record->urn);?>','')">   
                        <input type="button" onclick="bring_page('<?=base_url()?>index.php/xray/home/listRecords','')" class="btn btn-danger" value="<?=$this->lang->line('backToList')?>" >
                    </td>
                </tr>
          </table>  
        </div>
    </div>
</div>