<?php 
if($teeth_record){
    foreach($teeth_record as $row){
        if($row->ill_type == 5){
?>
<div class="inputfield" id="orthodan">
    <div class="inputfield">
        <div class="textfield btm20padding" style = "background: #faebcc;padding:3px 8px 5px;">
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->top_teeth == 1){echo "checked='checked'";} ?> disabled='disabled' >
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('top_teeth')?> </span>
                </label>
            </div>
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->bottom_teeth == 1){echo "checked='checked'";} ?> disabled='disabled' >
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('bottom_teeth')?> </span>
                </label>
            </div>
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->all_teeth == 1){echo "checked='checked'";} ?> disabled='disabled' >
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('complete')?> </span>
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