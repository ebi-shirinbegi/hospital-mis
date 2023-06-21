<?php 
if($teeth_record){
    foreach($teeth_record as $row){
        if($row->ill_type == 4){
?>
<div class="inputfield" id="clean">
    <div class="inputfield">
        <div class="textfield btm20padding" style = "background: #ebccd1;padding:3px 8px 5px;">
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->jermgery == 1){echo "checked='checked'";} ?> disabled='disabled' >
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('jermgery')?> </span>
                </label>
            </div>
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->bleching == 1){echo "checked='checked'";} ?> disabled='disabled' >
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('bleching')?> </span>
                </label>
            </div>
        </div>
    </div>
</div>

<?php
        }
    }
}
?>