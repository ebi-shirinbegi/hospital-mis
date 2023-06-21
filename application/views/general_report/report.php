<!-- BEGIN CONTENT -->   
<div class="row iRow">
    <div class="dashTitle" style="margin-bottom:10px;">
        <?=$title;?>
    </div>    
    <!-- <form class="form-horizontal"> -->
    <?php 
        $attributes = array('class' => 'form-horizontal', 'id' => 'filter');
        echo form_open_multipart('register/home/filter', $attributes);
    ?>
    <table class="table">
        <!-- <thead> -->
        <?php
            if($this->session->flashdata('msg')){
                echo $this->session->flashdata('msg');        
            }
        ?>
        <tr>
            <td scope="col" width="33%" class="iEntry" style="display:none">
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
                          <label class="" for="textinput"><?=$this->lang->line('to')?> &nbsp;&nbsp;: </label>
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
            <td scope="col" width="100%" class="iEntry">
                <div class="inputfield">
                    <div class="rLabel">
                        <label class="" for="textinput"><?=$this->lang->line('serial_no')?> : </label>
                    </div>
                    <div class="textfield btm20padding"> 
                          <select id="patient_id" name="patient_id" class="form-control nopadding chosen-select" onchange="bringPatientName('<?=base_url()?>index.php/xray/home/patientNameById','p_id','name')" style="width:300px">
                            <option value="0"><?=$this->lang->line('select')?></option>
                            <?php
                            if($patientid){
                                foreach($patientid as $p_id){
                                    ?>
                                    <option value="<?=$p_id->patient_id?>"><?=$p_id->patient_id?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </td>
        </tr>
        <!-- </thead> -->
        <tr>
            <td scope="col" width="100%%" class="iEntry" colspan="2">                                           
                <?php if($this->amc_auth->check_myrole('report')){ ?>
                <input type="button" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('print_excel')?>" onclick="do_it2('<?=base_url()?>index.php/register/home/generalReport/1','filter');">
                <?php } ?>
            </td>
        </tr>
    </table>
    <?=form_close()?>
</div>