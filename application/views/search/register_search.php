<!-- <form class="form-horizontal"> -->
<?php 
    $attributes = array('class' => 'form-horizontal', 'id' => 'r_add');
    echo form_open_multipart('register/home/register_add', $attributes);
?>
    <div class="table-responsive text-nowrap"> 
          <table class="table">
            <!-- <thead> -->
              <tr>
                <td scope="col" width="33%" class="iEntry">
                    <div class="inputfield">
                        <div class="rLabel">
                            <label class="" for="textinput"><?=$this->lang->line('name')?> : </label>                
                        </div>
                        <div class="textfield btm20padding">
                            <input id="name" name="name" type="text" placeholder="<?=$this->lang->line('name')?>" class="form-control iInput">     
                        </div>
                    </div>
                </td>
                <td scope="col" width="33%" class="iEntry">
                    <div class="inputfield">
                        <div class="rLabel">
                            <label class="" for="textinput"><?=$this->lang->line('f_name')?> : </label>                
                        </div>
                        <div class="textfield btm20padding">
                              <input id="f_name" name="f_name" type="text" placeholder="<?=$this->lang->line('f_name')?>" class="form-control iInput"> 
                        </div>
                    </div>    
                </td>
                <td scope="col" width="33%" class="iEntry">
                    <div class="inputfield">
                        <div class="rLabel">
                            <label class="" for="textinput"><?=$this->lang->line('contact')?> : </label>                
                        </div>
                        <div class="textfield btm20padding">
                            <input id="contact" name="contact" type="text" placeholder="<?=$this->lang->line('contact')?>" class="form-control iInput">     
                        </div>
                    </div>    
                </td>
              </tr>
              <tr>
                <td scope="col" width="33%" class="iEntry">
                    <div class="inputfield">
                        <div class="rLabel">
                            <label class="" for="textinput"><?=$this->lang->line('visit')?> : </label>                
                        </div>
                        <div class="textfield btm20padding">
                              <select id="visit" name="visit" class="form-control nopadding">
                                <option value="0"><?=$this->lang->line('select')?></option>
                                <option value="1">اول</option>
                                <option value="2">دوم</option>
                                <option value="3">سوم</option>
                                <option value="4">جهارم</option>
                                <option value="5">پنجم</option>
                                <option value="6">ششم</option>
                                <option value="7">هفتم</option>
                                <option value="8">هشتم</option>
                                <option value="9">نهم</option>
                                <option value="10">دهم</option>
                                <option value="11">يازدهم</option>
                                <option value="12">دوازدهم</option>
                                <option value="13">سيزدهم</option>
                                <option value="14">چهاردهم</option>
                                <option value="15">پانزدهم</option>
                            </select>
                        </div>
                    </div>    
                </td>
                <td scope="col" width="33%" class="iEntry">
                    <div class="inputfield">
                        <div class="rLabel">
                            <label class="" for="textinput"><?=$this->lang->line('fee')?> : </label>                
                        </div>
                        <div class="textfield btm20padding">
                            <input id="fee" name="fee" type="text" placeholder="<?=$this->lang->line('fee')?>" class="form-control iInput"> 
                        </div>
                    </div>  
                </td>
                <td scope="col" width="33%" class="iEntry">
                    <div class="inputfield">
                        <div class="rLabel">
                            <label class="" for="textinput"><?=$this->lang->line('remains')?> : </label>                
                        </div>
                        <div class="textfield btm20padding">
                            <input id="remains" name="remains" type="text" placeholder="<?=$this->lang->line('remains')?>" class="form-control iInput">     
                        </div>
                    </div> 
                </td>
              </tr>
              <tr>
                <td scope="col" width="33%" class="iEntry">
                    <div class="inputfield">
                        <div class="rLabel">
                            <label class="" for="textinput"><?=$this->lang->line('addrass')?> : </label>                
                        </div>
                        <div class="textfield btm10padding">
                            <textarea class="form-control stikynote" id="addrass" name="addrass" rows="1"></textarea>
                        </div>
                    </div>
                </td>
                <td scope="col" width="33%" class="iEntry" colspan="2">
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
                                            ?>
                                            <option value="<?=$doc->urn?>"><?=$doc->name?></option> 
                                            <?php
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </td>
              </tr>
              </tr>
                <td scope="col" width="33%" class="iEntry" colspan="3">
                    <div class="inputfield">
                        <div class="rLabel">
                            <label class="" for="textinput"><?=$this->lang->line('next_visit_date')?> : </label>
                        </div>
                        <div class="textfield btm20padding">
                              <select id="day" name="day" class="form-control nopadding inline" style="width:80px">
                                    <option value="1"><?=$this->lang->line('day')?> </option>
                                    <?=$days?>
                              </select>
                              <select id="month" name="month" class="form-control nopadding inline" style="width:80px">
                                    <option value="1"><?=$this->lang->line('month')?> </option>
                                    <?=$months?>
                              </select>
                              <select id="year" name="year" class="form-control nopadding inline" style="width:80px">
                                    <option value="1"><?=$this->lang->line('year')?></option>
                                    <?=$years?>
                              </select>
                        </div>
                    </div>
                    <div class="inputfield">
                        <div class="rLabel">
                            <label class="" for="textinput"><?=$this->lang->line('next_visit_time')?> : </label>
                        </div>
                        <div class="textfield btm20padding">
                              <select id="minute" name="minute" class="form-control nopadding inline" style="width:80px">
                                    <option value="1"><?=$this->lang->line('minute')?> </option>
                                    <option value="0">00</option>
                                    <?=$minute?>
                              </select>
                              <select id="hour" name="hour" class="form-control nopadding inline" style="width:80px">
                                    <option value="1"><?=$this->lang->line('hour')?> </option>
                                    <option value="0">00</option>
                                    <?=$hour?>
                              </select>
                        </div>
                    </div>
                </td>
              </tr>
              </tr>
        </table>
        <table class="table" id="scopdiv"> 
        </table
        <table class="table"> 
            <!-- </thead> -->
            <tr>
                <td scope="col" width="100%%" class="iEntry" colspan="3">
                    <input type="submit" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('save')?>">
                    <input type="button" onclick="bring_page('<?=base_url()?>index.php/register/home/register_list','')" class="btn btn-danger" value="<?=$this->lang->line('cancel')?>" >
                    <input type="reset"  id="singlebutton" name="singlebutton" class="btn btn-default" value="<?=$this->lang->line('clean')?>">
                </td>
            </tr>
        </table>

    </div>
</form>