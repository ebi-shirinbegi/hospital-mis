<div class="row iRow">
    <div class="dashTitle">
        <?=$title;?>
    </div>
    <div class="icontent">
        <!-- <form class="form-horizontal"> -->
        <?php 
            $attributes = array('class' => 'form-horizontal', 'id' => 'dr_add');
            echo form_open_multipart('expenses/home/add', $attributes);
        ?>
            <div class="table-responsive text-nowrap"> 
                  <table class="table btm6px">
                    <tr>
                        <td scope="col" width="33%" class="iEntry" colspan="3">
                            <span class="btn btn-success ino">1</span><input type="button" class="btn btn-success" value="+" onclick="addMultiple('<?=base_url()?>index.php/expenses/home/multiple','scopdiv',0)">
                        </td>
                    </tr>
                    <tr id="tardivid"> 
                        <td scope="col" width="33%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('name')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <input id="name[]" name="name[]" type="text" placeholder="<?=$this->lang->line('name')?>" class="form-control iInput" >     
                                </div>
                            </div>
                        </td>
                        <td scope="col" width="33%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('the_amount')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                      <input id="amount[]" name="amount[]" type="text" placeholder="<?=$this->lang->line('the_amount')?>" class="form-control iInput" onkeyup="totalThePrice('buy_price','amount','total')"> 
                                </div>
                            </div>    
                        </td>
                        <td scope="col" width="34%" class="iEntry">
                            <div class="inputfield">
                                <div class="rLabel">
                                    <label class="" for="textinput"><?=$this->lang->line('item_price')?> : </label>                
                                </div>
                                <div class="textfield btm20padding">
                                    <input id="price[]" name="price[]" type="text" placeholder="<?=$this->lang->line('item_price')?>" class="form-control iInput" >     
                                </div>
                            </div>    
                        </td>
                    </tr>
                </table>
                <table class="table btm6px" id="scopdiv"> 
                </table
                <table class="table btm6px"> 
                    <!-- </thead> -->
                    <tr>
                        <td scope="col" width="100%%" class="iEntry" colspan="3">
                            <input type="submit" id="singlebutton" name="singlebutton" class="btn btn-success" value="<?=$this->lang->line('save')?>">
                            <input type="button" onclick="bring_page('<?=base_url()?>index.php/expenses/home/listRecords','')" class="btn btn-danger" value="<?=$this->lang->line('cancel')?>" >
                            <input type="reset"  id="singlebutton" name="singlebutton" class="btn btn-default" value="<?=$this->lang->line('clean')?>">
                        </td>
                    </tr>
                </table>

            </div>
        </form>
    </div>
</div>