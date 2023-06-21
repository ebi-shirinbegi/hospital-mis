<div class="row iRow">
    <div class="dashTitle">
        <?=$title;?>
    </div>
    <div class="icontent">
        <!-- <form class="form-horizontal"> -->
        <?php 
            $attributes = array('class' => 'form-horizontal', 'id' => 'q_add');
            echo form_open_multipart('queue/home/add', $attributes);
        ?>
            <div class="table-responsive text-nowrap">

                  <table class="table">
                    <!-- <thead> -->
                    <tr>
                        <input type="hidden" name="parent_urn" value="<?=$parent_urn?>">
                      <td scope="col" width="20%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('queue_no')?></label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <select id="queue_no" name="queue_no" class="form-control nopadding" onchange="checkNumber('<?=base_url()?>index.php/queue/home/checkNumber','queue_no','msg_area')">
                                        <option value="1"><?=$this->lang->line('select')?></option>
                                        <?=$queue_no?>
                                    </select>
                                    <div id="msg_area"></div>
                                </div>
                            </div>    
                        </td>
                        <td scope="col" width="40%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('name')?></label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <input id="name" name="name" type="text" placeholder="<?=$this->lang->line('name')?>" class="form-control iInput">     
                                </div>
                            </div>
                        </td>
                        <td scope="col" width="40%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('f_name')?></label>                
                                </div>
                                <div class="textfield btm20padding">
                                      <input id="f_name" name="f_name" type="text" placeholder="<?=$this->lang->line('f_name')?>" class="form-control iInput"> 
                                </div>
                            </div>    
                        </td>
                    </tr>

                    <!-- </thead> -->
                    <tr>
                        <td scope="col" width="100%%" class="iEntry" colspan="3">
                            <input type="submit" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('save')?>">
                            <input type="button" onclick="bring_page('<?=base_url()?>index.php/queue/home/listRecords','')" class="btn btn-danger" value="<?=$this->lang->line('cancel')?>" >
                            <input type="reset"  id="singlebutton" name="singlebutton" class="btn btn-default" value="<?=$this->lang->line('clean')?>">
                        </td>
                    </tr>
                  </table>

                </div>
        </form>
    </div>
</div>

<script language="javascript">
    function readURL(input,imgid){
        if(input.files && input.files[0]){
            var reader = new FileReader();
            reader.onload = function(e){
                $('#'+imgid).attr('src',e.target.result).height(178);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>